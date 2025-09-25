<?php
namespace App\Models;
use CodeIgniter\Model;

class JalanDanJembatanModel extends Model
{
    protected $table = 'jalan_dan_jembatan';
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
        'panjang',
        'lebar',
        'luas',
        'konstruksi',
        'kelompok', // JALAN atau JEMBATAN
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
        'kelompok' => 'required|max_length[50]',
        'nup' => 'permit_empty|max_length[100]',
        'merk' => 'permit_empty|max_length[100]',
        'kondisi' => 'permit_empty|max_length[50]',
        'kuantitas' => 'permit_empty|integer',
        'status_penggunaan' => 'permit_empty|max_length[200]',
        'panjang' => 'permit_empty|decimal',
        'lebar' => 'permit_empty|decimal',
        'luas' => 'permit_empty|decimal',
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
            'max_length' => 'Kelompok maksimal 50 karakter'
        ],
        'kuantitas' => [
            'integer' => 'Kuantitas harus berupa angka'
        ],
        'panjang' => [
            'decimal' => 'Panjang harus berupa angka'
        ],
        'lebar' => [
            'decimal' => 'Lebar harus berupa angka'
        ],
        'luas' => [
            'decimal' => 'Luas harus berupa angka'
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
        return $builder->where('id', $id)->update($data);
    }
    
    public function deleteData($id)
    {
        $builder = $this->builder();
        return $builder->where('id', $id)->delete();
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
    
    public function searchJalanDanJembatan($searchTerm = '', $kelompok = '', $limit = 100, $offset = 0)
    {
        $builder = $this->builder();
        
        if (!empty($kelompok)) {
            $builder->where('UPPER(kelompok)', strtoupper($kelompok));
        }
        
        if (!empty($searchTerm)) {
            $searchTerm = $this->db->escapeLikeString($searchTerm);
            
            $builder->groupStart()
                ->like('kode_barang', $searchTerm)
                ->orLike('nama_barang', $searchTerm)
                ->orLike('merk', $searchTerm)
                ->orLike('sub_kelompok', $searchTerm)
                ->orLike('nup', $searchTerm)
                ->orLike('konstruksi', $searchTerm)
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
    
    public function getStatistikKonstruksi($kelompok = '')
    {
        $builder = $this->builder();
        
        if (!empty($kelompok)) {
            $builder->where('UPPER(kelompok)', strtoupper($kelompok));
        }
        
        return $builder->select('konstruksi, COUNT(*) as jumlah')
                      ->groupBy('konstruksi')
                      ->orderBy('jumlah', 'DESC')
                      ->get()
                      ->getResultArray();
    }
    
    public function getDashboardData()
    {
        $data = [];
        $data['total_all'] = $this->countAll();
        
        $kelompokList = ['JALAN', 'JEMBATAN'];
        
        foreach ($kelompokList as $kelompok) {
            $key = strtolower($kelompok);
            $data[$key] = $this->countByKelompok($kelompok);
        }
        
        $data['kondisi_stats'] = $this->getStatistikKondisi();
        $data['konstruksi_stats'] = $this->getStatistikKonstruksi();
        
        $builder = $this->builder();
        $result = $builder->selectSum('panjang')->get()->getRowArray();
        $data['total_panjang'] = $result['panjang'] ?? 0;
        
        $result = $builder->selectSum('luas')->get()->getRowArray();
        $data['total_luas'] = $result['luas'] ?? 0;
        
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
        
        foreach ($dataArray as &$data) {
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');
        }
        
        return $builder->insertBatch($dataArray);
    }
    
    // ============ BUSINESS LOGIC KHUSUS ============
    
    public function mapKelompokToKategori($kelompok_api) 
    {
        $mapping = [
            'JALAN' => 'Jalan',
            'JEMBATAN' => 'Jembatan'
        ];
        
        return $mapping[strtoupper($kelompok_api)] ?? null;
    }

    public function isValidKelompokJalanDanJembatan($kelompok)
    {
        $validKelompok = ['JALAN', 'JEMBATAN'];
        return in_array(strtoupper($kelompok), $validKelompok);
    }

    public function getByKategoriDetail($kategoriDetail)
    {
        return $this->where('kategori_detail', $kategoriDetail)->findAll();
    }

    public function getStatistikPerKategori()
    {
        return $this->select('kategori_detail, COUNT(*) as jumlah')
                   ->groupBy('kategori_detail')
                   ->findAll();
    }
    
    // ============ VALIDATION METHODS ============
    
    public function validateImportData($data)
    {
        $errors = [];
        
        $requiredFields = ['kode_barang', 'nama_barang', 'kelompok'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' tidak boleh kosong';
            }
        }
        
        $lengthValidation = [
            'kode_barang' => 100,
            'nama_barang' => 255,
            'kelompok' => 50,
            'nup' => 100,
            'merk' => 100,
            'kondisi' => 50,
            'status_penggunaan' => 200,
            'sub_kelompok' => 100,
            'konstruksi' => 100
        ];
        
        foreach ($lengthValidation as $field => $maxLength) {
            if (isset($data[$field]) && strlen($data[$field]) > $maxLength) {
                $errors[] = ucfirst(str_replace('_', ' ', $field)) . " maksimal {$maxLength} karakter";
            }
        }
        
        if (isset($data['kelompok']) && !$this->isValidKelompokJalanDanJembatan($data['kelompok'])) {
            $errors[] = 'Kelompok harus JALAN atau JEMBATAN';
        }
        
        $validKondisi = ['BAIK', 'RUSAK RINGAN', 'RUSAK BERAT'];
        if (isset($data['kondisi']) && 
            !empty($data['kondisi']) && 
            !in_array(strtoupper($data['kondisi']), $validKondisi)) {
            $errors[] = 'Kondisi harus salah satu dari: ' . implode(', ', $validKondisi);
        }
        
        $numericFields = ['kuantitas', 'nilai_perolehan', 'nilai_penyusutan', 'nilai_buku', 'panjang', 'lebar', 'luas'];
        foreach ($numericFields as $field) {
            if (isset($data[$field]) && 
                !empty($data[$field]) && 
                !is_numeric($data[$field])) {
                $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' harus berupa angka';
            }
        }
        
        if (isset($data['tanggal_perolehan']) && 
            !empty($data['tanggal_perolehan'])) {
            $date = \DateTime::createFromFormat('Y-m-d', $data['tanggal_perolehan']);
            if (!$date || $date->format('Y-m-d') !== $data['tanggal_perolehan']) {
                $errors[] = 'Format tanggal perolehan tidak valid (Y-m-d)';
            }
        }
        
        return $errors;
    }
    
    public function validateKelompok($kelompok)
    {
        return $this->isValidKelompokJalanDanJembatan($kelompok);
    }
    
    public function getValidKelompok()
    {
        return ['JALAN', 'JEMBATAN'];
    }
    
    public function cleanImportData($data)
    {
        $cleaned = [];
        
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
        $cleaned['konstruksi'] = trim($data['konstruksi'] ?? '');
        
        $cleaned['nilai_perolehan'] = $this->safeFloat($data['nilai_perolehan'] ?? 0);
        $cleaned['nilai_penyusutan'] = $this->safeFloat($data['nilai_penyusutan'] ?? 0);
        $cleaned['nilai_buku'] = $this->safeFloat($data['nilai_buku'] ?? 0);
        $cleaned['panjang'] = $this->safeFloat($data['panjang'] ?? 0);
        $cleaned['lebar'] = $this->safeFloat($data['lebar'] ?? 0);
        $cleaned['luas'] = $this->safeFloat($data['luas'] ?? 0);
        
        $cleaned['tanggal_perolehan'] = !empty($data['tanggal_perolehan']) ? $data['tanggal_perolehan'] : null;
        
        $cleaned['kategori_utama'] = 'JALAN DAN JEMBATAN';
        $cleaned['kategori_detail'] = $this->mapKelompokToKategori($cleaned['kelompok']);
        
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