<?php

/**
 * ============================================================================
 *  FILE BARU — milik aplikasi API (mapu/api/)
 *  Letakkan : mapu/api/app/Controllers/Kendaraan.php
 *  Route    : /api/v1/kendaraan/*   (DILINDUNGI filter jwt)
 *  Tujuan   : LANGKAH 2 — CRUD data kendaraan (tabel alat_angkutan).
 * ============================================================================
 */

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\Api\KendaraanModel;

class Kendaraan extends ResourceController
{
    protected $modelName = KendaraanModel::class;
    protected $format    = 'json';

    /**
     * GET /api/v1/kendaraan
     * Query opsional: ?search= ?kelompok= ?page= ?per_page= ?format=
     *
     * ?format=siman → membalas gaya API SIMAN: { "resource": [ ... ] }
     * (tanpa parameter format → format default dengan info pagination)
     */
    public function index()
    {
        $search   = (string) ($this->request->getGet('search') ?? '');
        $kelompok = (string) ($this->request->getGet('kelompok') ?? '');
        $page     = max(1, (int) ($this->request->getGet('page') ?? 1));
        $perPage  = (int) ($this->request->getGet('per_page') ?? 25);
        $perPage  = ($perPage > 0 && $perPage <= 200) ? $perPage : 25;
        $offset   = ($page - 1) * $perPage;

        $data = $this->model->search($search, $kelompok, $perPage, $offset);

        // Format gaya SIMAN
        if ($this->isSimanFormat()) {
            return $this->respond(['resource' => $data], 200);
        }

        $total = $this->model->countFiltered($search, $kelompok);

        return $this->respond([
            'status'   => 'success',
            'total'    => $total,
            'page'     => $page,
            'per_page' => $perPage,
            'count'    => count($data),
            'data'     => $data,
        ], 200);
    }

    /**
     * Apakah klien meminta format gaya SIMAN (?format=siman)?
     */
    private function isSimanFormat(): bool
    {
        return strtolower(trim((string) ($this->request->getGet('format') ?? ''))) === 'siman';
    }

    /**
     * GET /api/v1/kendaraan/{id}
     */
    public function show($id = null)
    {
        $item = $this->model->find((int) $id);

        if (! $item) {
            return $this->respond([
                'status'  => 'error',
                'message' => 'Data kendaraan tidak ditemukan.',
            ], 404);
        }

        // Format gaya SIMAN: resource selalu berupa array
        if ($this->isSimanFormat()) {
            return $this->respond(['resource' => [$item]], 200);
        }

        return $this->respond([
            'status' => 'success',
            'data'   => $item,
        ], 200);
    }

    /**
     * GET /api/v1/kendaraan/statistik
     */
    public function statistik()
    {
        return $this->respond([
            'status' => 'success',
            'data'   => [
                'total'        => $this->model->countFiltered(),
                'per_kelompok' => $this->model->statistikKelompok(),
                'per_kondisi'  => $this->model->statistikKondisi(),
            ],
        ], 200);
    }

    /**
     * POST /api/v1/kendaraan
     */
    public function create()
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        if (empty($data)) {
            return $this->respond([
                'status'  => 'error',
                'message' => 'Body request kosong.',
            ], 400);
        }

        if ($this->model->insert($data) === false) {
            return $this->respond([
                'status'  => 'error',
                'message' => 'Validasi gagal.',
                'errors'  => $this->model->errors(),
            ], 422);
        }

        $newId = $this->model->getInsertID();

        return $this->respond([
            'status'  => 'success',
            'message' => 'Kendaraan berhasil ditambahkan.',
            'data'    => $this->model->find((int) $newId),
        ], 201);
    }

}
