<?php
namespace App\Models;
use CodeIgniter\Model;

class TuguTitikKontrolModel extends Model
{
    protected $table = 'tugu_titik_kontrol';
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
        'jumlah_lantai', 'tinggi_bangunan', 'koordinat_x', 'koordinat_y', 'koordinat_z',
        'no_dana', 'tgl_dana', 'dari', 'asl_perlh', 'tgl_perlh',
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

    // Get valid kelompok untuk tugu titik kontrol
    public function getValidKelompok()
    {
        return [
            'TUGU BATAS',
            'TANDA BATAS',
            'TUGU TITIK KONTROL',
            'TUGU/TANDA BATAS' // Yang utama dari API
        ];
    }

    // Cek apakah kelompok valid
    public function isValidKelompokTuguTitikKontrol($kelompok)
    {
        return in_array(strtoupper($kelompok), $this->getValidKelompok());
    }

    // Mapping kelompok dari API ke kategori detail - SEMUA ke "Tugu/Tanda Batas"
    public function mapKelompokToKategori($kelompok_api) 
    {
        // Semua kelompok masuk ke kategori detail yang sama
        return 'Tugu/Tanda Batas';
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
    public function searchTuguTitikKontrol($searchTerm = '', $kelompok = '', $limit = 100, $offset = 0)
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
        $cleaned['tinggi_bangunan'] = $this->safeFloat($data['tinggi_bangunan'] ?? 0);
        $cleaned['jumlah_lantai'] = intval($data['jumlah_lantai'] ?? 1);
        
        // Handle koordinat
        $cleaned['koordinat_x'] = $this->safeFloat($data['koordinat_x'] ?? 0);
        $cleaned['koordinat_y'] = $this->safeFloat($data['koordinat_y'] ?? 0);
        $cleaned['koordinat_z'] = $this->safeFloat($data['koordinat_z'] ?? 0);
        $cleaned['latitude'] = $this->safeFloat($data['latitude'] ?? 0);
        $cleaned['longitude'] = $this->safeFloat($data['longitude'] ?? 0);
        
        // Handle date
        $cleaned['tanggal_perolehan'] = !empty($data['tanggal_perolehan']) ? $data['tanggal_perolehan'] : null;
        
        // Set kategori
        $cleaned['kategori_utama'] = 'GEDUNG DAN BANGUNAN - TUGU TITIK KONTROL/PASTI';
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
        $data['tugu_batas'] = $this->countByKelompok('TUGU BATAS');
        $data['tanda_batas'] = $this->countByKelompok('TANDA BATAS');
        $data['tugu_titik_kontrol'] = $this->countByKelompok('TUGU TITIK KONTROL');
        $data['tugu_tanda_batas'] = $this->countByKelompok('TUGU/TANDA BATAS');
        
        return $data;
    }
}