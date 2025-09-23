<?php
namespace App\Models;
use CodeIgniter\Model;

class BangunanGedungModel extends Model
{
    protected $table = 'bangunan_gedung';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'tgl_tarik', 'kode_kl', 'nama_kl', 'kode_kpknl', 'nama_kpknl',
        'kode_satker', 'nama_satker', 'kode_sub_satker', 'nama_sub_satker',
        'kode_barang', 'nama_barang', 'nilai_perolehan_pertama', 'nilai_mutasi',
        'nilai_perolehan', 'nilai_penyusutan', 'nilai_buku', 'kode_pos', 'jalan',
        'nup', 'no_kib', 'tanggal_perolehan', 'tgl_buku', 'tgl_rekam', 
        'tgl_rekam_pertama', 'kondisi', 'merk', 'kuantitas', 'sbsk', 'optimalisasi',
        'kd_kabkota', 'nm_kabkota', 'kode_provinsi', 'uraian_provinsi',
        'latitude', 'longitude', 'luas_bangunan', 'luas_dasar_bangunan', 
        'jumlah_lantai', 'no_dana', 'tgl_dana', 'dari', 'asl_perlh', 'tgl_perlh',
        'no_sk_psp', 'tgl_sk_psp', 'jumlah_foto', 'jumlah_kib', 'status_pengelolaan',
        'jenis_sertifikat', 'no_dok_kepemilikan', 'dok_kepemilikan', 
        'jns_dok_kepemilikan', 'dokumen', 'status_penggunaan', 'jenis_pengguna',
        'kd_satker_pengguna', 'ur_satker_pengguna', 'alamat_satker_pengguna',
        'status_sbsn', 'status_idle', 'kd_brg_tanah', 'ur_brg_tanah', 'nup_tanah',
        'kd_satker_tanah', 'nm_satker_tanah', 'tot_pegawai', 'dihentikan_yn',
        'kelompok', 'sub_kelompok', 'kategori_utama', 'kategori_detail',
        'created_at', 'updated_at'
    ];
    
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    protected $validationRules = [
        'kode_barang' => 'required|max_length[150]',
        'nama_barang' => 'required|max_length[255]',
        'kelompok' => 'required|max_length[200]'
    ];
    
    protected $validationMessages = [
        'kode_barang' => [
            'required' => 'Kode barang harus diisi',
            'max_length' => 'Kode barang maksimal 150 karakter'
        ],
        'nama_barang' => [
            'required' => 'Nama barang harus diisi',
            'max_length' => 'Nama barang maksimal 255 karakter'
        ],
        'kelompok' => [
            'required' => 'Kelompok harus diisi',
            'max_length' => 'Kelompok maksimal 200 karakter'
        ]
    ];
    
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Get valid kelompok untuk bangunan gedung
    public function getValidKelompok()
    {
        return [
            'BANGUNAN GEDUNG TEMPAT KERJA',
            'BANGUNAN GEDUNG TEMPAT TINGGAL'
        ];
    }

    // Cek apakah kelompok valid
    public function isValidKelompokBangunanGedung($kelompok)
    {
        return in_array(strtoupper($kelompok), $this->getValidKelompok());
    }

    // Mapping kelompok dari API ke kategori detail
    public function mapKelompokToKategori($kelompok_api) 
    {
        $mapping = [
            'BANGUNAN GEDUNG TEMPAT KERJA' => 'Bangunan Gedung Tempat Kerja',
            'BANGUNAN GEDUNG TEMPAT TINGGAL' => 'Bangunan Gedung Tempat Tinggal'
        ];
        
        return $mapping[strtoupper($kelompok_api)] ?? null;
    }

    // Get data by kelompok
    public function getByKelompok($kelompok, $limit = null, $offset = 0)
    {
        $builder = $this->builder();
        $builder->where('UPPER(kelompok)', strtoupper($kelompok));
        
        if ($limit) {
            $builder->limit($limit, $offset);
        }
        
        return $builder->orderBy('kode_barang', 'ASC')->get()->getResultArray();
    }

    // Count by kelompok
    public function countByKelompok($kelompok)
    {
        $builder = $this->builder();
        return $builder->where('UPPER(kelompok)', strtoupper($kelompok))->countAllResults();
    }

    // Search dengan filter kelompok
    public function searchBangunanGedung($searchTerm = '', $kelompok = '', $limit = 100, $offset = 0)
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
                ->groupEnd();
        }
        
        return $builder->orderBy('kode_barang', 'ASC')
                      ->limit($limit, $offset)
                      ->get()
                      ->getResultArray();
    }

    // Clean data untuk import
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
        $cleaned['nama_satker'] = trim($data['nama_satker'] ?? '');
        
        // Handle numeric fields
        $cleaned['nilai_perolehan'] = $this->safeFloat($data['nilai_perolehan'] ?? 0);
        $cleaned['nilai_buku'] = $this->safeFloat($data['nilai_buku'] ?? 0);
        $cleaned['luas_dasar_bangunan'] = $this->safeFloat($data['luas_dasar_bangunan'] ?? 0);
        $cleaned['luas_bangunan'] = $this->safeFloat($data['luas_bangunan'] ?? 0);
        $cleaned['jumlah_lantai'] = intval($data['jumlah_lantai'] ?? 1);
        
        // Handle date
        $cleaned['tanggal_perolehan'] = !empty($data['tanggal_perolehan']) ? $data['tanggal_perolehan'] : null;
        
        // Set kategori
        $cleaned['kategori_utama'] = 'GEDUNG DAN BANGUNAN';
        $cleaned['kategori_detail'] = $this->mapKelompokToKategori($cleaned['kelompok']);
        
        return $cleaned;
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

    // Get dashboard data
    public function getDashboardData()
    {
        $data = [];
        $data['total_all'] = $this->countAllResults();
        $data['tempat_kerja'] = $this->countByKelompok('BANGUNAN GEDUNG TEMPAT KERJA');
        $data['tempat_tinggal'] = $this->countByKelompok('BANGUNAN GEDUNG TEMPAT TINGGAL');
        
        return $data;
    }
}