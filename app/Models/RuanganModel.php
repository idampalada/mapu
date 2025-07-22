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
        ]
    ];

    protected $skipValidation = false;


    // 1. Get ruangan stats (converted to Query Builder)
    public function getRuanganStats()
    {
        // Count total ruangan
        $builderTotal = $this->builder();
        $total = $builderTotal->where('deleted_at', null)
                             ->countAllResults();
        
        // Count ruangan yang digunakan (disetujui)
        $builderDigunakan = $this->db->table('pinjam_ruangan');
        $digunakan = $builderDigunakan->where('status', 'disetujui')
                                    ->where('deleted_at', null)
                                    ->select('ruangan_id')
                                    ->distinct()
                                    ->countAllResults();
        
        // Count ruangan menunggu verifikasi
        $builderPending = $this->db->table('pinjam_ruangan');
        $menungguVerifikasi = $builderPending->where('status', 'pending')
                                           ->where('deleted_at', null)
                                           ->select('ruangan_id')
                                           ->distinct()
                                           ->countAllResults();
        
        $tersedia = $total - $digunakan - $menungguVerifikasi;

        return [
            'total' => $total,
            'digunakan' => $digunakan,
            'menunggu_verifikasi' => $menungguVerifikasi,
            'tersedia' => $tersedia
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
}