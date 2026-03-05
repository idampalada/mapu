<?php
// App/Models/BookingRuanganModel.php - FIXED VERSION

namespace App\Models;

use CodeIgniter\Model;

class BookingRuanganModel extends Model
{
    protected $table = 'booking_ruangan';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'ruangan_id', 'user_id', 'tanggal', 'waktu_mulai', 'waktu_selesai',
        'keperluan', 'nama_penanggung_jawab', 'nomor_hp_penanggung_jawab', 'unit_organisasi', 'unit_kerja',
        'jumlah_peserta', 'status'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    protected $validationRules = [
        'ruangan_id' => 'required|integer',
        'user_id' => 'required|integer',
        'tanggal' => 'required|valid_date',
        'waktu_mulai' => 'required',
        'waktu_selesai' => 'required',
        'keperluan' => 'required|min_length[5]',
        'nama_penanggung_jawab' => 'required|min_length[3]',
        'nomor_hp_penanggung_jawab' => 'required|regex_match[/^[0-9]{10,15}$/]',
        'unit_organisasi' => 'required|min_length[3]',
        'jumlah_peserta' => 'required|integer|greater_than[0]'
    ];
    
    protected $validationMessages = [
        'ruangan_id' => [
            'required' => 'Ruangan harus dipilih',
            'integer' => 'ID ruangan tidak valid'
        ],
        'tanggal' => [
            'required' => 'Tanggal harus diisi',
            'valid_date' => 'Format tanggal tidak valid'
        ],
        'waktu_mulai' => [
            'required' => 'Waktu mulai harus diisi'
        ],
        'waktu_selesai' => [
            'required' => 'Waktu selesai harus diisi'  
        ],
        'keperluan' => [
            'required' => 'Keperluan harus diisi',
            'min_length' => 'Keperluan minimal 5 karakter'
        ],
        'nama_penanggung_jawab' => [
            'required' => 'Nama penanggung jawab harus diisi',
            'min_length' => 'Nama minimal 3 karakter'
        ],
                'nomor_hp_penanggung_jawab' => [
            'required' => 'Nomor HP Penanggung Jawab wajib diisi',
            'regex_match' => 'Format Nomor HP tidak valid (10-15 digit angka)'
        ],
        'unit_organisasi' => [
            'required' => 'Unit organisasi harus diisi',
            'min_length' => 'Unit organisasi minimal 3 karakter'
        ],
        'jumlah_peserta' => [
            'required' => 'Jumlah peserta harus diisi',
            'integer' => 'Jumlah peserta harus berupa angka',
            'greater_than' => 'Jumlah peserta minimal 1 orang'
        ]
    ];

    /**
     * FIXED: Cek availability ruangan untuk booking langsung
     * Perbaikan struktur query untuk mencegah false positive
     */
public function checkAvailability($ruanganId, $tanggal, $waktuMulai, $waktuSelesai, $userId = null)
{
    if (!$userId) {
        $userId = session()->get('user_id');
    }

    // =============================
    // 1. CEK BOOKING USER SENDIRI
    // =============================
    $userConflict = $this->where('user_id', $userId)
        ->where('ruangan_id', $ruanganId)
        ->where('tanggal', $tanggal)
        ->where('status', 'aktif')
        ->groupStart()
            ->where('waktu_mulai <', $waktuSelesai)
            ->where('waktu_selesai >', $waktuMulai)
        ->groupEnd()
        ->first();

    if ($userConflict) {
        return $userConflict;
    }

    // =============================
    // 2. CEK PINJAM RUANGAN (CONFIRM)
    // =============================
    $pinjamModel = new \App\Models\PinjamRuanganModel();

    $confirmConflict = $pinjamModel->where('ruangan_id', $ruanganId)
        ->where('tanggal', $tanggal)
        ->where('status', 'disetujui') // CONFIRM
        ->groupStart()
            ->where('waktu_mulai <', $waktuSelesai)
            ->where('waktu_selesai >', $waktuMulai)
        ->groupEnd()
        ->first();

    if ($confirmConflict) {
        return $confirmConflict;
    }

    return null;
}


    /**
     * Get booking aktif untuk notifikasi publik
     */
    public function getBookingPublik($limit = 10)
    {
        return $this->select('booking_ruangan.*, ruangan.nama_ruangan')
            ->join('ruangan', 'ruangan.id = booking_ruangan.ruangan_id')
            ->where('booking_ruangan.status', 'aktif')
            ->where('booking_ruangan.tanggal >=', date('Y-m-d'))
            ->orderBy('booking_ruangan.tanggal', 'ASC')
            ->orderBy('booking_ruangan.waktu_mulai', 'ASC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get booking by date untuk kalender
     */
    public function getBookingByDate($tanggal, $lokasi = null)
    {
        $builder = $this->select('booking_ruangan.*, ruangan.nama_ruangan, ruangan.lokasi')
            ->join('ruangan', 'ruangan.id = booking_ruangan.ruangan_id')
            ->where('booking_ruangan.tanggal', $tanggal)
            ->where('booking_ruangan.status', 'aktif');

        if ($lokasi) {
            $builder->where('ruangan.lokasi', $lokasi);
        }

        return $builder->orderBy('booking_ruangan.waktu_mulai', 'ASC')->findAll();
    }

    /**
     * Get riwayat booking user
     */
    public function getUserBookings($userId, $limit = 20)
    {
        return $this->select('booking_ruangan.*, ruangan.nama_ruangan')
            ->join('ruangan', 'ruangan.id = booking_ruangan.ruangan_id')
            ->where('booking_ruangan.user_id', $userId)
            ->orderBy('booking_ruangan.created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Cancel booking (hanya bisa dilakukan oleh user sendiri atau admin)
     */
    public function cancelBooking($bookingId, $userId = null)
    {
        $booking = $this->find($bookingId);
        
        if (!$booking) {
            return false;
        }

        // Hanya user yang booking atau admin yang bisa cancel
        if ($userId && $booking['user_id'] != $userId) {
            return false;
        }

        // Cek apakah booking masih bisa dibatalkan (misalnya H-1)
        $tanggalBooking = strtotime($booking['tanggal']);
        $sekarang = time();
        $selisihHari = ($tanggalBooking - $sekarang) / (24 * 60 * 60);
        
        // Batasi pembatalan minimal H-1
        if ($selisihHari < 1) {
            return false;
        }

        return $this->update($bookingId, ['status' => 'dibatalkan']);
    }

    /**
     * Get booking untuk range tanggal tertentu
     */
    public function getBookingByDateRange($startDate, $endDate, $ruanganId = null)
    {
        $builder = $this->select('booking_ruangan.*, ruangan.nama_ruangan')
            ->join('ruangan', 'ruangan.id = booking_ruangan.ruangan_id')
            ->where('booking_ruangan.tanggal >=', $startDate)
            ->where('booking_ruangan.tanggal <=', $endDate)
            ->where('booking_ruangan.status', 'aktif');

        if ($ruanganId) {
            $builder->where('booking_ruangan.ruangan_id', $ruanganId);
        }

        return $builder->orderBy('booking_ruangan.tanggal', 'ASC')
            ->orderBy('booking_ruangan.waktu_mulai', 'ASC')
            ->findAll();
    }

    /**
     * Get statistik booking untuk dashboard
     */
    public function getBookingStats($startDate = null, $endDate = null)
    {
        $startDate = $startDate ?? date('Y-m-01'); // Awal bulan
        $endDate = $endDate ?? date('Y-m-t'); // Akhir bulan
        
        return [
            'total_booking' => $this->where('tanggal >=', $startDate)
                ->where('tanggal <=', $endDate)
                ->countAllResults(),
            'booking_aktif' => $this->where('tanggal >=', $startDate)
                ->where('tanggal <=', $endDate)
                ->where('status', 'aktif')
                ->countAllResults(),
            'booking_selesai' => $this->where('tanggal >=', $startDate)
                ->where('tanggal <=', $endDate)
                ->where('status', 'selesai')
                ->countAllResults(),
            'booking_dibatalkan' => $this->where('tanggal >=', $startDate)
                ->where('tanggal <=', $endDate)
                ->where('status', 'dibatalkan')
                ->countAllResults()
        ];
    }

    /**
     * Update status booking otomatis (untuk cron job)
     * Update status menjadi 'selesai' jika tanggal booking sudah lewat
     */
    public function updateExpiredBookings()
    {
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        
        return $this->where('tanggal <', $yesterday)
            ->where('status', 'aktif')
            ->set('status', 'selesai')
            ->update();
    }
    
}