<?php
// App/Models/BookingRuanganModel.php

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
        'keperluan', 'nama_penanggung_jawab', 'unit_organisasi', 
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
     * Cek availability ruangan untuk booking langsung
     * Simple check tanpa complex time blocking
     */
    public function checkAvailability($ruanganId, $tanggal, $waktuMulai, $waktuSelesai)
    {
        // Cek di tabel booking_ruangan (booking langsung)
        $existingBooking = $this->where('ruangan_id', $ruanganId)
            ->where('tanggal', $tanggal)
            ->where('status', 'aktif')
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
            ->first();

        if ($existingBooking) {
            return $existingBooking;
        }

        // Cek juga di tabel pinjam_ruangan (request confirm yang disetujui)
        $pinjamModel = new \App\Models\PinjamRuanganModel();
        $existingPinjam = $pinjamModel->where('ruangan_id', $ruanganId)
            ->where('tanggal', $tanggal)
            ->where('status', 'disetujui')
            ->where('deleted_at', null)
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
            ->first();

        return $existingPinjam;
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
        return $this->select('booking_ruangan.*, ruangan.nama_ruangan, ruangan.lokasi')
            ->join('ruangan', 'ruangan.id = booking_ruangan.ruangan_id')
            ->where('booking_ruangan.user_id', $userId)
            ->orderBy('booking_ruangan.created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Cancel booking
     */
    public function cancelBooking($bookingId, $userId)
    {
        $booking = $this->where('id', $bookingId)
            ->where('user_id', $userId)
            ->where('status', 'aktif')
            ->first();

        if (!$booking) {
            return false;
        }

        // Tidak bisa cancel jika hari H sudah lewat
        if (strtotime($booking['tanggal']) < strtotime(date('Y-m-d'))) {
            return false;
        }

        return $this->update($bookingId, ['status' => 'dibatalkan']);
    }

    /**
     * Mark booking as completed (untuk cleanup otomatis)
     */
    public function markExpiredBookings()
    {
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        
        return $this->where('tanggal <', $yesterday)
            ->where('status', 'aktif')
            ->set('status', 'selesai')
            ->update();
    }
}
?>