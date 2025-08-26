<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Libraries\DynamicTableManager;

class SimanApi extends Controller
{
    protected $db;
    protected $apiKey = 'c877acaa0de297a9e3b8bbdb101dd254d33a92a0444b979d599e04fdeaccdbc5';
    protected $baseUrl = 'https://apigw.pu.go.id/v1/siman';
    protected $tableName = 'siman_api_data';

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->db = \Config\Database::connect();
    }

    public function testConnection()
    {
        try {
            $url = $this->baseUrl . '/jumlah-data?api_key=' . $this->apiKey;
            $client = \Config\Services::curlrequest();
            $response = $client->request('GET', $url, ['timeout' => 30, 'verify' => false]);
            
            if ($response->getStatusCode() === 200) {
                $data = json_decode($response->getBody(), true);
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Connection successful',
                    'data' => array_slice($data, 0, 3)
                ]);
            }
            
            return $this->response->setJSON(['status' => 'error', 'message' => 'Connection failed']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function syncAllData()
    {
        $endpoints = [
            'tanah-sigi', 'rumah-negara', 'pm-tik', 'pm-non-tik', 'persediaan',
            'jumlah-data', 'jalan-jembatan', 'instalasi-jaringan', 'gedung-bangunan',
            'bangunan-air', 'aset-tetap-lainnya', 'aset-tak-berwujud', 'alat-berat',
            'alat-angkutan', 'tanah'
        ];
        
        $results = [];
        
        foreach ($endpoints as $endpoint) {
            try {
                // Clear existing data
                $this->db->table($this->tableName)->where('kategori_api', $endpoint)->delete();
                
                // Fetch new data
                $url = $this->baseUrl . '/' . $endpoint . '?api_key=' . $this->apiKey;
                $client = \Config\Services::curlrequest();
                $response = $client->request('GET', $url, ['timeout' => 60, 'verify' => false]);
                
                if ($response->getStatusCode() === 200) {
                    $data = json_decode($response->getBody(), true);
                    $apiData = $data['resource'] ?? $data['data'] ?? $data;
                    
                    if (is_array($apiData)) {
                        $inserted = 0;
                        foreach ($apiData as $item) {
                            $this->db->table($this->tableName)->insert([
                                'kategori_api' => $endpoint,
                                'data_json' => json_encode($item),
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s')
                            ]);
                            $inserted++;
                        }
                        $results[$endpoint] = ['status' => 'success', 'count' => $inserted];
                    }
                } else {
                    $results[$endpoint] = ['status' => 'error', 'message' => 'API error'];
                }
                
                sleep(1); // Rate limiting
                
            } catch (\Exception $e) {
                $results[$endpoint] = ['status' => 'error', 'message' => $e->getMessage()];
            }
        }
        
        return $this->response->setJSON([
            'status' => 'completed',
            'results' => $results
        ]);
    }

    public function getStatistics()
    {
        try {
            $builder = $this->db->table($this->tableName);
            $totalData = $builder->countAll();
            
            $kategoris = $builder->select('kategori_api, COUNT(*) as total')
                               ->groupBy('kategori_api')
                               ->get()
                               ->getResultArray();
            
            return $this->response->setJSON([
                'status' => 'success',
                'total_data' => $totalData,
                'data_per_kategori' => $kategoris
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function createColumnsForAllCategoriesSafe()
    {
        try {
            $dynamicManager = new DynamicTableManager($this->tableName);
            $endpoints = ['tanah-sigi', 'rumah-negara', 'pm-tik', 'pm-non-tik', 'persediaan'];
            $addedColumns = [];
            $skippedColumns = [];
            
            // Get existing columns first
            $existingColumns = array_map('strtolower', $this->db->getFieldNames($this->tableName));
            
            foreach ($endpoints as $kategori) {
                try {
                    $fieldAnalysis = $dynamicManager->analyzeJsonStructure($kategori, 50);
                    
                    foreach ($fieldAnalysis as $fieldName => $analysis) {
                        $columnName = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '_', $fieldName));
                        
                        // Only create if column doesn't exist
                        if (!in_array($columnName, $existingColumns)) {
                            try {
                                $this->db->query("ALTER TABLE {$this->tableName} ADD COLUMN {$columnName} VARCHAR(500) NULL");
                                $addedColumns[] = $columnName;
                                $existingColumns[] = $columnName; // Add to list to prevent duplicates
                            } catch (\Exception $e) {
                                $skippedColumns[] = [
                                    'column' => $columnName,
                                    'error' => 'Already exists or creation failed'
                                ];
                            }
                        } else {
                            $skippedColumns[] = [
                                'column' => $columnName,
                                'error' => 'Column already exists'
                            ];
                        }
                    }
                } catch (\Exception $e) {
                    // Skip kategori if analysis fails
                }
            }
            
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Column creation completed',
                'new_columns_added' => count($addedColumns),
                'columns_skipped' => count($skippedColumns),
                'added_columns' => $addedColumns,
                'skipped_columns' => array_slice($skippedColumns, 0, 10) // Show first 10 skipped
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function skipColumnCreation()
    {
        // Just return success without creating any columns
        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Column creation skipped - using existing columns',
            'new_columns_added' => 0
        ]);
    }

    public function extractAllDataSafe($kategori = null, $limit = 20000)
    {
        try {
            $builder = $this->db->table($this->tableName);
            if ($kategori) {
                $builder->where('kategori_api', $kategori);
            }
            $builder->limit($limit);
            $records = $builder->get()->getResultArray();
            
            $existingColumns = $this->db->getFieldNames($this->tableName);
            $systemColumns = ['id', 'kategori_api', 'data_json', 'created_at', 'updated_at'];
            $dynamicColumns = array_diff($existingColumns, $systemColumns);
            
            $updatedCount = 0;
            $fieldMapping = [
                'alamat' => 'alamat',
                'kd_brg' => 'kd_brg',
                'no_aset' => 'no_aset',
                'latitude' => 'latitude',
                'kd_satker' => 'kd_satker',
                'nama_barang' => 'nama_barang',
                'nama_satker' => 'nama_satker',
                'gps_longitude' => 'gps_longitude',
                'status_penggunaan' => 'status_penggunaan'
            ];
            
            foreach ($records as $record) {
                $jsonData = json_decode($record['data_json'], true);
                
                if ($jsonData && is_array($jsonData)) {
                    $updateData = [];
                    
                    foreach ($dynamicColumns as $columnName) {
                        $fieldName = $fieldMapping[$columnName] ?? $columnName;
                        
                        if (isset($jsonData[$fieldName])) {
                            $value = $jsonData[$fieldName];
                            
                            // Safe value handling
                            if (is_string($value)) {
                                $updateData[$columnName] = substr(trim($value), 0, 450); // Truncate to safe length
                            } elseif (is_numeric($value)) {
                                $updateData[$columnName] = (string)$value;
                            } else {
                                $updateData[$columnName] = substr(json_encode($value), 0, 450);
                            }
                        }
                    }
                    
                    if (!empty($updateData)) {
                        $updateData['updated_at'] = date('Y-m-d H:i:s');
                        $this->db->table($this->tableName)->where('id', $record['id'])->update($updateData);
                        $updatedCount++;
                    }
                }
            }
            
            return $this->response->setJSON([
                'status' => 'success',
                'message' => "Extracted {$updatedCount} records",
                'updated_count' => $updatedCount,
                'total_processed' => count($records)
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function fixColumnSizes()
    {
        try {
            $fields = $this->db->getFieldData($this->tableName);
            $fixedColumns = [];
            
            foreach ($fields as $field) {
                if (!in_array($field->name, ['id', 'kategori_api', 'data_json', 'created_at', 'updated_at'])) {
                    if (isset($field->max_length) && $field->max_length < 500) {
                        try {
                            $this->db->query("ALTER TABLE {$this->tableName} ALTER COLUMN {$field->name} TYPE VARCHAR(500)");
                            $fixedColumns[] = $field->name;
                        } catch (\Exception $e) {
                            // Skip if error
                        }
                    }
                }
            }
            
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Fixed ' . count($fixedColumns) . ' columns',
                'fixed_columns' => $fixedColumns
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function autoSyncWithDynamicColumns($kategori = null)
    {
        try {
            // Step 1: Sync
            if ($kategori) {
                $syncResult = $this->syncByCategory($kategori);
            } else {
                $syncResult = $this->syncAllData();
            }
            
            // Step 2: Create columns
            $this->createColumnsForAllCategoriesSafe();
            
            // Step 3: Extract
            $this->extractAllDataSafe($kategori);
            
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Auto-sync completed'
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function syncByCategory($kategori = null)
    {
        if (!$kategori) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Category required']);
        }
        
        try {
            // Clear existing data
            $this->db->table($this->tableName)->where('kategori_api', $kategori)->delete();
            
            // Fetch new data
            $url = $this->baseUrl . '/' . $kategori . '?api_key=' . $this->apiKey;
            $client = \Config\Services::curlrequest();
            $response = $client->request('GET', $url, ['timeout' => 60, 'verify' => false]);
            
            if ($response->getStatusCode() === 200) {
                $data = json_decode($response->getBody(), true);
                $apiData = $data['resource'] ?? $data['data'] ?? $data;
                
                if (is_array($apiData)) {
                    $inserted = 0;
                    foreach ($apiData as $item) {
                        $this->db->table($this->tableName)->insert([
                            'kategori_api' => $kategori,
                            'data_json' => json_encode($item),
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                        $inserted++;
                    }
                    
                    return $this->response->setJSON([
                        'status' => 'success',
                        'message' => "Synced {$inserted} records",
                        'count' => $inserted
                    ]);
                }
            }
            
            return $this->response->setJSON(['status' => 'error', 'message' => 'No data found']);
            
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function getDatabaseSchema()
    {
        try {
            $fields = $this->db->getFieldData($this->tableName);
            return $this->response->setJSON([
                'status' => 'success',
                'table_name' => $this->tableName,
                'total_columns' => count($fields),
                'columns' => $fields
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function checkExistingColumns()
    {
        try {
            $existingColumns = $this->db->getFieldNames($this->tableName);
            $systemColumns = ['id', 'kategori_api', 'data_json', 'created_at', 'updated_at'];
            $dynamicColumns = array_diff($existingColumns, $systemColumns);
            
            return $this->response->setJSON([
                'status' => 'success',
                'total_columns' => count($existingColumns),
                'system_columns' => $systemColumns,
                'dynamic_columns' => $dynamicColumns,
                'dynamic_columns_count' => count($dynamicColumns)
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}