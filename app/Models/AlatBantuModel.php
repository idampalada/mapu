<?php
namespace App\Models;
use CodeIgniter\Model;

class AlatBantuModel extends Model
{
    protected $table = 'alat_bantu';
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
        'kelompok',
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
    public function searchAlatBantu($searchTerm = '', $kondisi = '', $limit = 100, $offset = 0)
    {
        $builder = $this->builder();
        
        if (!empty($kondisi)) {
            $builder->where('kondisi', $kondisi);
        }
        
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
    
    // 9. BUSINESS LOGIC - Filter by kelompok
    public function getByKelompok($kelompok, $limit = null, $offset = 0)
    {
        $builder = $this->builder();
        
        $builder->where('kelompok', $kelompok);
        
        if ($limit) {
            $builder->limit($limit, $offset);
        }
        
        return $builder->orderBy('kode_barang', 'ASC')
                      ->get()
                      ->getResultArray();
    }
    
    // 10. BUSINESS LOGIC - Filter by kondisi
    public function getByKondisi($kondisi, $limit = null, $offset = 0)
    {
        $builder = $this->builder();
        
        $builder->where('kondisi', $kondisi);
        
        if ($limit) {
            $builder->limit($limit, $offset);
        }
        
        return $builder->orderBy('kode_barang', 'ASC')
                      ->get()
                      ->getResultArray();
    }
    
    // 11. BUSINESS LOGIC - Get by status penggunaan
    public function getByStatusPenggunaan($status, $limit = null, $offset = 0)
    {
        $builder = $this->builder();
        
        $builder->where('status_penggunaan', $status);
        
        if ($limit) {
            $builder->limit($limit, $offset);
        }
        
        return $builder->orderBy('kode_barang', 'ASC')
                      ->get()
                      ->getResultArray();
    }
    
    // 12. BUSINESS LOGIC - Get total nilai perolehan
    public function getTotalNilaiPerolehan()
    {
        $builder = $this->builder();
        
        $result = $builder->selectSum('CAST(nilai_perolehan AS DECIMAL)', 'total')
                         ->get()
                         ->getRowArray();
        
        return $result['total'] ?? 0;
    }
    
    // 13. BUSINESS LOGIC - Get total nilai buku
    public function getTotalNilaiBuku()
    {
        $builder = $this->builder();
        
        $result = $builder->selectSum('CAST(nilai_buku AS DECIMAL)', 'total')
                         ->get()
                         ->getRowArray();
        
        return $result['total'] ?? 0;
    }
    
    // 14. BUSINESS LOGIC - Get data by date range
    public function getDataByDateRange($startDate, $endDate)
    {
        $builder = $this->builder();
        
        if ($startDate && $endDate) {
            $builder->where('DATE(created_at) >=', $startDate)
                   ->where('DATE(created_at) <=', $endDate);
        }
        
        return $builder->orderBy('created_at', 'DESC')
                      ->get()
                      ->getResultArray();
    }
    
    // 15. BUSINESS LOGIC - Get statistics
    public function getStatistik()
    {
        $builder = $this->builder();
        
        $result = $builder->select('
                COUNT(*) as total_records,
                COUNT(CASE WHEN kondisi = \'BAIK\' THEN 1 END) as kondisi_baik,
                COUNT(CASE WHEN kondisi = \'RUSAK RINGAN\' THEN 1 END) as kondisi_rusak_ringan,
                COUNT(CASE WHEN kondisi = \'RUSAK BERAT\' THEN 1 END) as kondisi_rusak_berat,
                COALESCE(SUM(CAST(nilai_perolehan AS DECIMAL)), 0) as total_nilai_perolehan,
                COALESCE(SUM(CAST(nilai_buku AS DECIMAL)), 0) as total_nilai_buku,
                COALESCE(AVG(CAST(nilai_perolehan AS DECIMAL)), 0) as avg_nilai_perolehan
            ')
            ->get()
            ->getRowArray();
        
        return $result;
    }
    
    // ============ VALIDATION METHOD (Pure PHP - bukan database operation) ============
    
    // 16. Validation method - Pure PHP
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
            !empty($data['tahun_buat']) && 
            (!is_numeric($data['tahun_buat']) || strlen($data['tahun_buat']) != 4)) {
            $errors[] = 'Tahun buat harus berupa 4 digit angka';
        }
        
        return $errors;
    }
}