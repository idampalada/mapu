<?php
namespace App\Models;
use CodeIgniter\Model;

class AsetTakBerwujudModel extends Model
{
    protected $table = 'aset_tak_berwujud';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'tgl_tarik',
        'kode_kl',
        'nama_kl',
        'kode_kpknl',
        'nama_kpknl',
        'kode_satker',
        'nama_satker',
        'kode_sub_satker',
        'nama_sub_satker',
        'kode_barang',
        'nama_barang',
        'nilai_perolehan_pertama',
        'nilai_mutasi',
        'nilai_perolehan',
        'nilai_penyusutan',
        'nilai_buku',
        'nup',
        'no_kib',
        'tanggal_perolehan',
        'tgl_buku',
        'tgl_rekam',
        'kondisi',
        'merk',
        'kuantitas',
        'status_penggunaan',
        'kelompok', // ASET TAK BERWUJUD, ASET TAK BERWUJUD DALAM PENYELESAIAN, ASET KEMITRAAN
        'sub_kelompok',
        'dihentikan_yn',
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
        'merk' => 'permit_empty|max_length[255]',
        'kondisi' => 'permit_empty|max_length[50]',
        'kuantitas' => 'permit_empty|integer',
        'status_penggunaan' => 'permit_empty|max_length[100]',
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
        ]
    ];
    
    protected $skipValidation = false;
    protected $cleanValidationRules = true;
    
    // ============ CRUD DASAR ============
    
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
    
    public function findById($id)
    {
        $builder = $this->builder();
        return $builder->where('id', $id)
                      ->get()
                      ->getRowArray();
    }
    
    public function insertData($data)
    {
        $builder = $this->builder();
        
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        return $builder->insert($data);
    }
    
    public function updateData($id, $data)
    {
        $builder = $this->builder();
        
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        return $builder->where('id', $id)
                      ->update($data);
    }
    
    public function deleteData($id)
    {
        $builder = $this->builder();
        return $builder->where('id', $id)
                      ->delete();
    }
    
    public function countAll()
    {
        $builder = $this->builder();
        return $builder->countAllResults();
    }
    
    // ============ BUSINESS LOGIC ============
    
    public function findByKodeBarang($kodeBarang)
    {
        $builder = $this->builder();
        return $builder->where('kode_barang', $kodeBarang)
                      ->get()
                      ->getRowArray();
    }
    
    public function searchAsetTakBerwujud($searchTerm = '', $kelompok = '', $limit = 100, $offset = 0)
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
                ->orLike('nup', $searchTerm)
                ->orLike('no_kib', $searchTerm)
                ->groupEnd();
        }
        
        return $builder->orderBy('kode_barang', 'ASC')
                      ->limit($limit, $offset)
                      ->get()
                      ->getResultArray();
    }
    
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
    
    public function countByKelompok($kelompok)
    {
        $builder = $this->builder();
        return $builder->where('UPPER(kelompok)', strtoupper($kelompok))
                      ->countAllResults();
    }
    
    public function getStatistikKelompok()
    {
        $builder = $this->builder();
        
        return $builder->select('kelompok, COUNT(*) as jumlah')
                      ->groupBy('kelompok')
                      ->orderBy('kelompok', 'ASC')
                      ->get()
                      ->getResultArray();
    }
    
    public function getDashboardData()
    {
        $data = [];
        
        // Total semua data
        $data['total_all'] = $this->countAll();
        
        // Per kelompok
        $kelompokList = ['ASET TAK BERWUJUD', 'ASET TAK BERWUJUD DALAM PENYELESAIAN', 'ASET KEMITRAAN'];
        
        foreach ($kelompokList as $kelompok) {
            $key = strtolower(str_replace(' ', '_', $kelompok));
            $data[$key] = $this->countByKelompok($kelompok);
        }
        
        return $data;
    }
    
    public function isKodeBarangExists($kodeBarang, $excludeId = null)
    {
        $builder = $this->builder();
        $builder->where('kode_barang', $kodeBarang);
        
        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }
        
        return $builder->countAllResults() > 0;
    }
    
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
    
    // ============ VALIDATION METHODS ============
    
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
            'merk' => 255,
            'kondisi' => 50,
            'status_penggunaan' => 100,
        ];
        
        foreach ($lengthValidation as $field => $maxLength) {
            if (isset($data[$field]) && strlen($data[$field]) > $maxLength) {
                $errors[] = ucfirst(str_replace('_', ' ', $field)) . " maksimal {$maxLength} karakter";
            }
        }
        
        // Kelompok validation
        $validKelompok = ['ASET TAK BERWUJUD', 'ASET TAK BERWUJUD DALAM PENYELESAIAN', 'ASET KEMITRAAN'];
        if (isset($data['kelompok']) && !in_array(strtoupper($data['kelompok']), $validKelompok)) {
            $errors[] = 'Kelompok harus salah satu dari: ' . implode(', ', $validKelompok);
        }
        
        // Kondisi validation
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
        
        return $errors;
    }
    
    public function validateKelompok($kelompok)
    {
        $validKelompok = ['ASET TAK BERWUJUD', 'ASET TAK BERWUJUD DALAM PENYELESAIAN', 'ASET KEMITRAAN'];
        return in_array(strtoupper($kelompok), $validKelompok);
    }
    
    public function getValidKelompok()
    {
        return ['ASET TAK BERWUJUD', 'ASET TAK BERWUJUD DALAM PENYELESAIAN', 'ASET KEMITRAAN'];
    }
    
    public function cleanImportData($data)
    {
        $cleaned = [];
        
        $cleaned['kode_barang'] = trim($data['kode_barang'] ?? '');
        $cleaned['nama_barang'] = trim($data['nama_barang'] ?? '') ?: 'Unknown';
        $cleaned['kelompok'] = strtoupper(trim($data['kelompok'] ?? ''));
        $cleaned['sub_kelompok'] = trim($data['sub_kelompok'] ?? '');
        $cleaned['nup'] = trim($data['nup'] ?? '');
        $cleaned['no_kib'] = trim($data['no_kib'] ?? '');
        $cleaned['merk'] = trim($data['merk'] ?? '');
        $cleaned['kondisi'] = strtoupper(trim($data['kondisi'] ?? ''));
        $cleaned['kuantitas'] = intval($data['kuantitas'] ?? 1);
        $cleaned['status_penggunaan'] = trim($data['status_penggunaan'] ?? '');
        
        // Handle numeric fields
        $cleaned['nilai_perolehan'] = $this->safeFloat($data['nilai_perolehan'] ?? 0);
        $cleaned['nilai_penyusutan'] = $this->safeFloat($data['nilai_penyusutan'] ?? 0);
        $cleaned['nilai_buku'] = $this->safeFloat($data['nilai_buku'] ?? 0);
        
        // Handle date
        $cleaned['tanggal_perolehan'] = !empty($data['tanggal_perolehan']) ? $data['tanggal_perolehan'] : null;
        
        return $cleaned;
    }
    
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