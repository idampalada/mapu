<?php

namespace App\Controllers\User\Barang;

use App\Controllers\BaseController;
use App\Models\AsetTakBerwujudModel;

class AsetTakBerwujud extends BaseController
{
    protected $asetTakBerwujudModel;
    
    public function __construct()
    {
        $this->asetTakBerwujudModel = new AsetTakBerwujudModel();
    }

    // Method untuk mengambil data dari API
    private function getApiData($url = null)
    {
        $client = \Config\Services::curlrequest();
        $apiKey = 'c877acaa0de297a9e3b8bbdb101dd254d33a92a0444b979d599e04fdeaccdbc5';
        
        if (!$url) {
            $url = "https://apigw.pu.go.id/v1/siman/aset-tak-berwujud?api_key={$apiKey}";
        }
        
        try {
            $response = $client->get($url, [
                'timeout' => 30,
                'connect_timeout' => 10
            ]);
            
            if ($response->getStatusCode() === 200) {
                $result = json_decode($response->getBody(), true);
                return $result['resource'] ?? [];
            }
        } catch (\Exception $e) {
            log_message('error', 'API error: ' . $e->getMessage());
        }
        
        return [];
    }

    public function dashboard()
    {
        $asetTakBerwujudList = $this->getApiData();
        return view('user/barang/asettakberwujud/asettakberwujud', [
            'asetTakBerwujudList' => $asetTakBerwujudList
        ]);
    }

    public function kelompokAsetTakBerwujud()
    {
        $sort = $this->request->getGet('sort') ?? 'kode_barang';
        $order = $this->request->getGet('order') ?? 'asc';
        
        // Menggunakan data dari database untuk konsistensi
        $allAsetTakBerwujudList = $this->asetTakBerwujudModel->findAll();
        
        // Filter data berdasarkan kelompok
        $asetTakBerwujudData = array_filter($allAsetTakBerwujudList, function ($item) {
            return strtolower($item['kelompok'] ?? '') === 'aset tak berwujud';
        });

        $asetTakBerwujudDalamPenyelesaianData = array_filter($allAsetTakBerwujudList, function ($item) {
            return strtolower($item['kelompok'] ?? '') === 'aset tak berwujud dalam penyelesaian';
        });

        $asetKemitraanData = array_filter($allAsetTakBerwujudList, function ($item) {
            return strtolower($item['kelompok'] ?? '') === 'aset kemitraan';
        });

        // Reset array keys
        $asetTakBerwujudData = array_values($asetTakBerwujudData);
        $asetTakBerwujudDalamPenyelesaianData = array_values($asetTakBerwujudDalamPenyelesaianData);
        $asetKemitraanData = array_values($asetKemitraanData);
        
        // Gabungkan semua data
        $allData = array_merge($asetTakBerwujudData, $asetTakBerwujudDalamPenyelesaianData, $asetKemitraanData);

        return view('user/barang/asettakberwujud/asettakberwujud/kelompokasettakberwujud', [
            'sort' => $sort,
            'order' => $order,
            'allData' => $allData,
        ]);
    }

    public function kelompokDetail($kelompok)
    {
        $searchTerm = $this->request->getGet('search') ?? '';
        $sort = $this->request->getGet('sort') ?? 'kode_barang';
        $order = $this->request->getGet('order') ?? 'asc';
        $perPage = 100;
        $page = $this->request->getGet('page') ?? 1;

        // Gunakan database sebagai sumber data
        $builder = $this->asetTakBerwujudModel->builder();
        
        // Filter berdasarkan kelompok
        $builder->where('UPPER(kelompok)', strtoupper($kelompok));
        
        // Filter berdasarkan pencarian
        if (!empty($searchTerm)) {
            $builder->groupStart()
                ->like('nama_barang', $searchTerm)
                ->orLike('kode_barang', $searchTerm) 
                ->orLike('merk', $searchTerm)
                ->orLike('nup', $searchTerm)
                ->orLike('no_kib', $searchTerm)
                ->groupEnd();
        }
        
        // Hitung total data
        $totalItems = $builder->countAllResults(false);
        
        // Sorting
        if (!empty($sort)) {
            $builder->orderBy($sort, $order);
        }
        
        // Pagination
        $offset = ($page - 1) * $perPage;
        $asetTakBerwujudList = $builder->limit($perPage, $offset)->get()->getResultArray();

        // Setup pagination
        $pager = service('pager');
        $pager->setPath('user/barang/asettakberwujud/kelompokasettakberwujud/' . urlencode($kelompok));
        $totalPages = ceil($totalItems / $perPage);

        return view('user/barang/asettakberwujud/kelompokasettakberwujud', [
            'asetTakBerwujudList' => $asetTakBerwujudList,
            'kelompok' => strtoupper($kelompok),
            'activeKelompok' => strtoupper($kelompok),
            'pager' => $pager,
            'searchTerm' => $searchTerm,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalItems' => $totalItems, 
            'sort' => $sort, 
            'order' => $order  
        ]);
    }

    // Method untuk menambah aset tak berwujud manual
    public function tambah()
    {
        $method2 = $_SERVER['REQUEST_METHOD'] ?? 'unknown';
        $postData = $this->request->getPost();
        $postRaw = $_POST;
        
        $isPost = (strtoupper($method2) === 'POST') || !empty($postData) || !empty($postRaw);
        
        if ($isPost && (!empty($postData) || !empty($postRaw))) {
            $data_source = !empty($postData) ? $postData : $postRaw;
            
            $data = [
                'kode_barang' => trim($data_source['kode_barang'] ?? ''),
                'nama_barang' => trim($data_source['nama_barang'] ?? ''),
                'nup' => trim($data_source['nup'] ?? ''),
                'no_kib' => trim($data_source['no_kib'] ?? ''),
                'merk' => trim($data_source['merk'] ?? ''),
                'kelompok' => strtoupper(trim($data_source['kelompok'] ?? '')),
                'kondisi' => trim($data_source['kondisi'] ?? ''),
                'kuantitas' => intval($data_source['kuantitas'] ?? 1),
                'status_penggunaan' => trim($data_source['status_penggunaan'] ?? ''),
                'nilai_perolehan' => $this->safeFloat($data_source['nilai_perolehan'] ?? ''),
                'tanggal_perolehan' => !empty($data_source['tanggal_perolehan']) ? $data_source['tanggal_perolehan'] : null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Validasi
            $errors = [];
            if (empty($data['kode_barang'])) {
                $errors[] = 'Kode barang harus diisi';
            }
            if (empty($data['nama_barang'])) {
                $errors[] = 'Nama barang harus diisi';
            }
            if (empty($data['kelompok'])) {
                $errors[] = 'Kelompok harus diisi';
            }

            if (!empty($errors)) {
                session()->setFlashdata('error', 'Error: ' . implode(', ', $errors));
                return redirect()->back()->withInput();
            }

            try {
                $this->asetTakBerwujudModel->skipValidation(true);
                $insertResult = $this->asetTakBerwujudModel->insert($data);
                
                if ($insertResult) {
                    $insertId = $this->asetTakBerwujudModel->getInsertID();
                    session()->setFlashdata('success', "Data aset tak berwujud berhasil disimpan! ID: {$insertId}");
                } else {
                    $errors = $this->asetTakBerwujudModel->errors();
                    session()->setFlashdata('error', 'Gagal menyimpan data: ' . implode(', ', $errors));
                }
                
                $this->asetTakBerwujudModel->skipValidation(false);
                
            } catch (\Exception $e) {
                session()->setFlashdata('error', 'Error database: ' . $e->getMessage());
                $this->asetTakBerwujudModel->skipValidation(false);
            }
        }
        
        return redirect()->to('user/barang/asettakberwujud/kelompokasettakberwujud');
    }

    // Method untuk reset semua data
    public function resetData()
    {
        try {
            $this->asetTakBerwujudModel->builder()->truncate();
            session()->setFlashdata('success', 'Semua data berhasil dihapus!');
            return redirect()->to('user/barang/asettakberwujud/kelompokasettakberwujud');
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Gagal menghapus data: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    // Method import dari API
    public function importFromApi()
    {
        $imported = 0;
        $skipped = 0;
        $filtered = 0;
        $errors = [];

        try {
            $apiData = $this->getApiData();

            if (empty($apiData)) {
                session()->setFlashdata('error', 'Tidak ada data dari API atau API tidak dapat diakses!');
                return redirect()->back();
            }

            $this->asetTakBerwujudModel->skipValidation(true);
            $validKelompok = ['ASET TAK BERWUJUD', 'ASET TAK BERWUJUD DALAM PENYELESAIAN', 'ASET KEMITRAAN'];

            foreach ($apiData as $index => $item) {
                try {
                    $kode_barang = trim($item['kode_barang'] ?? '');
                    $kelompok_api = strtoupper(trim($item['kelompok'] ?? ''));
                    
                    if (empty($kode_barang)) {
                        $skipped++;
                        continue;
                    }

                    if (!in_array($kelompok_api, $validKelompok)) {
                        $filtered++;
                        continue;
                    }

                    $unique_kode = $kode_barang . '_' . $index;

                    $data = [
                        'kode_barang' => $unique_kode,
                        'nama_barang' => trim($item['nama_barang'] ?? '') ?: 'Unknown',
                        'nup' => trim($item['nup'] ?? ''),
                        'no_kib' => trim($item['no_kib'] ?? ''),
                        'merk' => trim($item['merk'] ?? ''),
                        'kelompok' => $kelompok_api,
                        'sub_kelompok' => trim($item['sub_kelompok'] ?? ''),
                        'kondisi' => trim($item['kondisi'] ?? ''),
                        'kuantitas' => intval($item['kuantitas'] ?? 1),
                        'status_penggunaan' => trim($item['status_penggunaan'] ?? ''),
                        'nilai_perolehan' => $this->safeFloat($item['nilai_perolehan'] ?? 0),
                        'nilai_penyusutan' => $this->safeFloat($item['nilai_penyusutan'] ?? 0),
                        'nilai_buku' => $this->safeFloat($item['nilai_buku'] ?? 0),
                        'tanggal_perolehan' => !empty($item['tanggal_perolehan']) ? $item['tanggal_perolehan'] : null,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];

                    if ($this->asetTakBerwujudModel->insert($data)) {
                        $imported++;
                    } else {
                        $errors[] = $kode_barang;
                    }

                } catch (\Exception $e) {
                    $errors[] = ($kode_barang ?? 'unknown') . ': ' . $e->getMessage();
                }
            }

            $this->asetTakBerwujudModel->skipValidation(false);

            $total = count($apiData);
            $message = "Import selesai! Total API: {$total}, Berhasil: {$imported}, Dilewati: {$skipped}, Difilter: {$filtered}";
            
            if (!empty($errors)) {
                $message .= ", Error: " . count($errors);
            }

            session()->setFlashdata('success', $message);
            return redirect()->to('user/barang/asettakberwujud/kelompokasettakberwujud');

        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Gagal import data: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    // Helper method untuk konversi float yang aman
    private function safeFloat($value)
    {
        if (is_null($value) || $value === '') {
            return 0.0;
        }
        
        if (is_string($value)) {
            $value = str_replace(',', '.', $value);
        }
        
        return floatval($value);
    }
}