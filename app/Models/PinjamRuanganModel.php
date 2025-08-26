<?php

namespace App\Models;

use CodeIgniter\Model;

class PinjamRuanganModel extends Model
{
    const STATUS_PENDING = 'pending';
    const STATUS_DISETUJUI = 'disetujui';
    const STATUS_DITOLAK = 'ditolak';
    const STATUS_SELESAI = 'selesai';

    protected $table = 'pinjam_ruangan';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'user_id',
        'ruangan_id',
        'nama_penanggung_jawab',
        'unit_organisasi',
        'tanggal',
        'waktu_mulai',
        'waktu_selesai',
        'jumlah_peserta',
        'keperluan',
        'surat_permohonan',
        'dokumen_tambahan',
        'status',
        'keterangan',
        'keterangan_status',
        'verified_at',
        'verified_by',
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
        'ruangan_id' => 'required',
        'nama_penanggung_jawab' => 'required',
        'unit_organisasi' => 'required|in_list[Setjen,Itjen,Ditjen Sumber Daya Air,Ditjen Bina Marga,Ditjen Cipta Karya,Ditjen Perumahan,Ditjen Bina Konstruksi,Ditjen Pembiayaan Infrastruktur Pekerjaan Umum dan Perumahan,BPIW,BPSDM,BPJT]',
        'tanggal' => 'required|valid_date',
        'waktu_mulai' => 'required',
        'waktu_selesai' => 'required',
        'jumlah_peserta' => 'required|numeric|greater_than[0]',
        'keperluan' => 'required',
        'surat_permohonan' => 'uploaded[surat_permohonan]|mime_in[surat_permohonan,application/pdf]|max_size[surat_permohonan,2048]'
    ];

    protected $validationMessages = [
        'unit_organisasi' => [
            'in_list' => 'Unit organisasi harus dipilih dari daftar yang tersedia'
        ],
        'surat_permohonan' => [
            'uploaded' => 'Surat permohonan wajib diunggah',
            'mime_in' => 'File surat permohonan harus berformat PDF',
            'max_size' => 'Ukuran file tidak boleh lebih dari 2MB'
        ]
    ];


    // 1. Check ruangan availability (converted to Query Builder)
    public function checkRuanganAvailability($ruanganId, $tanggal, $waktuMulai, $waktuSelesai, $excludeId = null)
{
    $builder = $this->builder();
    $builder->where('ruangan_id', $ruanganId)
        ->where('tanggal', $tanggal)
        ->whereIn('status', [self::STATUS_DISETUJUI, self::STATUS_PENDING]) // Include pending juga
        ->where('deleted_at', null);

    // PERBAIKAN: Cek konflik waktu dengan logika yang lebih ketat
    $builder->groupStart()
                // Kondisi 1: Waktu mulai baru berada dalam range booking yang ada
                ->where('waktu_mulai <=', $waktuMulai)
                ->where('waktu_selesai >', $waktuMulai)
            ->groupEnd()
            ->orGroupStart()
                // Kondisi 2: Waktu selesai baru berada dalam range booking yang ada  
                ->where('waktu_mulai <', $waktuSelesai)
                ->where('waktu_selesai >=', $waktuSelesai)
                ->where('ruangan_id', $ruanganId)
                ->where('tanggal', $tanggal)
                ->whereIn('status', [self::STATUS_DISETUJUI, self::STATUS_PENDING])
                ->where('deleted_at', null)
            ->groupEnd()
            ->orGroupStart()
                // Kondisi 3: Booking baru menutupi booking yang ada sepenuhnya
                ->where('waktu_mulai >=', $waktuMulai)
                ->where('waktu_selesai <=', $waktuSelesai)
                ->where('ruangan_id', $ruanganId)
                ->where('tanggal', $tanggal)
                ->whereIn('status', [self::STATUS_DISETUJUI, self::STATUS_PENDING])
                ->where('deleted_at', null)
            ->groupEnd();

    if ($excludeId) {
        $builder->where('id !=', $excludeId);
    }

    $result = $builder->get()->getRowArray();
    return !$result; // Return true jika tidak ada konflik
}
public function getBookingsByDate($ruanganId, $tanggal)
{
    $builder = $this->builder();
    $result = $builder->select('
            pinjam_ruangan.id,
            pinjam_ruangan.waktu_mulai,
            pinjam_ruangan.waktu_selesai,
            pinjam_ruangan.keperluan,
            pinjam_ruangan.nama_penanggung_jawab,
            pinjam_ruangan.unit_organisasi,
            pinjam_ruangan.status,
            pinjam_ruangan.user_id
        ')
        ->where('ruangan_id', $ruanganId)
        ->where('tanggal', $tanggal)
        ->whereIn('status', [self::STATUS_DISETUJUI, self::STATUS_PENDING])
        ->where('deleted_at', null)
        ->orderBy('waktu_mulai', 'ASC')
        ->get()
        ->getResultArray();

    // PERBAIKAN: Format waktu ke HH:MM
    foreach ($result as &$booking) {
        if (strlen($booking['waktu_mulai']) > 5) {
            $booking['waktu_mulai'] = substr($booking['waktu_mulai'], 0, 5);
        }
        if (strlen($booking['waktu_selesai']) > 5) {
            $booking['waktu_selesai'] = substr($booking['waktu_selesai'], 0, 5);
        }
    }

    return $result;
}
public function validateTimeSlot($waktuMulai, $waktuSelesai)
{
    // Cek format waktu
    if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $waktuMulai) ||
        !preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $waktuSelesai)) {
        return ['valid' => false, 'message' => 'Format waktu tidak valid'];
    }

    // Cek apakah waktu selesai setelah waktu mulai
    if ($waktuSelesai <= $waktuMulai) {
        return ['valid' => false, 'message' => 'Waktu selesai harus setelah waktu mulai'];
    }

    // Cek jam operasional (07:00 - 17:30)
    $minTime = '07:00';
    $maxTime = '17:30';
    
    if ($waktuMulai < $minTime || $waktuSelesai > $maxTime) {
        return ['valid' => false, 'message' => 'Waktu booking harus antara 07:00 - 17:30'];
    }

    // Cek durasi minimum (30 menit)
    $start = new \DateTime($waktuMulai);
    $end = new \DateTime($waktuSelesai);
    $diff = $start->diff($end);
    $diffInMinutes = ($diff->h * 60) + $diff->i;
    
    if ($diffInMinutes < 30) {
        return ['valid' => false, 'message' => 'Durasi minimum booking adalah 30 menit'];
    }

    return ['valid' => true, 'message' => 'Waktu valid'];
}

    // 2. Get peminjaman history (converted to Query Builder)
    public function getPeminjamanHistory($userId = null)
    {
        $builder = $this->builder();
        $builder->select('
                pinjam_ruangan.*, 
                ruangan.nama_ruangan, 
                ruangan.lokasi,
                ruangan.kapasitas
            ')
            ->join('ruangan', 'ruangan.id = pinjam_ruangan.ruangan_id');

        if ($userId) {
            $builder->where('pinjam_ruangan.user_id', $userId);
        }

        return $builder->where('pinjam_ruangan.deleted_at', null)
            ->orderBy('pinjam_ruangan.created_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    // 3. Get pending peminjaman (converted to Query Builder)
    public function getPendingPeminjaman()
    {
        $builder = $this->builder();
        return $builder->select('
                pinjam_ruangan.*, 
                ruangan.nama_ruangan, 
                ruangan.lokasi,
                ruangan.kapasitas
            ')
            ->join('ruangan', 'ruangan.id = pinjam_ruangan.ruangan_id')
            ->where('pinjam_ruangan.status', self::STATUS_PENDING)
            ->where('pinjam_ruangan.deleted_at', null)
            ->orderBy('pinjam_ruangan.created_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    // 4. Get full history (converted to Query Builder)
    public function getFullHistory($ruanganId = null)
    {
        $builder = $this->builder();
        $builder->select('
                pinjam_ruangan.*, 
                ruangan.nama_ruangan, 
                ruangan.lokasi,
                ruangan.kapasitas
            ')
            ->join('ruangan', 'ruangan.id = pinjam_ruangan.ruangan_id');

        if ($ruanganId) {
            $builder->where('pinjam_ruangan.ruangan_id', $ruanganId);
        }

        return $builder->orderBy('pinjam_ruangan.created_at', 'DESC')
            ->get()
            ->getResultArray();
    }
}