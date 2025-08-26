<?php
namespace App\Models;
use CodeIgniter\Model;

class TanahModel extends Model
{
    protected $table = 'tanah';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'kode_barang',
        'nama_barang',
        'alamat',
        'kelompok',
        'luas_tanah_seluruhnya',
        'status_penggunaan',
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
        'luas_tanah_seluruhnya' => 'permit_empty|numeric',
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
        'luas_tanah_seluruhnya' => [
            'numeric' => 'Luas tanah harus berupa angka'
        ]
    ];
    
    protected $skipValidation = false;
    protected $cleanValidationRules = true;
    
    
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
    
    // 7. BUSINESS LOGIC - Find by kode barang (converted to Query Builder)
    public function findByKodeBarang($kodeBarang)
    {
        $builder = $this->builder();
        return $builder->where('kode_barang', $kodeBarang)
                      ->get()
                      ->getRowArray();
    }
    
    // 8. BUSINESS LOGIC - Search functionality (converted to Query Builder)  
    public function searchTanah($searchTerm = '', $kelompok = '', $limit = 100, $offset = 0)
    {
        $builder = $this->builder();
        
        if (!empty($kelompok)) {
            $builder->where('kelompok', $kelompok);
        }
        
        if (!empty($searchTerm)) {
            $searchTerm = $this->db->escapeLikeString($searchTerm);
            
            $builder->groupStart()
                ->like('kode_barang', $searchTerm)
                ->orLike('nama_barang', $searchTerm)
                ->orLike('alamat', $searchTerm)
                ->groupEnd();
        }
        
        return $builder->orderBy('kode_barang', 'ASC')
                      ->limit($limit, $offset)
                      ->get()
                      ->getResultArray();
    }
    
    // ============ VALIDATION METHOD (Pure PHP - bukan database operation) ============
    
    // 9. Validation method - Pure PHP
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
            'kelompok' => 100
        ];
        
        foreach ($lengthValidation as $field => $maxLength) {
            if (isset($data[$field]) && strlen($data[$field]) > $maxLength) {
                $errors[] = ucfirst(str_replace('_', ' ', $field)) . " maksimal {$maxLength} karakter";
            }
        }
        
        // Numeric validation
        if (isset($data['luas_tanah_seluruhnya']) && 
            !empty($data['luas_tanah_seluruhnya']) && 
            !is_numeric($data['luas_tanah_seluruhnya'])) {
            $errors[] = 'Luas tanah harus berupa angka';
        }
        
        return $errors;
    }
}