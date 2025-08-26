<?php

namespace App\Models;

use CodeIgniter\Model;

class BarangModel extends Model
{
    protected $table            = 'barang';
    protected $primaryKey       = 'id';

    protected $allowedFields    = [
        'nama_peminjam',
        'user_id',
        'barang_id',
        'tanggal',
        'waktu_mulai',
        'waktu_selesai',
        'keperluan',
        'file_pendukung',
        'nama_barang',
        'kategori',
        'kondisi',
        'lokasi',
        'status',
        'gambar',
        'kode_barang',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'nama_barang' => 'required|string|min_length[3]',
        'kategori'    => 'required|string',
        'kondisi'     => 'required|string',
        'lokasi'      => 'required|string',
        'status'      => 'required|string',
        'kode_barang'  => 'required|string'
    ];

    protected $validationMessages = [
        'nama_barang' => [
            'required' => 'Nama barang harus diisi.'
        ],
        'kategori' => [
            'required' => 'Kategori harus diisi.'
        ],
        'kondisi' => [
            'required' => 'Kondisi barang harus diisi.'
        ],
        'lokasi' => [
            'required' => 'Lokasi harus diisi.'
        ],
        'status' => [
            'required' => 'Status harus diisi.'
        ],
        'kode_barang' => [
            'required' => 'Kode barang harus diisi.'
        ],
    ];

 public function getStatistikBarang()
    {
        return [
            'total'     => $this->where('deleted_at', null)->countAllResults(),
            'tersedia'  => $this->where('status', 'Tersedia')->where('deleted_at', null)->countAllResults(),
            'dipinjam'  => $this->where('status', 'Dipinjam')->where('deleted_at', null)->countAllResults(),
        ];
    }
}