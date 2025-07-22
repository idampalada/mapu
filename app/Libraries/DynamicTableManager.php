<?php

namespace App\Libraries;

/**
 * Dynamic Table Manager - FIXED VERSION
 * Handles automatic column creation based on JSON structure analysis
 */
class DynamicTableManager
{
    protected $db;
    protected $forge;
    protected $tableName;
    
    public function __construct($tableName = 'siman_api_data')
    {
        $this->db = \Config\Database::connect();
        $this->forge = \Config\Database::forge();
        $this->tableName = $tableName;
    }

    /**
     * Analyze JSON structure dari sample data - FIXED VERSION
     * Handle both array dan object structures
     */
    public function analyzeJsonStructure($kategori = null, $sampleSize = 100)
{
    $builder = $this->db->table($this->tableName);
    
    if ($kategori) {
        $builder->where('kategori_api', $kategori);
    }
    
    $builder->limit($sampleSize);
    $records = $builder->get()->getResultArray();
    
    $fieldAnalysis = [];
    
    foreach ($records as $record) {
        $rawJson = $record['data_json'];
        
        // Debug log
        log_message('debug', "Processing JSON for record {$record['id']}: " . substr($rawJson, 0, 200));
        
        $jsonData = json_decode($rawJson, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            log_message('error', "JSON decode error for record {$record['id']}: " . json_last_error_msg());
            continue;
        }
        
        if (!$jsonData) {
            log_message('warning', "Empty JSON data for record {$record['id']}");
            continue;
        }
        
        // HANDLE DIFFERENT STRUCTURES BASED ON DEBUG RESULTS
        $dataToAnalyze = [];
        
        // Case 1: Data has 'resource' key (like from your previous sync)
        if (isset($jsonData['resource']) && is_array($jsonData['resource'])) {
            $dataToAnalyze = $jsonData['resource'];
            log_message('debug', "Found 'resource' key with " . count($dataToAnalyze) . " items");
        }
        // Case 2: Direct array of objects
        elseif (is_array($jsonData) && isset($jsonData[0]) && is_array($jsonData[0])) {
            $dataToAnalyze = $jsonData;
            log_message('debug', "Found direct array with " . count($dataToAnalyze) . " items");
        }
        // Case 3: Single object
        elseif (is_array($jsonData)) {
            $dataToAnalyze = [$jsonData];
            log_message('debug', "Found single object");
        }
        
        // Analyze each item
        foreach ($dataToAnalyze as $item) {
            if (is_array($item)) {
                $this->analyzeObjectFields($item, $fieldAnalysis);
            }
        }
    }
    
    log_message('info', "Analysis completed. Found " . count($fieldAnalysis) . " unique fields");
    
    return $fieldAnalysis;
}
    /**
     * Analyze fields dari single object
     */
    private function analyzeObjectFields($item, &$fieldAnalysis)
    {
        foreach ($item as $key => $value) {
            // Skip numeric keys atau keys yang tidak bermakna
            if (is_numeric($key)) {
                continue;
            }
            
            // Skip keys yang terlalu pendek atau aneh
            if (strlen($key) < 2) {
                continue;
            }
            
            if (!isset($fieldAnalysis[$key])) {
                $fieldAnalysis[$key] = [
                    'count' => 0,
                    'types' => [],
                    'max_length' => 0,
                    'sample_values' => [],
                    'null_count' => 0,
                    'numeric_count' => 0,
                    'date_count' => 0
                ];
            }
            
            $analysis = &$fieldAnalysis[$key];
            $analysis['count']++;
            
            if ($value === null || $value === '') {
                $analysis['null_count']++;
                continue;
            }
            
            // Type analysis
            $type = gettype($value);
            $analysis['types'][$type] = ($analysis['types'][$type] ?? 0) + 1;
            
            // Length analysis untuk string
            if (is_string($value)) {
                $analysis['max_length'] = max($analysis['max_length'], strlen($value));
            }
            
            // Numeric detection
            if (is_numeric($value)) {
                $analysis['numeric_count']++;
            }
            
            // Date detection
            if ($this->isDateValue($value)) {
                $analysis['date_count']++;
            }
            
            // Sample values (limit to 5)
            if (count($analysis['sample_values']) < 5) {
                $analysis['sample_values'][] = $value;
            }
        }
    }

    /**
     * Auto-generate column definition berdasarkan analisis
     */
    public function generateColumnDefinitions($fieldAnalysis)
    {
        $columnDefinitions = [];
        
        foreach ($fieldAnalysis as $fieldName => $analysis) {
            // Skip jika field terlalu jarang muncul (< 10% sample)
            $totalSamples = $analysis['count'] + $analysis['null_count'];
            if ($totalSamples === 0) {
                continue;
            }
            
            $frequency = $analysis['count'] / $totalSamples;
            if ($frequency < 0.1) {
                continue;
            }
            
            $columnDef = $this->determineColumnType($fieldName, $analysis);
            
            if ($columnDef) {
                $columnDefinitions[$fieldName] = $columnDef;
            }
        }
        
        return $columnDefinitions;
    }

    /**
     * Determine column type berdasarkan analisis data
     */
    private function determineColumnType($fieldName, $analysis)
{
    $totalNonNull = $analysis['count'] - $analysis['null_count'];
    
    if ($totalNonNull === 0) {
        return null;
    }
    
    // Date detection
    if ($analysis['date_count'] / $totalNonNull > 0.8) {
        return [
            'type' => 'DATE',
            'null' => true,
            'comment' => "Auto-generated date field: {$fieldName}"
        ];
    }
    
    // Numeric detection - FIXED untuk PostgreSQL
    if ($analysis['numeric_count'] / $totalNonNull > 0.8) {
        $hasDecimals = false;
        $maxValue = 0;
        
        foreach ($analysis['sample_values'] as $value) {
            if (is_numeric($value)) {
                $numValue = (float) $value;
                $maxValue = max($maxValue, abs($numValue));
                
                if (strpos($value, '.') !== false) {
                    $hasDecimals = true;
                }
            }
        }
        
        // Jika nilai sangat besar atau ada decimal, gunakan DECIMAL
        if ($hasDecimals || $maxValue > 2147483647 || $this->isFinancialField($fieldName)) {
            return [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => true,
                'comment' => "Auto-generated decimal field: {$fieldName}"
            ];
        } else {
            // Gunakan BIGINT untuk integer besar
            return [
                'type' => 'BIGINT',
                'null' => true,
                'comment' => "Auto-generated bigint field: {$fieldName}"
            ];
        }
    }
    
    // String detection (tidak berubah)
    if (isset($analysis['types']['string'])) {
        $maxLength = $analysis['max_length'];
        
        if ($maxLength <= 10) {
            $constraint = 20;
        } elseif ($maxLength <= 50) {
            $constraint = 100;
        } elseif ($maxLength <= 100) {
            $constraint = 150;
        } elseif ($maxLength <= 255) {
            $constraint = 255;
        } else {
            return [
                'type' => 'TEXT',
                'null' => true,
                'comment' => "Auto-generated text field: {$fieldName}"
            ];
        }
        
        return [
            'type' => 'VARCHAR',
            'constraint' => $constraint,
            'null' => true,
            'comment' => "Auto-generated varchar field: {$fieldName}"
        ];
    }
    
    return [
        'type' => 'VARCHAR',
        'constraint' => 255,
        'null' => true,
        'comment' => "Auto-generated default field: {$fieldName}"
    ];
}

    /**
     * Execute dynamic column creation
     */
    public function createDynamicColumns($kategori = null)
    {
        try {
            log_message('info', "Starting dynamic column analysis for kategori: " . ($kategori ?? 'ALL'));
            $fieldAnalysis = $this->analyzeJsonStructure($kategori, 500);
            
            $columnDefinitions = $this->generateColumnDefinitions($fieldAnalysis);
            $existingColumns = $this->getExistingColumns();
            
            $newColumns = [];
            foreach ($columnDefinitions as $fieldName => $definition) {
                $columnName = $this->sanitizeColumnName($fieldName);
                
                if (!in_array($columnName, $existingColumns)) {
                    $newColumns[$columnName] = $definition;
                }
            }
            
            $addedColumns = [];
            foreach ($newColumns as $columnName => $definition) {
                try {
                    $this->forge->addColumn($this->tableName, [
                        $columnName => $definition
                    ]);
                    $addedColumns[] = $columnName;
                    log_message('info', "Added dynamic column: {$columnName}");
                } catch (\Exception $e) {
                    log_message('error', "Failed to add column {$columnName}: " . $e->getMessage());
                }
            }
            
            $this->createDynamicIndexes($addedColumns);
            
            return [
                'success' => true,
                'analyzed_fields' => count($fieldAnalysis),
                'generated_definitions' => count($columnDefinitions),
                'existing_columns' => count($existingColumns),
                'new_columns_added' => count($addedColumns),
                'added_columns' => $addedColumns
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Enhanced dynamic field extraction - Handle array structures
     */
    public function dynamicFieldExtraction($jsonData, &$dataToInsert)
    {
        $existingColumns = $this->getExistingColumns();
        
        // Handle different JSON structures
        $fieldsToProcess = [];
        
        if (is_array($jsonData)) {
            // Jika array of objects, ambil object pertama sebagai template
            if (isset($jsonData[0]) && is_array($jsonData[0])) {
                $fieldsToProcess = $jsonData[0];
            } 
            // Jika single object
            else {
                $fieldsToProcess = $jsonData;
            }
        }
        
        foreach ($fieldsToProcess as $jsonKey => $value) {
            // Skip numeric keys
            if (is_numeric($jsonKey)) {
                continue;
            }
            
            if ($value === null || $value === '') {
                continue;
            }
            
            $columnName = $this->sanitizeColumnName($jsonKey);
            
            if (!in_array($columnName, $existingColumns)) {
                continue;
            }
            
            $processedValue = $this->processValueDynamically($jsonKey, $value);
            
            if ($processedValue !== null) {
                $dataToInsert[$columnName] = $processedValue;
            }
        }
    }

    // Helper methods
    private function isDateValue($value)
    {
        if (!is_string($value)) {
            return false;
        }
        
        $datePatterns = [
            '/^\d{4}-\d{2}-\d{2}$/',
            '/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/',
            '/^\d{2}\/\d{2}\/\d{4}$/',
        ];
        
        foreach ($datePatterns as $pattern) {
            if (preg_match($pattern, $value)) {
                return true;
            }
        }
        
        return false;
    }
    
    private function isFinancialField($fieldName)
    {
        $financialKeywords = ['nilai', 'harga', 'biaya', 'tarif', 'nominal', 'rupiah'];
        $lowerFieldName = strtolower($fieldName);
        
        foreach ($financialKeywords as $keyword) {
            if (strpos($lowerFieldName, $keyword) !== false) {
                return true;
            }
        }
        
        return false;
    }
    
    private function sanitizeColumnName($fieldName)
    {
        $sanitized = strtolower($fieldName);
        $sanitized = preg_replace('/[^a-z0-9_]/', '_', $sanitized);
        $sanitized = preg_replace('/_+/', '_', $sanitized);
        $sanitized = trim($sanitized, '_');
        
        if (preg_match('/^\d/', $sanitized)) {
            $sanitized = 'field_' . $sanitized;
        }
        
        return $sanitized;
    }
    
    private function getExistingColumns()
    {
        $fields = $this->db->getFieldNames($this->tableName);
        return array_map('strtolower', $fields);
    }
    
    private function processValueDynamically($fieldName, $value)
    {
        if ($this->isDateValue($value)) {
            try {
                $date = new \DateTime($value);
                return $date->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }
        
        if (is_numeric($value)) {
            if ($this->isFinancialField($fieldName)) {
                return (float) $value;
            } else {
                return is_int($value + 0) ? (int) $value : (float) $value;
            }
        }
        
        if (is_string($value)) {
            $cleaned = trim($value);
            return $cleaned !== '' ? $cleaned : null;
        }
        
        return null;
    }
    
    private function createDynamicIndexes($columnNames)
    {
        foreach ($columnNames as $columnName) {
            try {
                $this->db->query("CREATE INDEX IF NOT EXISTS idx_dynamic_{$columnName} ON {$this->tableName} ({$columnName})");
            } catch (\Exception $e) {
                log_message('warning', "Failed to create index for {$columnName}: " . $e->getMessage());
            }
        }
    }
}