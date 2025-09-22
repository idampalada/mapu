<?php
// =================================================================
// TANAMAN MODEL - app/Models/TanamanModel.php
// =================================================================

namespace App\Models;
use CodeIgniter\Model;

class TanamanModel extends Model
{
    protected $table = 'tanaman';
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
        'kelompok', // Selalu "TANAMAN"
        'sub_kelompok', // Pohon Hias, Tanaman Buah, dll
        'lokasi_tanam',
        'umur_tanaman',
        'jenis_tanaman',
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
        'sub_kelompok' => 'permit_empty|max_length[100]',
        'nup' => 'permit_empty|max_length[100]',
        'merk' => 'permit_empty|max_length[100]',
        'kondisi' => 'permit_empty|max_length[50]',
        'kuantitas' => 'permit_empty|integer',
        'status_penggunaan' => 'permit_empty|max_length[100]',
        'nilai_perolehan' => 'permit_empty|decimal',
        'nilai_penyusutan' => 'permit_empty|decimal',
        'nilai_buku' => 'permit_empty|decimal',
        'lokasi_tanam' => 'permit_empty|max_length[255]',
        'umur_tanaman' => 'permit_empty|max_length[50]',
        'jenis_tanaman' => 'permit_empty|max_length[100]',
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
        ]
    ];
    
    protected $skipValidation = false;
    protected $cleanValidationRules = true;
    
    // Helper method - Get valid sub kelompok
    public function getValidSubKelompok()
    {
        return [
            'POHON HIAS',
            'TANAMAN BUAH',
            'TANAMAN SAYURAN',
            'TANAMAN OBAT',
            'TANAMAN REMPAH',
            'POHON PELINDUNG',
            'TANAMAN PAKAN TERNAK',
            'TANAMAN INDUSTRI',
            'TANAMAN LAINNYA'
        ];
    }
    
    // Get statistics per sub kelompok
    public function getStatistikSubKelompok()
    {
        $builder = $this->builder();
        
        return $builder->select('sub_kelompok, COUNT(*) as jumlah')
                      ->where('kelompok', 'TANAMAN')
                      ->groupBy('sub_kelompok')
                      ->orderBy('sub_kelompok', 'ASC')
                      ->get()
                      ->getResultArray();
    }
    
    // Get data by sub kelompok
    public function getBySubKelompok($subKelompok, $limit = null, $offset = 0)
    {
        $builder = $this->builder();
        
        $builder->where('kelompok', 'TANAMAN');
        $builder->where('UPPER(sub_kelompok)', strtoupper($subKelompok));
        
        if ($limit) {
            $builder->limit($limit, $offset);
        }
        
        return $builder->orderBy('kode_barang', 'ASC')
                      ->get()
                      ->getResultArray();
    }
    
    // Search functionality
    public function searchTanaman($searchTerm = '', $subKelompok = '', $limit = 100, $offset = 0)
    {
        $builder = $this->builder();
        
        // Selalu filter kelompok TANAMAN
        $builder->where('kelompok', 'TANAMAN');
        
        // Filter berdasarkan sub kelompok jika ada
        if (!empty($subKelompok)) {
            $builder->where('UPPER(sub_kelompok)', strtoupper($subKelompok));
        }
        
        // Filter pencarian jika ada
        if (!empty($searchTerm)) {
            $searchTerm = $this->db->escapeLikeString($searchTerm);
            
            $builder->groupStart()
                ->like('kode_barang', $searchTerm)
                ->orLike('nama_barang', $searchTerm)
                ->orLike('sub_kelompok', $searchTerm)
                ->orLike('jenis_tanaman', $searchTerm)
                ->orLike('lokasi_tanam', $searchTerm)
                ->orLike('nup', $searchTerm)
                ->groupEnd();
        }
        
        return $builder->orderBy('kode_barang', 'ASC')
                      ->limit($limit, $offset)
                      ->get()
                      ->getResultArray();
    }
    
    // Safe float conversion
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