<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;

class BarangKategori extends ResourceController
{
    protected $format = 'json';

    public function index()
    {
        $data = [
            [
                'kode' => '3.10.01',
                'uraian' => 'KOMPUTER UNIT',
                'sub' => [
                    ['kode' => '3.10.01.01', 'uraian' => 'Komputer Jaringan'],
                    ['kode' => '3.10.01.02', 'uraian' => 'Personal Komputer'],
                    ['kode' => '3.10.01.99', 'uraian' => 'Komputer Unit Lainnya']
                ]
            ],
            [
                'kode' => '3.10.02',
                'uraian' => 'PERALATAN KOMPUTER',
                'sub' => [
                    ['kode' => '3.10.02.01', 'uraian' => 'Peralatan Mainframe']
                ]
            ]
        ];

        return $this->respond($data, 200);
    }
}
