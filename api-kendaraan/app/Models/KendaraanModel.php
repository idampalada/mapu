<?php

/**
 * ============================================================================
 *  FILE BARU — milik aplikasi API (mapu/api/)
 *  Letakkan : mapu/api/app/Models/KendaraanModel.php
 *  Tabel    : alat_angkutan (dibaca dari database mapu yang sama)
 *  Catatan  : Model ini MILIK APP BARU. Project mapu tidak disentuh sama sekali.
 * ============================================================================
 */

namespace App\Models;

use CodeIgniter\Model;

class KendaraanModel extends Model
{
    protected $table         = 'alat_angkutan';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;

    protected $allowedFields = [
        'tgl_tarik',
        'nama_kl',
        'nama_kpknl',
        'nama_satker',
        'kode_barang',
        'nama_barang',
        'nilai_perolehan',
        'nilai_penyusutan',
        'nilai_buku',
        'nup',
        'tanggal_perolehan',
        'kondisi',
        'merk',
        'kuantitas',
        'status_penggunaan',
        'thn_buat',
        'no_mesin',
        'no_rangka',
        'no_polisi',
        'daya_mesin',
        'bhn_bakar',
        'kelompok',
        'sub_kelompok',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'kode_barang'      => 'required|max_length[100]',
        'nama_barang'      => 'required|max_length[255]',
        'kelompok'         => 'required|max_length[100]',
        'nup'              => 'permit_empty|max_length[100]',
        'merk'             => 'permit_empty|max_length[100]',
        'kondisi'          => 'permit_empty|max_length[50]',
        'kuantitas'        => 'permit_empty|integer',
        'status_penggunaan'=> 'permit_empty|max_length[200]',
        'thn_buat'         => 'permit_empty|max_length[4]',
        'no_mesin'         => 'permit_empty|max_length[100]',
        'no_rangka'        => 'permit_empty|max_length[100]',
        'no_polisi'        => 'permit_empty|max_length[50]',
        'daya_mesin'       => 'permit_empty|max_length[100]',
        'bhn_bakar'        => 'permit_empty|max_length[50]',
        'nilai_perolehan'  => 'permit_empty|decimal',
        'nilai_penyusutan' => 'permit_empty|decimal',
        'nilai_buku'       => 'permit_empty|decimal',
    ];

    protected $validationMessages = [
        'kode_barang' => [
            'required'   => 'Kode barang harus diisi',
            'max_length' => 'Kode barang maksimal 100 karakter',
        ],
        'nama_barang' => [
            'required'   => 'Nama barang harus diisi',
            'max_length' => 'Nama barang maksimal 255 karakter',
        ],
        'kelompok' => [
            'required'   => 'Kelompok harus diisi',
            'max_length' => 'Kelompok maksimal 100 karakter',
        ],
    ];

    protected $skipValidation        = false;
    protected $cleanValidationRules  = true;

    // ================= QUERY =================

    /**
     * Pencarian + filter kelompok + pagination.
     */
    public function search(string $keyword = '', string $kelompok = '', int $limit = 25, int $offset = 0): array
    {
        $builder = $this->builder();

        if ($kelompok !== '') {
            $builder->where('UPPER(kelompok)', strtoupper($kelompok));
        }

        if ($keyword !== '') {
            $keyword = $this->db->escapeLikeString($keyword);

            $builder->groupStart()
                ->like('kode_barang', $keyword)
                ->orLike('nama_barang', $keyword)
                ->orLike('merk', $keyword)
                ->orLike('no_mesin', $keyword)
                ->orLike('no_rangka', $keyword)
                ->orLike('no_polisi', $keyword)
                ->orLike('nup', $keyword)
                ->groupEnd();
        }

        return $builder->orderBy('kode_barang', 'ASC')
                       ->limit($limit, $offset)
                       ->get()
                       ->getResultArray();
    }

    /**
     * Hitung total sesuai filter (untuk info pagination).
     */
    public function countFiltered(string $keyword = '', string $kelompok = ''): int
    {
        $builder = $this->builder();

        if ($kelompok !== '') {
            $builder->where('UPPER(kelompok)', strtoupper($kelompok));
        }

        if ($keyword !== '') {
            $keyword = $this->db->escapeLikeString($keyword);

            $builder->groupStart()
                ->like('kode_barang', $keyword)
                ->orLike('nama_barang', $keyword)
                ->orLike('merk', $keyword)
                ->orLike('no_mesin', $keyword)
                ->orLike('no_rangka', $keyword)
                ->orLike('no_polisi', $keyword)
                ->orLike('nup', $keyword)
                ->groupEnd();
        }

        return $builder->countAllResults();
    }

    /**
     * Statistik jumlah per kelompok.
     */
    public function statistikKelompok(): array
    {
        return $this->builder()
                    ->select('kelompok, COUNT(*) AS jumlah')
                    ->groupBy('kelompok')
                    ->orderBy('kelompok', 'ASC')
                    ->get()
                    ->getResultArray();
    }

    /**
     * Statistik jumlah per kondisi.
     */
    public function statistikKondisi(): array
    {
        return $this->builder()
                    ->select('kondisi, COUNT(*) AS jumlah')
                    ->groupBy('kondisi')
                    ->orderBy('kondisi', 'ASC')
                    ->get()
                    ->getResultArray();
    }
}
