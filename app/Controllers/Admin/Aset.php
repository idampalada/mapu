<?php

namespace App\Controllers\Admin;

use App\Models\AsetModel;
use CodeIgniter\Controller;

class Aset extends Controller
{
    protected $asetModel;

    public function __construct()
    {
        $this->asetModel = new AsetModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Daftar Aset',
            'aset' => $this->asetModel->findAll()
        ];

        return view('admin/daftar-aset', $data);
    }

    public function getDetail($id)
    {
        $aset = $this->asetModel->find($id);

        if (!$aset) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Aset tidak ditemukan'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $aset
        ]);
    }
}