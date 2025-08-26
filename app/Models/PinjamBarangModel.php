<?php

namespace App\Models;

use CodeIgniter\Model;

class PinjamBarangModel extends Model
{
    const STATUS_DIAJUKAN = 'diajukan';
    const STATUS_DISETUJUI = 'disetujui';
    const STATUS_DITOLAK = 'ditolak';
    const STATUS_SELESAI = 'selesai';

    protected $table            = 'pinjam_barang';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;

    protected $allowedFields    = [
        'user_id',
        'barang_id',
        'nama_peminjam',
        'tanggal',
        'waktu_mulai',
        'waktu_selesai',
        'tanggal_kembali',
        'keperluan',
        'status',
        'keterangan',
        'keterangan_status',
        'verified_at',
        'verified_by',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';

    protected $validationRules = [
        'user_id'         => 'required|integer',
        'barang_id'       => 'required|integer',
        'nama_peminjam'   => 'required|string|max_length[255]',
        'tanggal'         => 'required|valid_date',
        'waktu_mulai'     => 'required|regex_match[/^\d{2}:\d{2}$/]',
        'waktu_selesai'   => 'required|regex_match[/^\d{2}:\d{2}$/]',
        'keperluan'       => 'required|string',
        'status' => 'required|in_list[diajukan,disetujui,ditolak,proses_pengembalian,selesai]'
    ];

    protected $validationMessages = [
        'user_id' => [
            'required' => 'User wajib diisi.'
        ],
        'barang_id' => [
            'required' => 'Barang wajib dipilih.'
        ],
        'nama_peminjam' => [
            'required' => 'Nama peminjam wajib diisi.',
            'max_length' => 'Nama peminjam maksimal 255 karakter.'
        ],
        'tanggal' => [
            'required' => 'Tanggal mulai harus diisi.',
            'valid_date' => 'Format tanggal mulai tidak valid.'
        ],
        'waktu_mulai' => [
            'required' => 'Waktu mulai harus diisi.',
            'regex_match' => 'Format waktu mulai tidak valid (HH:MM).'
        ],
        'waktu_selesai' => [
            'required' => 'Waktu selesai harus diisi.',
            'regex_match' => 'Format waktu selesai tidak valid (HH:MM).'
        ],
        'keperluan' => [
            'required' => 'Keperluan peminjaman wajib diisi.'
        ],
        'status' => [
            'required' => 'Status wajib diisi.',
            'in_list' => 'Status harus diajukan, disetujui, atau ditolak.'
        ]
    ];

    public function checkBarangAvailability($barangId, $tanggal, $waktuMulai, $waktuSelesai, $excludeId = null)
    {
        $tanggalWaktuMulai = $tanggal . ' ' . $waktuMulai;
        $tanggalWaktuSelesai = $tanggal . ' ' . $waktuSelesai;

        $builder = $this->where('barang_id', $barangId)
            ->where('status', self::STATUS_DISETUJUI)
            ->where('deleted_at', null)
            ->where('tanggal', $tanggal)
            ->groupStart()
                ->where("CONCAT(tanggal, ' ', waktu_mulai) <=", $tanggalWaktuSelesai)
                ->where("CONCAT(tanggal, ' ', waktu_selesai) >=", $tanggalWaktuMulai)
            ->groupEnd();

        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }

        return !$builder->first();
    }

    public function getPeminjamanHistory($userId = null)
    {
        $builder = $this->select('pinjam_barang.*, barang.nama_barang, barang.kategori, barang.lokasi')
                         ->join('barang', 'barang.id = pinjam_barang.barang_id');

        if ($userId) {
            $builder->where('pinjam_barang.user_id', $userId);
        }

        return $builder->where('pinjam_barang.deleted_at', null)
                       ->orderBy('pinjam_barang.created_at', 'DESC')
                       ->findAll();
    }

    public function getPendingPeminjaman()
    {
        return $this->select('pinjam_barang.*, barang.nama_barang, barang.kategori, barang.lokasi')
                    ->join('barang', 'barang.id = pinjam_barang.barang_id')
                    ->where('pinjam_barang.status', self::STATUS_DIAJUKAN)
                    ->where('pinjam_barang.deleted_at', null)
                    ->orderBy('pinjam_barang.created_at', 'DESC')
                    ->findAll();
    }

    public function getFullHistory($barangId = null)
    {
        $builder = $this->select('pinjam_barang.*, barang.nama_barang, barang.kategori, barang.lokasi')
                         ->join('barang', 'barang.id = pinjam_barang.barang_id');

        if ($barangId) {
            $builder->where('pinjam_barang.barang_id', $barangId);
        }

        return $builder->orderBy('pinjam_barang.created_at', 'DESC')->findAll();
    }
    public function getPendingPengembalian()
    {
        return $this->select('pinjam_barang.*, barang.nama_barang, barang.kategori, barang.lokasi')
                    ->join('barang', 'barang.id = pinjam_barang.barang_id')
                    ->where('pinjam_barang.status', 'proses_pengembalian')
                    ->where('pinjam_barang.deleted_at', null)
                    ->orderBy('pinjam_barang.updated_at', 'DESC')
                    ->findAll();
    }
    

}
