<?php
namespace App\Models;
use CodeIgniter\Model;

class AlatKedokteranKesehatanModel extends Model
{
    protected $table = 'alat_kedokteran_kesehatan';
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
        'processor',
        'memori',
        'hardisk',
        'monitor',
        'spek_lain',
        'kelompok', // ALAT KEDOKTERAN, ALAT KESEHATAN UMUM
        'sub_kelompok',
        'kategori_utama',
        'kategori_detail',
        'created_at',
        'updated_at',
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
        'processor' => 'permit_empty|max_length[200]',
        'memori' => 'permit_empty|max_length[100]',
        'hardisk' => 'permit_empty|max_length[100]',
        'monitor' => 'permit_empty|max_length[100]',
        'spek_lain' => 'permit_empty|max_length[500]',
        'nilai_perolehan' => 'permit_empty|decimal',
        'nilai_penyusutan' => 'permit_empty|decimal',
        'nilai_buku' => 'permit_empty|decimal',
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
        'processor' => [
            'max_length' => 'Processor maksimal 200 karakter'
        ],
        'memori' => [
            'max_length' => 'Memori maksimal 100 karakter'
        ],
        'hardisk' => [
            'max_length' => 'Hardisk maksimal 100 karakter'
        ],
        'monitor' => [
            'max_length' => 'Monitor maksimal 100 karakter'
        ],
        'spek_lain' => [
            'max_length' => 'Spesifikasi lain maksimal 500 karakter'
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
    public function searchAlatKedokteranKesehatan($searchTerm = '', $kelompok = '', $limit = 100, $offset = 0)
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
                ->orLike('sub_kelompok', $searchTerm)
                ->orLike('nup', $searchTerm)
                ->orLike('kategori_detail', $searchTerm)
                ->orLike('processor', $searchTerm)
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
        
        // Per kelompok - KHUSUS UNTUK ALAT KEDOKTERAN DAN KESEHATAN
        $kelompokList = [
            'ALAT KEDOKTERAN',
            'ALAT KESEHATAN UMUM'
        ];
        
        foreach ($kelompokList as $kelompok) {
            $key = strtolower(str_replace([' ', '/', '&', '-'], '_', $kelompok));
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
    
    // ============ BUSINESS LOGIC KHUSUS ALAT KEDOKTERAN DAN KESEHATAN ============
    
    // 16. Mapping kelompok dari API ke kategori detail
    public function mapKelompokToKategori($kelompok_api) 
    {
        $mapping = [
            'ALAT KEDOKTERAN' => 'Alat Kedokteran',
            'ALAT KESEHATAN UMUM' => 'Alat Kesehatan Umum'
        ];
        
        return $mapping[strtoupper($kelompok_api)] ?? null;
    }

    // 17. Cek apakah kelompok valid untuk alat kedokteran dan kesehatan
    public function isValidKelompokAlatKedokteranKesehatan($kelompok)
    {
        $validKelompok = [
            'ALAT KEDOKTERAN',
            'ALAT KESEHATAN UMUM'
        ];
        return in_array(strtoupper($kelompok), $validKelompok);
    }

    // 18. Get data berdasarkan kategori detail
    public function getByKategoriDetail($kategoriDetail)
    {
        return $this->where('kategori_detail', $kategoriDetail)->findAll();
    }

    // 19. Get statistik per kategori
    public function getStatistikPerKategori()
    {
        return $this->select('kategori_detail, COUNT(*) as jumlah')
                   ->groupBy('kategori_detail')
                   ->findAll();
    }
    
    // ============ VALIDATION METHODS (Pure PHP) ============
    
    // 20. Validation method - Pure PHP validation untuk import data
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
            'sub_kelompok' => 100,
            'kategori_utama' => 100,
            'kategori_detail' => 100,
            'processor' => 200,
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
        if (isset($data['kelompok']) && !$this->isValidKelompokAlatKedokteranKesehatan($data['kelompok'])) {
            $errors[] = 'Kelompok harus salah satu dari kelompok alat kedokteran dan kesehatan yang valid';
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
        
        // Date validation - PERBAIKAN: TIDAK MEMVALIDASI FORMAT LAGI
        // Biarkan safeDate() yang menangani konversi format
        if (isset($data['tanggal_perolehan']) && 
            !empty($data['tanggal_perolehan'])) {
            $cleanedDate = $this->safeDate($data['tanggal_perolehan']);
            if ($cleanedDate === null && !empty($data['tanggal_perolehan'])) {
                $errors[] = 'Format tanggal perolehan tidak dapat dikonversi';
            }
        }
        
        return $errors;
    }
    
    // 21. Validation method - Validate kelompok (untuk form input)
    public function validateKelompok($kelompok)
    {
        return $this->isValidKelompokAlatKedokteranKesehatan($kelompok);
    }
    
    // 22. Helper method - Get valid kelompok list
    public function getValidKelompok()
    {
        return [
            'ALAT KEDOKTERAN',
            'ALAT KESEHATAN UMUM'
        ];
    }
    
    // 23. Helper method - Clean data untuk import
    public function cleanImportData($data)
    {
        $cleaned = [];
        
        // Clean dan assign nilai default
        $cleaned['kode_barang'] = trim($data['kode_barang'] ?? '');
        $cleaned['nama_barang'] = trim($data['nama_barang'] ?? '') ?: 'Unknown';
        $cleaned['kelompok'] = strtoupper(trim($data['kelompok'] ?? ''));
        $cleaned['sub_kelompok'] = trim($data['sub_kelompok'] ?? '');
        $cleaned['nup'] = trim($data['nup'] ?? '');
        $cleaned['merk'] = trim($data['merk'] ?? '');
        $cleaned['kondisi'] = strtoupper(trim($data['kondisi'] ?? ''));
        $cleaned['kuantitas'] = intval($data['kuantitas'] ?? 1);
        $cleaned['status_penggunaan'] = trim($data['status_penggunaan'] ?? '');
        $cleaned['nama_satker'] = trim($data['nama_satker'] ?? '');
        $cleaned['processor'] = trim($data['processor'] ?? '');
        $cleaned['memori'] = trim($data['memori'] ?? '');
        $cleaned['hardisk'] = trim($data['hardisk'] ?? '');
        $cleaned['monitor'] = trim($data['monitor'] ?? '');
        $cleaned['spek_lain'] = trim($data['spek_lain'] ?? '');
        
        // Handle numeric fields
        $cleaned['nilai_perolehan'] = $this->safeFloat($data['nilai_perolehan'] ?? 0);
        $cleaned['nilai_penyusutan'] = $this->safeFloat($data['nilai_penyusutan'] ?? 0);
        $cleaned['nilai_buku'] = $this->safeFloat($data['nilai_buku'] ?? 0);
        
        // Handle date - PERBAIKAN UNTUK MENDUKUNG BERBAGAI FORMAT TANGGAL
        $cleaned['tanggal_perolehan'] = $this->safeDate($data['tanggal_perolehan'] ?? null);
        
        // Set kategori - KHUSUS UNTUK ALAT KEDOKTERAN DAN KESEHATAN
        $cleaned['kategori_utama'] = 'ALAT KEDOKTERAN DAN KESEHATAN';
        $cleaned['kategori_detail'] = $this->mapKelompokToKategori($cleaned['kelompok']);
        
        return $cleaned;
    }
    
    // 24. Helper method - Safe float conversion
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
    
    // 25. Helper method - Safe date conversion
    private function safeDate($value)
    {
        if (empty($value) || is_null($value)) {
            return null;
        }
        
        // Jika sudah format Y-m-d
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return $value;
        }
        
        // Coba berbagai format tanggal yang mungkin dari API
        $formats = [
            'd/m/Y',        // 26/09/2025
            'd-m-Y',        // 26-09-2025
            'Y/m/d',        // 2025/09/26
            'd.m.Y',        // 26.09.2025
            'Y.m.d',        // 2025.09.26
            'm/d/Y',        // 09/26/2025
            'm-d-Y',        // 09-26-2025
            'Y-m-d H:i:s',  // 2025-09-26 10:30:45
            'd/m/Y H:i:s',  // 26/09/2025 10:30:45
            'd-m-Y H:i:s',  // 26-09-2025 10:30:45
            'Y/m/d H:i:s',  // 2025/09/26 10:30:45
        ];
        
        foreach ($formats as $format) {
            $date = \DateTime::createFromFormat($format, $value);
            if ($date && $date->format($format) === $value) {
                return $date->format('Y-m-d');
            }
        }
        
        // Jika tidak ada format yang cocok, log error dan return null
        log_message('error', "Cannot parse date: {$value}");
        return null;
    }
}