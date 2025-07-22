<?php

namespace App\Models;

use CodeIgniter\Model;

class PinjamModel extends Model
{
    const STATUS_PENDING = 'pending';
    const STATUS_DISETUJUI = 'disetujui';
    const STATUS_DITOLAK = 'ditolak';
    const STATUS_SELESAI = 'selesai';

    protected $table = 'pinjam';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'user_id',
        'kode_barang',
        'kendaraan_id',
        'nama_penanggung_jawab',
        'nip_nrp',
        'pangkat_golongan',
        'jabatan',
        'unit_organisasi',
        'surat_permohonan',
        'surat_jalan_admin',
        'dokumen_tambahan',
        'pengemudi',
        'no_hp',
        'tanggal_pinjam',
        'tanggal_kembali',
        'urusan_kedinasan',
        'status',
        'is_returned',
        'keterangan',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    protected $validationRules = [
        'user_id' => 'required',
        'kode_barang' => 'required',
        'kendaraan_id' => 'required'
        // 'status' => 'in_list[pending,disetujui,ditolak]'
    ];
    // protected $casts = [
    //     'is_returned' => 'boolean'
    // ];

    // ============ ORIGINAL FUNCTIONS - 100% QUERY BUILDER ============

    // 1. Update return status (already Query Builder, kept the same)
    public function updateReturnStatus($id, $isReturned = true)
    {
        $builder = $this->db->table($this->table);
        return $builder->where('id', $id)
            ->set('is_returned', (bool) $isReturned, true)
            ->update();
    }

    // 2. Get peminjaman history (converted to Query Builder)
    public function getPeminjamanHistory($userId = null)
    {
        $builder = $this->builder();
        $builder->select('pinjam.*, assets.merk, assets.no_polisi')
            ->join('assets', 'assets.id = pinjam.kendaraan_id');

        if ($userId) {
            $builder->where('pinjam.user_id', $userId);
        }

        return $builder->orderBy('pinjam.created_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    // 3. Get pending peminjaman (converted to Query Builder)
    public function getPendingPeminjaman()
    {
        $builder = $this->builder();
        return $builder->select('pinjam.*, assets.merk, assets.no_polisi')
            ->join('assets', 'assets.id = pinjam.kendaraan_id')
            ->where('pinjam.status', self::STATUS_PENDING)
            ->where('pinjam.deleted_at', null)
            ->orderBy('pinjam.created_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    // 4. Get active peminjaman (converted to Query Builder)
    public function getActivePeminjaman($kendaraanId)
    {
        $builder = $this->builder();
        return $builder->where('kendaraan_id', $kendaraanId)
            ->whereIn('status', [self::STATUS_DISETUJUI, self::STATUS_PENDING])
            ->where('is_returned', false)
            ->where('deleted_at', null)
            ->get()
            ->getRowArray();
    }

    // 5. Get full history (converted to Query Builder)
    public function getFullHistory($kendaraanId = null)
    {
        $builder = $this->builder();
        $builder->select('
                pinjam.*, 
                assets.merk, 
                assets.no_polisi,
                assets.status_pinjam
            ')
            ->join('assets', 'assets.id = pinjam.kendaraan_id');

        if ($kendaraanId) {
            $builder->where('pinjam.kendaraan_id', $kendaraanId);
        }

        return $builder->orderBy('pinjam.created_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    // 6. Can borrow check (converted to Query Builder)
    public function canBorrow($kendaraanId)
    {
        $builder = $this->builder();
        $result = $builder->where('kendaraan_id', $kendaraanId)
            ->whereIn('status', [self::STATUS_PENDING, self::STATUS_DISETUJUI])
            ->where('is_returned', false)
            ->where('deleted_at', null)
            ->get()
            ->getRowArray();
            
        return !$result;
    }

    // 7. Get active user peminjaman (converted to Query Builder)
    public function getActiveUserPeminjaman($userId)
    {
        $builder = $this->builder();
        return $builder->where('user_id', $userId)
            ->whereIn('status', [self::STATUS_DISETUJUI, self::STATUS_PENDING])
            ->where('is_returned', false)
            ->where('deleted_at', null)
            ->get()
            ->getResultArray();
    }
    
    // public function updateStatus($id, $isReturned = true)
    // {
    //     if (!$this->find($id)) {
    //         return false;
    //     }

    //     $builder = $this->db->table($this->table);
    //     return $builder->where('id', $id)
    //         ->set(['is_returned' => $isReturned])
    //         ->update();
    // }
}