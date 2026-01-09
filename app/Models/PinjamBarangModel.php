<?php

namespace App\Models;

use CodeIgniter\Model;

class PinjamBarangModel extends Model
{
    protected $table = 'pinjam_barang';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    // Fields sesuai struktur database yang ada
    protected $allowedFields = [
        'barang_id',
        'user_id', 
        'nama_peminjam',
        'tanggal',
        'waktu_mulai',
        'waktu_selesai',
        'keperluan',
        'status',
        'keterangan',
        'keterangan_status',
        'verified_at',
        'verified_by',
        'tanggal_kembali',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    protected $validationRules = [
        'user_id' => 'required|integer',
        'barang_id' => 'required|integer',
        'nama_peminjam' => 'required|min_length[3]|max_length[255]',
        'tanggal' => 'required|valid_date',
        'keperluan' => 'required|min_length[5]',
        'status' => 'required|in_list[diajukan,disetujui,ditolak,dipinjam,proses_pengembalian,selesai]'
    ];

    protected $validationMessages = [
        'user_id' => [
            'required' => 'User ID harus diisi',
            'integer' => 'User ID harus berupa angka'
        ],
        'barang_id' => [
            'required' => 'Barang ID harus diisi',
            'integer' => 'Barang ID harus berupa angka'
        ],
        'nama_peminjam' => [
            'required' => 'Nama peminjam harus diisi',
            'min_length' => 'Nama peminjam minimal 3 karakter',
            'max_length' => 'Nama peminjam maksimal 255 karakter'
        ],
        'tanggal' => [
            'required' => 'Tanggal harus diisi',
            'valid_date' => 'Format tanggal tidak valid'
        ],
        'keperluan' => [
            'required' => 'Keperluan harus diisi',
            'min_length' => 'Keperluan minimal 5 karakter'
        ],
        'status' => [
            'required' => 'Status harus diisi',
            'in_list' => 'Status harus salah satu dari: diajukan, disetujui, ditolak, dipinjam, proses_pengembalian, selesai'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    protected $allowCallbacks = true;
    protected $beforeInsert = ['beforeInsert'];
    protected $beforeUpdate = ['beforeUpdate'];

    protected function beforeInsert(array $data)
    {
        return $data;
    }

    protected function beforeUpdate(array $data)
    {
        return $data;
    }

    /**
     * Get peminjaman dengan detail user dan barang
     */
    public function getPeminjamanWithDetails($limit = null, $status = null)
    {
        $this->select('pinjam_barang.*, 
                      users.fullname as nama_peminjam_user, 
                      users.email as email_peminjam,
                      komputer.nama as nama_barang,
                      komputer.merk,
                      komputer.type,
                      komputer.qr_code')
             ->join('users', 'users.id = pinjam_barang.user_id')
             ->join('komputer', 'komputer.id = pinjam_barang.barang_id')
             ->orderBy('pinjam_barang.created_at', 'DESC');

        if ($status) {
            $this->where('pinjam_barang.status', $status);
        }

        if ($limit) {
            $this->limit($limit);
        }

        return $this->findAll();
    }

    /**
     * Get peminjaman berdasarkan user
     */
    public function getPeminjamanByUser($userId, $limit = 10)
    {
        return $this->select('pinjam_barang.*, 
                             komputer.nama as nama_barang,
                             komputer.merk,
                             komputer.type,
                             komputer.qr_code')
                    ->join('komputer', 'komputer.id = pinjam_barang.barang_id')
                    ->where('pinjam_barang.user_id', $userId)
                    ->orderBy('pinjam_barang.created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Get peminjaman yang pending/diajukan
     */
    public function getPendingPeminjaman()
    {
        return $this->getPeminjamanWithDetails(null, 'diajukan');
    }

    /**
     * Get peminjaman yang sedang dipinjam
     */
    public function getDipinjamPeminjaman()
    {
        return $this->getPeminjamanWithDetails(null, 'dipinjam');
    }

    /**
     * Get peminjaman proses pengembalian
     */
    public function getProsesPengembalian()
    {
        return $this->getPeminjamanWithDetails(null, 'proses_pengembalian');
    }

    /**
     * Approve peminjaman
     */
    public function approvePeminjaman($id, $adminId, $keterangan = null)
    {
        $data = [
            'status' => 'disetujui',
            'verified_by' => $adminId,
            'verified_at' => date('Y-m-d H:i:s'),
            'keterangan_status' => $keterangan
        ];

        return $this->update($id, $data);
    }

    /**
     * Reject peminjaman
     */
    public function rejectPeminjaman($id, $adminId, $keterangan)
    {
        $data = [
            'status' => 'ditolak',
            'verified_by' => $adminId,
            'verified_at' => date('Y-m-d H:i:s'),
            'keterangan_status' => $keterangan
        ];

        return $this->update($id, $data);
    }

    /**
     * Proses pengembalian
     */
    public function processReturn($id, $adminId, $keterangan = null)
    {
        $data = [
            'status' => 'selesai',
            'tanggal_kembali' => date('Y-m-d H:i:s'),
            'verified_by' => $adminId,
            'keterangan_status' => $keterangan
        ];

        return $this->update($id, $data);
    }

    /**
     * Get statistik peminjaman
     */
    public function getStatistik()
    {
        $stats = [
            'total' => $this->countAll(),
            'pending' => $this->where('status', 'diajukan')->countAllResults(false),
            'approved' => $this->where('status', 'disetujui')->countAllResults(false),
            'dipinjam' => $this->where('status', 'dipinjam')->countAllResults(false),
            'proses_pengembalian' => $this->where('status', 'proses_pengembalian')->countAllResults(false),
            'selesai' => $this->where('status', 'selesai')->countAllResults(false),
            'ditolak' => $this->where('status', 'ditolak')->countAllResults(false)
        ];

        return $stats;
    }

    /**
     * Get peminjaman yang overdue (lewat waktu selesai)
     */
    public function getOverduePeminjaman()
    {
        $today = date('Y-m-d');
        
        return $this->select('pinjam_barang.*, 
                             users.fullname as nama_peminjam_user, 
                             users.email as email_peminjam,
                             komputer.nama as nama_barang,
                             komputer.merk,
                             komputer.type,
                             komputer.qr_code')
                    ->join('users', 'users.id = pinjam_barang.user_id')
                    ->join('komputer', 'komputer.id = pinjam_barang.barang_id')
                    ->where('pinjam_barang.status', 'dipinjam')
                    ->where('pinjam_barang.tanggal <', $today)
                    ->orderBy('pinjam_barang.created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Check konflik peminjaman berdasarkan tanggal dan waktu
     */
    public function checkKonflikPeminjaman($barangId, $tanggal, $waktuMulai, $waktuSelesai, $excludeId = null)
    {
        $this->where('barang_id', $barangId)
             ->where('tanggal', $tanggal)
             ->whereIn('status', ['diajukan', 'disetujui', 'dipinjam'])
             ->groupStart()
                ->groupStart()
                    ->where('waktu_mulai <=', $waktuMulai)
                    ->where('waktu_selesai >', $waktuMulai)
                ->groupEnd()
                ->orGroupStart()
                    ->where('waktu_mulai <', $waktuSelesai)
                    ->where('waktu_selesai >=', $waktuSelesai)
                ->groupEnd()
                ->orGroupStart()
                    ->where('waktu_mulai >=', $waktuMulai)
                    ->where('waktu_selesai <=', $waktuSelesai)
                ->groupEnd()
             ->groupEnd();

        if ($excludeId) {
            $this->where('id !=', $excludeId);
        }

        return $this->first();
    }
}