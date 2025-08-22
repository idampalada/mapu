<?php

namespace App\Models;

use CodeIgniter\Model;

class RuanganModel extends Model
{
    protected $table = 'ruangan';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = [
        'nama_ruangan',
        'lokasi',
        'kapasitas',
        'fasilitas',
        'foto_ruangan',
        'is_active',
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
        'nama_ruangan' => 'required',
        'lokasi' => 'required',
        'kapasitas' => 'required|numeric',
        
    ];

    protected $validationMessages = [
        'nama_ruangan' => [
            'required' => 'Nama ruangan harus diisi'
        ],
        'lokasi' => [
            'required' => 'Lokasi harus dipilih'
        ],
        'kapasitas' => [
            'required' => 'Kapasitas harus diisi',
            'numeric' => 'Kapasitas harus berupa angka'
            ],
    ];

    protected $skipValidation = false;


    // 1. Get ruangan stats (converted to Query Builder)
    public function getRuanganStats()
    {
        // Count total ruangan aktif
        $builderTotal = $this->builder();
        $total = $builderTotal->where('deleted_at', null)
                             ->where('is_active', true)
                             ->countAllResults();
        
        // Count ruangan yang digunakan (disetujui) dan aktif
        $builderDigunakan = $this->db->table('pinjam_ruangan pr');
        $digunakan = $builderDigunakan->join('ruangan r', 'r.id = pr.ruangan_id')
                                    ->where('pr.status', 'disetujui')
                                    ->where('pr.deleted_at', null)
                                    ->where('r.is_active', true)
                                    ->select('pr.ruangan_id')
                                    ->distinct()
                                    ->countAllResults();
        
        // Count ruangan menunggu verifikasi dan aktif
        $builderPending = $this->db->table('pinjam_ruangan pr');
        $menungguVerifikasi = $builderPending->join('ruangan r', 'r.id = pr.ruangan_id')
                                           ->where('pr.status', 'pending')
                                           ->where('pr.deleted_at', null)
                                           ->where('r.is_active', true)
                                           ->select('pr.ruangan_id')
                                           ->distinct()
                                           ->countAllResults();
        
        // Count ruangan non-aktif (maintenance)
        $builderMaintenance = $this->builder();
        $maintenance = $builderMaintenance->where('deleted_at', null)
                                         ->where('is_active', false)
                                         ->countAllResults();
        
        $tersedia = $total - $digunakan - $menungguVerifikasi;

        return [
            'total' => $total,
            'digunakan' => $digunakan,
            'menunggu_verifikasi' => $menungguVerifikasi,
            'tersedia' => $tersedia,
            'maintenance' => $maintenance // Status baru untuk ruangan non-aktif
        ];
    }

    // 2. Get all ruangan (converted to Query Builder)
    public function getAllRuangan()
    {
        $builder = $this->builder();
        return $builder->where('deleted_at', null)
                      ->orderBy('created_at', 'DESC')
                      ->get()
                      ->getResultArray();
    }

    // 3. Get ruangan by lokasi (converted to Query Builder)
    public function getRuanganByLokasi($lokasi)
    {
        $builder = $this->builder();
        return $builder->where('lokasi', $lokasi)
                      ->where('deleted_at', null)
                      ->get()
                      ->getResultArray();
    }

    // 4. Get ruangan detail (converted to Query Builder)
    public function getRuanganDetail($id)
    {
        $builder = $this->builder();
        return $builder->where('id', $id)
                      ->get()
                      ->getRowArray();
    }

    // 5. Delete ruangan (converted to Query Builder)
    public function deleteRuangan($id)
    {
        $builder = $this->builder();
        
        $data = [
            'deleted_at' => date('Y-m-d H:i:s')
        ];
        
        return $builder->where('id', $id)
                      ->update($data);
    }
    
    // 6. Update ruangan (converted to Query Builder)
    public function updateRuangan($id, $data)
    {
        $builder = $this->builder();
        return $builder->where('id', $id)
                      ->update($data);
    }
     // 7. Method untuk ruangan aktif
        public function getActiveRuangan()
    {
        return $this->where('is_active', true)
                   ->where('deleted_at', null)
                   ->findAll();
    }
    // 8. Method untuk mendapatkan ruangan berdasarkan lokasi dan status aktif
        public function getRuanganByLokasiAndStatus($lokasi, $isActive = true)
    {
        return $this->where('lokasi', $lokasi)
                   ->where('is_active', $isActive)
                   ->where('deleted_at', null)
                   ->findAll();
    }
    // 9. Method untuk mengubah status aktif ruangany
    public function toggleActiveStatus($id, $isActive)
    {
        return $this->update($id, ['is_active' => $isActive]);
    }

    // Method untuk cek apakah ruangan bisa dipinjam
    public function isRuanganAvailableForBooking($id)
    {
        $ruangan = $this->find($id);
        if (!$ruangan) {
            return false;
        }
        
        // Ruangan harus aktif untuk bisa dipinjam
        return $ruangan['is_active'] == true;
    }
}
    

