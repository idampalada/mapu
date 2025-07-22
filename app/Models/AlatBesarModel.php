<?php
namespace App\Models;
use CodeIgniter\Model;

class AlatBesarModel extends Model
{
    protected $table = 'alat_besar';
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
        'tahun_buat',
        'no_mesin',
        'no_rangka',
        'kelompok', // ALAT BESAR DARAT, ALAT BANTU, ALAT BESAR APUNG
        'sub_kelompok',
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
        'status_penggunaan' => 'permit_empty|max_length[100]',
        'tahun_buat' => 'permit_empty|max_length[4]',
        'no_mesin' => 'permit_empty|max_length[100]',
        'no_rangka' => 'permit_empty|max_length[100]',
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
    public function searchAlatBesar($searchTerm = '', $kelompok = '', $limit = 100, $offset = 0)
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
                ->orLike('no_mesin', $searchTerm)
                ->orLike('no_rangka', $searchTerm)
                ->orLike('nup', $searchTerm)
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
        $kelompokList = ['ALAT BESAR DARAT', 'ALAT BANTU', 'ALAT BESAR APUNG'];
        
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
            'status_penggunaan' => 100,
            'tahun_buat' => 4,
            'no_mesin' => 100,
            'no_rangka' => 100
        ];
        
        foreach ($lengthValidation as $field => $maxLength) {
            if (isset($data[$field]) && strlen($data[$field]) > $maxLength) {
                $errors[] = ucfirst(str_replace('_', ' ', $field)) . " maksimal {$maxLength} karakter";
            }
        }
        
        // Kelompok validation (harus salah satu dari kelompok yang valid)
        $validKelompok = ['ALAT BESAR DARAT', 'ALAT BANTU', 'ALAT BESAR APUNG'];
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
        
        // Year validation
        if (isset($data['tahun_buat']) && 
            !empty($data['tahun_buat'])) {
            if (!is_numeric($data['tahun_buat']) || strlen($data['tahun_buat']) != 4) {
                $errors[] = 'Tahun buat harus berupa 4 digit angka';
            } else {
                $tahun = intval($data['tahun_buat']);
                $currentYear = intval(date('Y'));
                if ($tahun < 1900 || $tahun > $currentYear + 1) {
                    $errors[] = 'Tahun buat tidak valid (1900 - ' . ($currentYear + 1) . ')';
                }
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
        $validKelompok = ['ALAT BESAR DARAT', 'ALAT BANTU', 'ALAT BESAR APUNG'];
        return in_array(strtoupper($kelompok), $validKelompok);
    }
    
    // 18. Helper method - Get valid kelompok list
    public function getValidKelompok()
    {
        return ['ALAT BESAR DARAT', 'ALAT BANTU', 'ALAT BESAR APUNG'];
    }
    
    // 19. Helper method - Clean data untuk import
    public function cleanImportData($data)
    {
        $cleaned = [];
        
        // Clean dan assign nilai default
        $cleaned['kode_barang'] = trim($data['kode_barang'] ?? '');
        $cleaned['nama_barang'] = trim($data['nama_barang'] ?? '') ?: 'Unknown';
        $cleaned['kelompok'] = strtoupper(trim($data['kelompok'] ?? ''));
        $cleaned['nup'] = trim($data['nup'] ?? '');
        $cleaned['merk'] = trim($data['merk'] ?? '');
        $cleaned['kondisi'] = strtoupper(trim($data['kondisi'] ?? ''));
        $cleaned['kuantitas'] = intval($data['kuantitas'] ?? 1);
        $cleaned['status_penggunaan'] = trim($data['status_penggunaan'] ?? '');
        $cleaned['tahun_buat'] = trim($data['tahun_buat'] ?? '');
        $cleaned['no_mesin'] = trim($data['no_mesin'] ?? '');
        $cleaned['no_rangka'] = trim($data['no_rangka'] ?? '');
        
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
}