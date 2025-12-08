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
    'no_ktp',
    'alamat_rumah',
    'pangkat_golongan',
    'jabatan',
    'unit_organisasi',
    'surat_permohonan',
    'surat_jalan_admin',
    'surat_penanggung_jawab', // Tambahkan kolom ini
    'nomor_surat', // Tambahkan kolom ini
    'tanggal_surat', // Tambahkan kolom ini
    'tempat_surat', // Tambahkan kolom ini
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
    'deleted_at',
    'nama_penanggung_jawab_kendaraan',
    'nip_penanggung_jawab_kendaraan',
    'nama_kepala_satuan_kerja',
    'nip_kepala_satuan_kerja',
            'is_tte_signed',
        'tte_signed_at',
        'tte_signer_nik',
        // TAMBAHKAN KOLOM TTE KDF BARU:
        'is_kdf_tte_signed',         // ← BARU
        'kdf_tte_signed_at',         // ← BARU
        'kdf_tte_signer_nik'        // ← BARU
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
    public function getPeminjamanWithTTEStatus($id = null)
    {
        $builder = $this->select('pinjam.*, aset.merk, aset.no_polisi, aset.kategori_id')
                        ->join('aset', 'aset.id = pinjam.kendaraan_id', 'left');
        
        if ($id) {
            return $builder->where('pinjam.id', $id)->first();
        }
        
        return $builder->findAll();
    }

    /**
     * Get KDF documents that need TTE signing
     */
    public function getKDFNeedsTTE()
    {
        return $this->select('pinjam.*, aset.merk, aset.no_polisi, aset.kategori_id')
                    ->join('aset', 'aset.id = pinjam.kendaraan_id', 'left')
                    ->where('aset.kategori_id', 'KDF')
                    ->where('pinjam.surat_penanggung_jawab IS NOT NULL')
                    ->where('pinjam.is_kdf_tte_signed', 0)
                    ->findAll();
    }

    /**
     * Get KDF documents that are TTE signed
     */
    public function getKDFTTESigned()
    {
        return $this->select('pinjam.*, aset.merk, aset.no_polisi, aset.kategori_id')
                    ->join('aset', 'aset.id = pinjam.kendaraan_id', 'left')
                    ->where('aset.kategori_id', 'KDF')
                    ->where('pinjam.is_kdf_tte_signed', 1)
                    ->orderBy('pinjam.kdf_tte_signed_at', 'DESC')
                    ->findAll();
    }

    /**
     * Update TTE KDF status
     */
    public function updateKDFTTEStatus($pinjamId, $signerNik)
    {
        return $this->update($pinjamId, [
            'is_kdf_tte_signed' => 1,
            'kdf_tte_signed_at' => date('Y-m-d H:i:s'),
            'kdf_tte_signer_nik' => $signerNik,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }
}





















