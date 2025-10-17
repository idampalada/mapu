<?php
namespace App\Models;
use CodeIgniter\Model;

class KomputerModel extends Model
{
    protected $table = 'komputer';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
    'tgl_tarik',
    'nama_kl',
    'nama_kpknl',
    'nama_satker',
    'kode_barang',
    'nama_barang',
    'nilai_perolehan',
    'nilai_penyusutan',
    'nilai_buku',
    'nup',
    'tanggal_perolehan',
    'kondisi',
    'merk',
    'kuantitas',
    'status_penggunaan',
    'jns_processor',
    'processor',
    'memori',
    'hardisk',
    'monitor',
    'spek_lain',
    'kelompok', // KOMPUTER UNIT, PERALATAN KOMPUTER
    'sub_kelompok',
    'created_at',
    'updated_at',
    // Field baru yang ditambahkan
    'bidang',
    'pengguna_sebelumnya',
    'pengguna_sekarang',
    'status_barang',
    'keterangan'
];
    
    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    // Validation Rules
    protected $validationRules = [
    'kode_barang' => 'required|max_length[100]',
    'nama_barang' => 'required|max_length[255]',
    'kelompok' => 'required|max_length[100]',
    'nup' => 'permit_empty|max_length[100]',
    'merk' => 'permit_empty|max_length[100]',
    'kondisi' => 'permit_empty|max_length[50]',
    'kuantitas' => 'permit_empty|integer',
    'status_penggunaan' => 'permit_empty|max_length[200]',
    'jns_processor' => 'permit_empty|max_length[100]',
    'processor' => 'permit_empty|max_length[100]',
    'memori' => 'permit_empty|max_length[100]',
    'hardisk' => 'permit_empty|max_length[100]',
    'monitor' => 'permit_empty|max_length[100]',
    'spek_lain' => 'permit_empty|max_length[500]',
    'nilai_perolehan' => 'permit_empty|decimal',
    'nilai_penyusutan' => 'permit_empty|decimal',
    'nilai_buku' => 'permit_empty|decimal',
    // Validasi untuk field baru
    'bidang' => 'permit_empty|max_length[200]',
    'pengguna_sebelumnya' => 'permit_empty|max_length[255]',
    'pengguna_sekarang' => 'permit_empty|max_length[255]',
    'status_barang' => 'permit_empty|max_length[100]',
    'keterangan' => 'permit_empty'
];
    
    protected $validationMessages = [
        'kode_barang' => [
            'required' => 'Kode barang harus diisi',
            'max_length' => 'Kode barang maksimal 100 karakter'
        ],
        'nama_barang' => [
            'required' => 'Nama barang harus diisi',
            'max_length' => 'Nama barang maksimal 255 karakter'
        ],
        'kelompok' => [
            'required' => 'Kelompok harus diisi',
            'max_length' => 'Kelompok maksimal 100 karakter'
        ],
        'kuantitas' => [
            'integer' => 'Kuantitas harus berupa angka'
        ],
        'nilai_perolehan' => [
            'decimal' => 'Nilai perolehan harus berupa angka'
        ],
        'nilai_penyusutan' => [
            'decimal' => 'Nilai penyusutan harus berupa angka'
        ],
        'nilai_buku' => [
            'decimal' => 'Nilai buku harus berupa angka'
        ]
    ];
    
    protected $skipValidation = false;
    protected $cleanValidationRules = true;
    
    
    // ============ CRUD DASAR ============
    
    // 1. CRUD DASAR - Get all data
    public function getAllData($limit = null, $offset = 0)
    {
        $builder = $this->builder();
        
        if ($limit) {
            $builder->limit($limit, $offset);
        }
        
        return $builder->orderBy('kode_barang', 'ASC')
                      ->get()
                      ->getResultArray();
    }
    
    // 2. CRUD DASAR - Find by ID
    public function findById($id)
    {
        $builder = $this->builder();
        return $builder->where('id', $id)
                      ->get()
                      ->getRowArray();
    }
    
    // 3. CRUD DASAR - Insert data
    public function insertData($data)
    {
        $builder = $this->builder();
        
        // Add timestamps
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        return $builder->insert($data);
    }
    
    // 4. CRUD DASAR - Update data
    public function updateData($id, $data)
    {
        $builder = $this->builder();
        
        // Add updated timestamp
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        return $builder->where('id', $id)
                      ->update($data);
    }
    
    // 5. CRUD DASAR - Delete data
    public function deleteData($id)
    {
        $builder = $this->builder();
        return $builder->where('id', $id)
                      ->delete();
    }
    
    // 6. CRUD DASAR - Count all records
    public function countAll()
    {
        $builder = $this->builder();
        return $builder->countAllResults();
    }
    
    // ============ BUSINESS LOGIC ============
    
    // 7. BUSINESS LOGIC - Find by kode barang
    public function findByKodeBarang($kodeBarang)
    {
        $builder = $this->builder();
        return $builder->where('kode_barang', $kodeBarang)
                      ->get()
                      ->getRowArray();
    }
    
    // 8. BUSINESS LOGIC - Search functionality dengan filter kelompok
    public function searchKomputer($searchTerm = '', $kelompok = '', $limit = 100, $offset = 0)
    {
        $builder = $this->builder();
        
        // Filter berdasarkan kelompok jika ada
        if (!empty($kelompok)) {
            $builder->where('UPPER(kelompok)', strtoupper($kelompok));
        }
        
        // Filter pencarian jika ada
        if (!empty($searchTerm)) {
            $searchTerm = $this->db->escapeLikeString($searchTerm);
            
            $builder->groupStart()
                ->like('kode_barang', $searchTerm)
                ->orLike('nama_barang', $searchTerm)
                ->orLike('merk', $searchTerm)
                ->orLike('processor', $searchTerm)
                ->orLike('memori', $searchTerm)
                ->orLike('hardisk', $searchTerm)
                ->orLike('monitor', $searchTerm)
                ->orLike('nup', $searchTerm)
                ->orLike('spek_lain', $searchTerm)
                ->groupEnd();
        }
        
        return $builder->orderBy('kode_barang', 'ASC')
                      ->limit($limit, $offset)
                      ->get()
                      ->getResultArray();
    }
    
    // 9. BUSINESS LOGIC - Get data by kelompok (untuk filter kategori)
    public function getByKelompok($kelompok, $limit = null, $offset = 0)
    {
        $builder = $this->builder();
        
        $builder->where('UPPER(kelompok)', strtoupper($kelompok));
        
        if ($limit) {
            $builder->limit($limit, $offset);
        }
        
        return $builder->orderBy('kode_barang', 'ASC')
                      ->get()
                      ->getResultArray();
    }
    
    // 10. BUSINESS LOGIC - Count by kelompok
    public function countByKelompok($kelompok)
    {
        $builder = $this->builder();
        return $builder->where('UPPER(kelompok)', strtoupper($kelompok))
                      ->countAllResults();
    }
    
    // 11. BUSINESS LOGIC - Get statistics per kelompok
    public function getStatistikKelompok()
    {
        $builder = $this->builder();
        
        return $builder->select('kelompok, COUNT(*) as jumlah')
                      ->groupBy('kelompok')
                      ->orderBy('kelompok', 'ASC')
                      ->get()
                      ->getResultArray();
    }
    
    // 12. BUSINESS LOGIC - Get statistics per kondisi
    public function getStatistikKondisi($kelompok = '')
    {
        $builder = $this->builder();
        
        if (!empty($kelompok)) {
            $builder->where('UPPER(kelompok)', strtoupper($kelompok));
        }
        
        return $builder->select('kondisi, COUNT(*) as jumlah')
                      ->groupBy('kondisi')
                      ->orderBy('kondisi', 'ASC')
                      ->get()
                      ->getResultArray();
    }
    
    // 13. BUSINESS LOGIC - Get data untuk dashboard (ringkasan per kelompok)
    public function getDashboardData()
    {
        $data = [];
        
        // Total semua data
        $data['total_all'] = $this->countAll();
        
        // Per kelompok
        $kelompokList = ['KOMPUTER UNIT', 'PERALATAN KOMPUTER'];
        
        foreach ($kelompokList as $kelompok) {
            $key = strtolower(str_replace(' ', '_', $kelompok));
            $data[$key] = $this->countByKelompok($kelompok);
        }
        
        // Statistik kondisi
        $data['kondisi_stats'] = $this->getStatistikKondisi();
        
        return $data;
    }
    
    // 14. BUSINESS LOGIC - Check if kode_barang exists (untuk validasi import)
    public function isKodeBarangExists($kodeBarang, $excludeId = null)
    {
        $builder = $this->builder();
        $builder->where('kode_barang', $kodeBarang);
        
        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }
        
        return $builder->countAllResults() > 0;
    }
    
    // 15. BUSINESS LOGIC - Bulk insert for import (dengan validasi)
    public function bulkInsert($dataArray)
    {
        if (empty($dataArray)) {
            return false;
        }
        
        $builder = $this->builder();
        
        // Add timestamps to all records
        foreach ($dataArray as &$data) {
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');
        }
        
        return $builder->insertBatch($dataArray);
    }
    
    // ============ VALIDATION METHODS (Pure PHP) ============
    
    // 16. Validation method - Pure PHP validation untuk import data
    public function validateImportData($data)
    {
        $errors = [];
        
        // Required fields
        $requiredFields = ['kode_barang', 'nama_barang', 'kelompok'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' tidak boleh kosong';
            }
        }
        
        // Length validation
        $lengthValidation = [
            'kode_barang' => 100,
            'nama_barang' => 255,
            'kelompok' => 100,
            'nup' => 100,
            'merk' => 100,
            'kondisi' => 50,
            'status_penggunaan' => 200,
            'processor' => 100,
            'memori' => 100,
            'hardisk' => 100,
            'monitor' => 100,
            'spek_lain' => 500
        ];
        
        foreach ($lengthValidation as $field => $maxLength) {
            if (isset($data[$field]) && strlen($data[$field]) > $maxLength) {
                $errors[] = ucfirst(str_replace('_', ' ', $field)) . " maksimal {$maxLength} karakter";
            }
        }
        
        // Kelompok validation (harus salah satu dari kelompok yang valid)
        $validKelompok = ['KOMPUTER UNIT', 'PERALATAN KOMPUTER'];
        if (isset($data['kelompok']) && !in_array(strtoupper($data['kelompok']), $validKelompok)) {
            $errors[] = 'Kelompok harus salah satu dari: ' . implode(', ', $validKelompok);
        }
        
        // Kondisi validation (jika ada)
        $validKondisi = ['BAIK', 'RUSAK RINGAN', 'RUSAK BERAT'];
        if (isset($data['kondisi']) && 
            !empty($data['kondisi']) && 
            !in_array(strtoupper($data['kondisi']), $validKondisi)) {
            $errors[] = 'Kondisi harus salah satu dari: ' . implode(', ', $validKondisi);
        }
        
        // Numeric validation
        $numericFields = ['kuantitas', 'nilai_perolehan', 'nilai_penyusutan', 'nilai_buku'];
        foreach ($numericFields as $field) {
            if (isset($data[$field]) && 
                !empty($data[$field]) && 
                !is_numeric($data[$field])) {
                $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' harus berupa angka';
            }
        }
        
        // Date validation
        if (isset($data['tanggal_perolehan']) && 
            !empty($data['tanggal_perolehan'])) {
            $date = \DateTime::createFromFormat('Y-m-d', $data['tanggal_perolehan']);
            if (!$date || $date->format('Y-m-d') !== $data['tanggal_perolehan']) {
                $errors[] = 'Format tanggal perolehan tidak valid (Y-m-d)';
            }
        }
        
        return $errors;
    }
    
    // 17. Validation method - Validate kelompok (untuk form input)
    public function validateKelompok($kelompok)
    {
        $validKelompok = ['KOMPUTER UNIT', 'PERALATAN KOMPUTER'];
        return in_array(strtoupper($kelompok), $validKelompok);
    }
    
    // 18. Helper method - Get valid kelompok list
    public function getValidKelompok()
    {
        return ['KOMPUTER UNIT', 'PERALATAN KOMPUTER'];
    }
    
    // 19. Helper method - Clean data untuk import
public function cleanImportData($data)
{
    $cleaned = [];
    
    // Clean dan assign nilai default
    $cleaned['kode_barang'] = $this->truncateString(trim($data['kode_barang'] ?? ''), 100);
    $cleaned['nama_barang'] = $this->truncateString(trim($data['nama_barang'] ?? '') ?: 'Unknown', 255);
    $cleaned['kelompok'] = $this->truncateString(strtoupper(trim($data['kelompok'] ?? '')), 100);
    $cleaned['nup'] = $this->truncateString(trim($data['nup'] ?? ''), 100);
    $cleaned['merk'] = $this->truncateString(trim($data['merk'] ?? ''), 100);
    $cleaned['kondisi'] = $this->truncateString(strtoupper(trim($data['kondisi'] ?? '')), 50);
    $cleaned['kuantitas'] = intval($data['kuantitas'] ?? 1);
    $cleaned['status_penggunaan'] = $this->truncateString(trim($data['status_penggunaan'] ?? ''), 100);
    $cleaned['processor'] = $this->truncateString(trim($data['processor'] ?? ''), 100);
    $cleaned['memori'] = $this->truncateString(trim($data['memori'] ?? ''), 100);
    $cleaned['hardisk'] = $this->truncateString(trim($data['hardisk'] ?? ''), 100);
    $cleaned['monitor'] = $this->truncateString(trim($data['monitor'] ?? ''), 100);
    $cleaned['spek_lain'] = $this->truncateString(trim($data['spek_lain'] ?? ''), 500);
    
    // Field baru
    $cleaned['bidang'] = $this->truncateString(trim($data['bidang'] ?? ''), 100);
    $cleaned['pengguna_sebelumnya'] = $this->truncateString(trim($data['pengguna_sebelumnya'] ?? ''), 255);
    $cleaned['pengguna_sekarang'] = $this->truncateString(trim($data['pengguna_sekarang'] ?? ''), 255);
    $cleaned['status_barang'] = $this->truncateString(trim($data['status_barang'] ?? ''), 100);
    $cleaned['keterangan'] = $this->truncateString(trim($data['keterangan'] ?? ''), 255);
    
    // Handle numeric fields
    $cleaned['nilai_perolehan'] = $this->safeFloat($data['nilai_perolehan'] ?? 0);
    $cleaned['nilai_penyusutan'] = $this->safeFloat($data['nilai_penyusutan'] ?? 0);
    $cleaned['nilai_buku'] = $this->safeFloat($data['nilai_buku'] ?? 0);
    
    // Handle date
    $cleaned['tanggal_perolehan'] = !empty($data['tanggal_perolehan']) ? $data['tanggal_perolehan'] : null;
    
    return $cleaned;
}
    
    // 20. Helper method - Safe float conversion
    private function safeFloat($value)
    {
        if (is_null($value) || $value === '') {
            return 0.0;
        }
        
        if (is_string($value)) {
            $value = str_replace(',', '.', $value);
        }
        
        return floatval($value);
    }
public function importFromExcel($filePath)
{
    // Pastikan library PhpSpreadsheet tersedia
    if (!class_exists('\PhpOffice\PhpSpreadsheet\IOFactory')) {
        return [
            'success' => false,
            'message' => 'Library PhpSpreadsheet tidak tersedia. Pastikan composer require phpoffice/phpspreadsheet sudah diinstall.'
        ];
    }

    try {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
        
        // Array untuk menampung data gabungan
        $combinedData = [];
        
        // Baca data kondisi dari sheet Lampiran BA terlebih dahulu
        $conditionData = $this->extractConditionData($spreadsheet);
        
        // Baca data dari setiap sheet
        $sheetsToRead = [
            'Master Aset', 
            'Master Aset ', // Dengan spasi di akhir
            'MTI',
            'BDI',
            'TU',
            'Daftar Tablet PC',
            'Daftar External Harddisk',
            'Daftar Laptop_All'
        ];
        
        foreach ($sheetsToRead as $sheetName) {
            if ($spreadsheet->getSheetByName($sheetName)) {
                $data = $this->readSheetData($spreadsheet, $sheetName);
                $combinedData = $this->mergeData($combinedData, $data);
            }
        }
        
        // Update kondisi barang berdasarkan data dari sheet lampiran BA
        $combinedData = $this->updateCondition($combinedData, $conditionData);
        
        // Konversi data ke format database dan simpan
        return $this->saveDataToDatabase($combinedData);
    } catch (\Exception $e) {
        log_message('error', 'Error saat import Excel: ' . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error saat import Excel: ' . $e->getMessage()
        ];
    }
}

/**
 * Baca data kondisi dari sheet Lampiran BA
 */
private function extractConditionData($spreadsheet)
{
    $conditionData = [];
    $conditionSheets = [
        'Lampiran BA_Laptop',
        'Lampiran BA_Daftar Tablet PC',
        'Lampiran BA_External Harddisk'
    ];
    
    foreach ($conditionSheets as $sheetName) {
        if ($spreadsheet->getSheetByName($sheetName)) {
            try {
                $worksheet = $spreadsheet->getSheetByName($sheetName);
                
                // Ambil data dengan menangani error formula
                $sheetData = [];
                $highestRow = $worksheet->getHighestRow();
                $highestColumn = $worksheet->getHighestColumn();
                $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
                
                // Cari header row (biasanya baris pertama)
                $headerRow = 1;
                $headers = [];
                
                // Baca header row
                for ($col = 1; $col <= $highestColumnIndex; $col++) {
                    $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                    $cellCoordinate = $colLetter . $headerRow;
                    
                    try {
                        $cellValue = $worksheet->getCell($cellCoordinate)->getValue();
                        if ($cellValue) {
                            $headers[$colLetter] = trim($cellValue);
                        }
                    } catch (\Exception $e) {
                        continue;
                    }
                }
                
                // Cari indeks kolom untuk informasi yang kita butuhkan
                $namaBarangCol = null;
                $merkCol = null;
                $nupCol = null;
                $kondisiCol = null;
                
                foreach ($headers as $col => $header) {
                    if (stripos($header, 'Nama Barang') !== false) {
                        $namaBarangCol = $col;
                    } else if (stripos($header, 'Merk') !== false) {
                        $merkCol = $col;
                    } else if (stripos($header, 'NUP') !== false) {
                        $nupCol = $col;
                    } else if (stripos($header, 'Kondisi') !== false) {
                        $kondisiCol = $col;
                    }
                }
                
                // Jika kolom tidak ditemukan, lanjutkan ke sheet berikutnya
                if (!$namaBarangCol || !$merkCol || !$nupCol) {
                    continue;
                }
                
                // Mulai baca data dari baris setelah header
                for ($row = $headerRow + 1; $row <= $highestRow; $row++) {
                    try {
                        // Baca nilai dari kolom-kolom penting
                        $nama_barang = trim($worksheet->getCell($namaBarangCol . $row)->getValue() ?? '');
                        $merk = trim($worksheet->getCell($merkCol . $row)->getValue() ?? '');
                        $nup = trim($worksheet->getCell($nupCol . $row)->getValue() ?? '');
                        
                        // Hanya proses jika ketiga nilai tersebut ada
                        if ($nama_barang && $merk && $nup) {
                            $key = $nama_barang . '|' . $merk . '|' . $nup;
                            
                            // Baca kondisi dari kolom kondisi jika ada
                            if ($kondisiCol) {
                                $kondisi = trim($worksheet->getCell($kondisiCol . $row)->getValue() ?? '');
                                if (!empty($kondisi)) {
                                    $conditionData[$key] = $kondisi;
                                }
                            } else {
                                // Cari kolom Rusak/Baik jika tidak ada kolom Kondisi eksplisit
                                for ($col = 1; $col <= $highestColumnIndex; $col++) {
                                    $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                                    $header = $headers[$colLetter] ?? '';
                                    
                                    if (stripos($header, 'Rusak') !== false || stripos($header, 'Baik') !== false) {
                                        $cellValue = $worksheet->getCell($colLetter . $row)->getValue();
                                        
                                        if ($cellValue === '1' || $cellValue === 1 || strtoupper(trim($cellValue)) === 'YA') {
                                            // Jika nilainya 1 atau YA di kolom Rusak, tandai sebagai Rusak
                                            if (stripos($header, 'Rusak') !== false) {
                                                $conditionData[$key] = 'RUSAK';
                                            } else if (stripos($header, 'Baik') !== false) {
                                                $conditionData[$key] = 'BAIK';
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        continue;
                    }
                }
            } catch (\Exception $e) {
                log_message('warning', "Error membaca sheet $sheetName: " . $e->getMessage());
                continue;
            }
        }
    }
    
    return $conditionData;
}

/**
 * Baca dan standarisasi data dari sebuah sheet
 */
/**
 * Baca dan standarisasi data dari sebuah sheet dengan penanganan error formula
 */
private function readSheetData($spreadsheet, $sheetName)
{
    $data = [];
    
    if ($spreadsheet->getSheetByName($sheetName)) {
        try {
            $worksheet = $spreadsheet->getSheetByName($sheetName);
            
            // Ambil data dengan menangani error formula
            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();
            $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
            
            // Cari header row (biasanya baris pertama)
            $headerRow = 1;
            $headers = [];
            
            // Baca header row dengan penanganan error
            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                $cellCoordinate = $colLetter . $headerRow;
                
                try {
                    $cellValue = $worksheet->getCell($cellCoordinate)->getValue();
                    if ($cellValue) {
                        $headers[$colLetter] = trim($cellValue);
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }
            
            // Mapping header ke nama kolom yang diharapkan
            $headerMap = [];
            
            foreach ($headers as $col => $header) {
                $header = trim($header);
                if (empty($header)) continue;
                
                // Standarisasi nama header
                if (stripos($header, 'Nama Barang') !== false) {
                    $headerMap['nama_barang'] = $col;
                } else if (stripos($header, 'Merk') !== false) {
                    $headerMap['merk'] = $col;
                } else if (stripos($header, 'NUP') !== false) {
                    $headerMap['nup'] = $col;
                } else if (stripos($header, 'Bidang') !== false) {
                    $headerMap['bidang'] = $col;
                } else if (stripos($header, 'Kode Barang') !== false) {
                    $headerMap['kode_barang'] = $col;
                } else if (stripos($header, 'Tanggal Perolehan') !== false) {
                    $headerMap['tanggal_perolehan'] = $col;
                } else if (stripos($header, 'Nilai Perolehan') !== false) {
                    $headerMap['nilai_perolehan'] = $col;
                } else if (stripos($header, 'Pengguna Sebelumnya') !== false) {
                    $headerMap['pengguna_sebelumnya'] = $col;
                } else if (stripos($header, 'Pengguna Sekarang') !== false ||
                          stripos($header, 'Pengguna / Lokasi') !== false ||
                          stripos($header, 'Pengguna') !== false) {
                    $headerMap['pengguna_sekarang'] = $col;
                } else if (stripos($header, 'Kondisi') !== false) {
                    $headerMap['kondisi'] = $col;
                } else if (stripos($header, 'Status Penggunaan') !== false ||
                          (stripos($header, 'Status') !== false && !stripos($header, 'Status Barang') !== false)) {
                    $headerMap['status_penggunaan'] = $col;
                } else if (stripos($header, 'Status Barang') !== false) {
                    $headerMap['status_barang'] = $col;
                } else if (stripos($header, 'Keterangan') !== false) {
                    $headerMap['keterangan'] = $col;
                } else if (stripos($header, 'Processor') !== false) {
                    $headerMap['processor'] = $col;
                } else if (stripos($header, 'Memori') !== false || stripos($header, 'RAM') !== false) {
                    $headerMap['memori'] = $col;
                } else if (stripos($header, 'Hardisk') !== false || stripos($header, 'Storage') !== false || stripos($header, 'SSD') !== false) {
                    $headerMap['hardisk'] = $col;
                }
            }
            
            // Tentukan kelompok berdasarkan sheet
            $defaultKelompok = 'KOMPUTER UNIT';
            if ($sheetName === 'Daftar External Harddisk') {
                $defaultKelompok = 'PERALATAN KOMPUTER';
            }
            
            // Default bidang berdasarkan nama sheet
            $defaultBidang = 'Master';
            if (stripos($sheetName, 'MTI') !== false) {
                $defaultBidang = 'MTI';
            } else if (stripos($sheetName, 'BDI') !== false) {
                $defaultBidang = 'BDI';
            } else if (stripos($sheetName, 'TU') !== false) {
                $defaultBidang = 'TU';
            }
            
            // Mulai dari baris setelah header
            for ($row = $headerRow + 1; $row <= $highestRow; $row++) {
                try {
                    // Baca nilai dari kolom-kolom penting - minimal harus ada nama_barang
                    $nama_barang = '';
                    if (isset($headerMap['nama_barang'])) {
                        $nama_barang = trim($worksheet->getCell($headerMap['nama_barang'] . $row)->getValue() ?? '');
                    }
                    
                    // Skip jika tidak ada nama barang
                    if (empty($nama_barang)) continue;
                    
                    $merk = '';
                    if (isset($headerMap['merk'])) {
                        $merk = trim($worksheet->getCell($headerMap['merk'] . $row)->getValue() ?? '');
                    }
                    
                    $nup = '';
                    if (isset($headerMap['nup'])) {
                        $nup = trim($worksheet->getCell($headerMap['nup'] . $row)->getValue() ?? '');
                    }
                    
                    // Buat kunci unik untuk identifikasi
                    $key = $nama_barang . '|' . $merk . '|' . $nup;
                    
                    // Baca bidang
                    $bidang = $defaultBidang;
                    if (isset($headerMap['bidang'])) {
                        $tmpBidang = trim($worksheet->getCell($headerMap['bidang'] . $row)->getValue() ?? '');
                        if (!empty($tmpBidang)) {
                            $bidang = $tmpBidang;
                        }
                    }
                    
                    // Baca kode barang
                    $kode_barang = '';
                    if (isset($headerMap['kode_barang'])) {
                        $kode_barang = trim($worksheet->getCell($headerMap['kode_barang'] . $row)->getValue() ?? '');
                    }
                    
                    // Baca tanggal perolehan
                    $tanggal_perolehan = null;
                    if (isset($headerMap['tanggal_perolehan'])) {
                        $tmp = $worksheet->getCell($headerMap['tanggal_perolehan'] . $row)->getValue();
                        if (!empty($tmp)) {
                            try {
                                if (is_numeric($tmp)) {
                                    // Tanggal Excel
                                    $tanggal_perolehan = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tmp)->format('Y-m-d');
                                } else {
                                    // Coba format lain
                                    $date = date_create_from_format('Y-m-d', $tmp);
                                    if (!$date) $date = date_create_from_format('d-m-Y', $tmp);
                                    if (!$date) $date = date_create_from_format('d/m/Y', $tmp);
                                    if (!$date) $date = date_create($tmp);
                                    
                                    if ($date) {
                                        $tanggal_perolehan = $date->format('Y-m-d');
                                    }
                                }
                            } catch (\Exception $e) {
                                log_message('warning', "Error konversi tanggal: " . $e->getMessage());
                            }
                        }
                    }
                    
                    // Baca nilai perolehan
                    $nilai_perolehan = 0;
                    if (isset($headerMap['nilai_perolehan'])) {
                        $tmp = $worksheet->getCell($headerMap['nilai_perolehan'] . $row)->getValue();
                        if (!empty($tmp)) {
                            $nilai_perolehan = str_replace([' ', ',', '.'], '', $tmp);
                            if (!is_numeric($nilai_perolehan)) {
                                preg_match('/[0-9]+/', $nilai_perolehan, $matches);
                                $nilai_perolehan = $matches[0] ?? 0;
                            }
                        }
                    }
                    
                    // Baca pengguna
                    $pengguna_sebelumnya = '';
                    if (isset($headerMap['pengguna_sebelumnya'])) {
                        $pengguna_sebelumnya = trim($worksheet->getCell($headerMap['pengguna_sebelumnya'] . $row)->getValue() ?? '');
                    }
                    
                    $pengguna_sekarang = '';
                    if (isset($headerMap['pengguna_sekarang'])) {
                        $pengguna_sekarang = trim($worksheet->getCell($headerMap['pengguna_sekarang'] . $row)->getValue() ?? '');
                    }
                    
                    // Baca kondisi
                    $kondisi = '';
                    if (isset($headerMap['kondisi'])) {
                        $kondisi = trim($worksheet->getCell($headerMap['kondisi'] . $row)->getValue() ?? '');
                    }
                    
                    // Baca status
                    $status_penggunaan = '';
                    if (isset($headerMap['status_penggunaan'])) {
                        $status_penggunaan = trim($worksheet->getCell($headerMap['status_penggunaan'] . $row)->getValue() ?? '');
                    }
                    
                    $status_barang = '';
                    if (isset($headerMap['status_barang'])) {
                        $status_barang = trim($worksheet->getCell($headerMap['status_barang'] . $row)->getValue() ?? '');
                    }
                    
                    $keterangan = '';
                    if (isset($headerMap['keterangan'])) {
                        $keterangan = trim($worksheet->getCell($headerMap['keterangan'] . $row)->getValue() ?? '');
                    }
                    
                    // Baca spesifikasi
                    $processor = '';
                    if (isset($headerMap['processor'])) {
                        $processor = trim($worksheet->getCell($headerMap['processor'] . $row)->getValue() ?? '');
                    }
                    
                    $memori = '';
                    if (isset($headerMap['memori'])) {
                        $memori = trim($worksheet->getCell($headerMap['memori'] . $row)->getValue() ?? '');
                    }
                    
                    $hardisk = '';
                    if (isset($headerMap['hardisk'])) {
                        $hardisk = trim($worksheet->getCell($headerMap['hardisk'] . $row)->getValue() ?? '');
                    }
                    
                    // Simpan data terstandarisasi
                    $data[$key] = [
                        'kode_barang' => $this->truncateString($kode_barang, 100),
                        'nama_barang' => $this->truncateString($nama_barang, 255),
                        'merk' => $this->truncateString($merk, 100),
                        'nup' => $this->truncateString($nup, 100),
                        'bidang' => $this->truncateString($bidang, 100),
                        'kelompok' => $this->truncateString($defaultKelompok, 100),
                        'processor' => $this->truncateString($processor, 100),
                        'memori' => $this->truncateString($memori, 100),
                        'hardisk' => $this->truncateString($hardisk, 100),
                        'tanggal_perolehan' => $tanggal_perolehan,
                        'nilai_perolehan' => $nilai_perolehan,
                        'pengguna_sebelumnya' => $this->truncateString($pengguna_sebelumnya, 255),
                        'pengguna_sekarang' => $this->truncateString($pengguna_sekarang, 255),
                        'kondisi' => $this->truncateString($kondisi, 50),
                        'status_penggunaan' => $this->truncateString($status_penggunaan, 100),
                        'status_barang' => $this->truncateString($status_barang, 100),
                        'keterangan' => $this->truncateString($keterangan, 255)
                    ];
                } catch (\Exception $e) {
                    log_message('warning', "Error membaca baris $row di sheet $sheetName: " . $e->getMessage());
                    continue;
                }
            }
        } catch (\Exception $e) {
            log_message('error', "Error membaca sheet $sheetName: " . $e->getMessage());
        }
    }
    
    return $data;
}

/**
 * Gabungkan data dari berbagai sheet
 */
private function mergeData($existingData, $newData)
{
    foreach ($newData as $key => $item) {
        if (isset($existingData[$key])) {
            // Barang sudah ada, gabungkan data baru ke data yang sudah ada
            foreach ($item as $field => $value) {
                // Jika field kosong di data existing tapi ada isinya di data baru, 
                // atau jika data existing kosong dan data baru ada isinya
                if ((!isset($existingData[$key][$field]) || empty($existingData[$key][$field])) && !empty($value)) {
                    $existingData[$key][$field] = $value;
                }
            }
        } else {
            // Barang belum ada, tambahkan ke data gabungan
            $existingData[$key] = $item;
        }
    }
    
    return $existingData;
}

/**
 * Update kondisi barang berdasarkan data dari sheet Lampiran BA
 */
private function updateCondition($data, $conditionData)
{
    foreach ($data as $key => $item) {
        if (isset($conditionData[$key]) && !empty($conditionData[$key])) {
            // Prioritaskan kondisi dari sheet Lampiran BA
            $data[$key]['kondisi'] = $this->truncateString($conditionData[$key], 50);
        }
    }
    
    return $data;
}

/**
 * Simpan data ke database
 */
/**
 * Batasi panjang string sesuai batas maksimum kolom
 */
private function truncateString($string, $maxLength = 100)
{
    if (strlen($string) <= $maxLength) {
        return $string;
    }
    return substr($string, 0, $maxLength);
}

/**
 * Simpan data ke database dengan pembatasan panjang field
 */
private function saveDataToDatabase($data)
{
    $db = \Config\Database::connect();
    $builder = $db->table('komputer');
    
    $successCount = 0;
    $failureCount = 0;
    $existingCount = 0;
    $updatedCount = 0;
    
    // Konversi array asosiatif ke array datar untuk batch insert/update
    $batchData = [];
    
    foreach ($data as $key => $item) {
        // Skip jika data tidak memiliki field wajib
        if (empty($item['nama_barang'])) {
            $failureCount++;
            continue;
        }
        
        // Set kelompok default jika tidak ada
        if (empty($item['kelompok'])) {
            $item['kelompok'] = 'KOMPUTER UNIT';
        }
        
        // Cek apakah data sudah ada berdasarkan kombinasi nama_barang, merk, nup
        $query = $builder->where('nama_barang', $item['nama_barang'])
                         ->where('merk', $item['merk']);
        
        if (!empty($item['nup'])) {
            $query = $query->where('nup', $item['nup']);
        }
        
        $existingRecord = $query->get()->getRowArray();
        
        if ($existingRecord) {
            // Update record yang sudah ada
            $updateData = [];
            foreach ($item as $field => $value) {
                // Prioritaskan data baru yang tidak kosong, kecuali untuk field yang sudah diisi di DB
                if (!empty($value) && (empty($existingRecord[$field]) || $existingRecord[$field] != $value)) {
                    $updateData[$field] = $value;
                }
            }
            
            if (!empty($updateData)) {
                // Add timestamp
                $updateData['updated_at'] = date('Y-m-d H:i:s');
                
                try {
                    $builder->where('id', $existingRecord['id'])
                            ->update($updateData);
                    $updatedCount++;
                } catch (\Exception $e) {
                    log_message('error', 'Error saat update data: ' . $e->getMessage());
                    $failureCount++;
                }
            } else {
                $existingCount++;
            }
        } else {
            // Tambahkan record baru
            // Add timestamps
            $item['created_at'] = date('Y-m-d H:i:s');
            $item['updated_at'] = date('Y-m-d H:i:s');
            
            // Bersihkan data
            $cleanedData = $this->cleanImportData($item);
            
            $batchData[] = $cleanedData;
        }
        
        // Proses data batch setiap 20 records untuk menghindari memory issues
        if (count($batchData) >= 20) {
            try {
                $insertResult = $builder->insertBatch($batchData);
                $successCount += $insertResult;
                $batchData = []; // Reset setelah batch diproses
            } catch (\Exception $e) {
                log_message('error', 'Error saat batch insert: ' . $e->getMessage());
                $failureCount += count($batchData);
                $batchData = []; // Reset batch jika error
            }
        }
    }
    
    // Proses sisa batch terakhir jika ada
    if (!empty($batchData)) {
        try {
            $insertResult = $builder->insertBatch($batchData);
            $successCount += $insertResult;
        } catch (\Exception $e) {
            log_message('error', 'Error saat batch insert: ' . $e->getMessage());
            $failureCount += count($batchData);
        }
    }
    
    return [
        'success' => true,
        'message' => "Impor Excel selesai: $successCount record ditambahkan, $updatedCount diupdate, $existingCount tidak diubah, $failureCount gagal.",
        'success_count' => $successCount,
        'updated_count' => $updatedCount,
        'existing_count' => $existingCount,
        'failure_count' => $failureCount
    ];
}
}