<?php

namespace App\Controllers\User\Barang\PeralatanDanMesin;

use App\Controllers\BaseController;
use App\Models\AlatKantorRumahTanggaModel;

class AlatKantorRumahTangga extends BaseController
{
    protected $alatKantorRumahTanggaModel;
    
    public function __construct()
    {
        $this->alatKantorRumahTanggaModel = new AlatKantorRumahTanggaModel();
    }

    // Method untuk mengambil data dari API
    private function getApiData($url = null)
    {
        $client = \Config\Services::curlrequest();
        $apiKey = 'c877acaa0de297a9e3b8bbdb101dd254d33a92a0444b979d599e04fdeaccdbc5';
        
        if (!$url) {
            $url = "https://apigw.pu.go.id/v1/siman/pm-non-tik?api_key={$apiKey}";
        }
        
        try {
            $response = $client->get($url, [
                'timeout' => 30,
                'connect_timeout' => 10,
                'verify' => false
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
        $alatKantorRumahTanggaList = $this->getApiData();
        return view('user/barang/peralatandanmesin/alatkantorrt/dashboardalatkantorrt', [
            'alatKantorRumahTanggaList' => $alatKantorRumahTanggaList
        ]);
    }

    public function kelompokAlatKantorRumahTangga()
    {
        $sort = $this->request->getGet('sort') ?? 'kode_barang';
        $order = $this->request->getGet('order') ?? 'asc';
        
        $allAlatKantorRumahTanggaList = $this->alatKantorRumahTanggaModel->findAll();
        
        // Filter data berdasarkan kelompok - 2 kategori
        $alatKantorData = array_filter($allAlatKantorRumahTanggaList, function ($item) {
            return strtoupper($item['kelompok'] ?? '') === 'ALAT KANTOR';
        });

        $alatRumahTanggaData = array_filter($allAlatKantorRumahTanggaList, function ($item) {
            return strtoupper($item['kelompok'] ?? '') === 'ALAT RUMAH TANGGA';
        });

        // Reset array keys dan gabungkan
        $allData = array_merge(
            array_values($alatKantorData),
            array_values($alatRumahTanggaData)
        );

        return view('user/barang/peralatandanmesin/alatkantorrt/kelompokalatkantorrt', [
            'sort' => $sort,
            'order' => $order,
            'allData' => $allData,
        ]);
    }

    public function kelompokDetail($kelompok)
    {
        // Decode URL untuk handle karakter khusus
        $kelompok = urldecode($kelompok);
        
        // Log untuk debugging
        log_message('info', "kelompokDetail called with: " . $kelompok);
        
        // Validasi kelompok yang valid
        if (!$this->alatKantorRumahTanggaModel->isValidKelompokAlatKantorRumahTangga($kelompok)) {
            session()->setFlashdata('error', 'Kelompok tidak valid: ' . $kelompok);
            return redirect()->to('user/barang/peralatandanmesin/alatkantorrt/kelompokalatkantorrt');
        }
        
        // Debug: cek data di database
        $debugCount = $this->alatKantorRumahTanggaModel->where('UPPER(kelompok)', strtoupper($kelompok))->countAllResults();
        log_message('info', "Data found for kelompok '{$kelompok}': {$debugCount}");
        
        if ($debugCount == 0) {
            session()->setFlashdata('error', "Tidak ada data untuk kelompok: {$kelompok}");
            return redirect()->to('user/barang/peralatandanmesin/alatkantorrt/kelompokalatkantorrt');
        }
        
        $searchTerm = $this->request->getGet('search') ?? '';
        $sort = $this->request->getGet('sort') ?? 'kode_barang';
        $order = $this->request->getGet('order') ?? 'asc';
        $perPage = 100;
        $page = $this->request->getGet('page') ?? 1;

        // Gunakan database sebagai sumber data
        $builder = $this->alatKantorRumahTanggaModel->builder();
        
        // Filter berdasarkan kelompok
        $builder->where('UPPER(kelompok)', strtoupper($kelompok));
        
        // Filter berdasarkan pencarian
        if (!empty($searchTerm)) {
            $builder->groupStart()
                ->like('nama_barang', $searchTerm)
                ->orLike('kode_barang', $searchTerm) 
                ->orLike('merk', $searchTerm)
                ->orLike('sub_kelompok', $searchTerm)
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
        $alatKantorRumahTanggaList = $builder->limit($perPage, $offset)->get()->getResultArray();

        // Setup pagination
        $pager = service('pager');
        $pager->setPath('user/barang/peralatandanmesin/alatkantorrt/kelompokalatkantorrt/' . urlencode($kelompok));
        $totalPages = ceil($totalItems / $perPage);

        return view('user/barang/peralatandanmesin/alatkantorrt/kelompokalatkantorrt', [
            'alatKantorRumahTanggaList' => $alatKantorRumahTanggaList,
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

    // Method untuk menambah alat kantor rumah tangga manual
    public function tambah()
    {
        log_message('info', '=== TAMBAH ALAT KANTOR RUMAH TANGGA METHOD DIPANGGIL ===');
        
        $method2 = $_SERVER['REQUEST_METHOD'] ?? 'unknown';
        $postData = $this->request->getPost();
        $postRaw = $_POST;
        
        $isPost = (strtoupper($method2) === 'POST') || !empty($postData) || !empty($postRaw);
        
        if ($isPost && (!empty($postData) || !empty($postRaw))) {
            log_message('info', 'MASUK KE PROSES POST');
            
            $data_source = !empty($postData) ? $postData : $postRaw;
            
            $kode_barang = $data_source['kode_barang'] ?? '';
            $nama_barang = $data_source['nama_barang'] ?? '';
            $nup = $data_source['nup'] ?? '';
            $merk = $data_source['merk'] ?? '';
            $kelompok = $data_source['kelompok'] ?? '';
            $sub_kelompok = $data_source['sub_kelompok'] ?? '';
            $kondisi = $data_source['kondisi'] ?? '';
            $kuantitas = $data_source['kuantitas'] ?? '';
            $status_penggunaan = $data_source['status_penggunaan'] ?? '';
            $nilai_perolehan = $data_source['nilai_perolehan'] ?? '';
            $nilai_buku = $data_source['nilai_buku'] ?? '';
            $tanggal_perolehan = $data_source['tanggal_perolehan'] ?? '';
            $nama_satker = $data_source['nama_satker'] ?? '';
            $spek_lain = $data_source['spek_lain'] ?? '';
            
            // Mapping kelompok menggunakan method dari model
            $kategori_detail = $this->alatKantorRumahTanggaModel->mapKelompokToKategori($kelompok);
            
            $data = [
                'kode_barang' => trim($kode_barang),
                'nama_barang' => trim($nama_barang),
                'nup' => trim($nup),
                'merk' => trim($merk),
                'kelompok' => strtoupper(trim($kelompok)),
                'sub_kelompok' => trim($sub_kelompok),
                'kondisi' => trim($kondisi),
                'kuantitas' => intval($kuantitas ?: 1),
                'status_penggunaan' => trim($status_penggunaan),
                'nilai_perolehan' => $this->safeFloat($nilai_perolehan),
                'nilai_buku' => $this->safeFloat($nilai_buku),
                'tanggal_perolehan' => !empty($tanggal_perolehan) ? $tanggal_perolehan : null,
                'nama_satker' => trim($nama_satker),
                'spek_lain' => trim($spek_lain),
                'kategori_utama' => 'ALAT KANTOR DAN RUMAH TANGGA',
                'kategori_detail' => $kategori_detail,
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
                $this->alatKantorRumahTanggaModel->skipValidation(true);
                $insertResult = $this->alatKantorRumahTanggaModel->insert($data);
                
                if ($insertResult) {
                    $insertId = $this->alatKantorRumahTanggaModel->getInsertID();
                    session()->setFlashdata('success', "Data alat kantor rumah tangga berhasil disimpan! ID: {$insertId}");
                } else {
                    $errors = $this->alatKantorRumahTanggaModel->errors();
                    session()->setFlashdata('error', 'Gagal menyimpan data: ' . implode(', ', $errors));
                }
                
                $this->alatKantorRumahTanggaModel->skipValidation(false);
                
            } catch (\Exception $e) {
                session()->setFlashdata('error', 'Error database: ' . $e->getMessage());
                $this->alatKantorRumahTanggaModel->skipValidation(false);
            }
        }
        
        return redirect()->to('user/barang/peralatandanmesin/alatkantorrt/kelompokalatkantorrt');
    }

    // Method untuk reset semua data
    public function resetData()
    {
        try {
            $this->alatKantorRumahTanggaModel->builder()->truncate();
            
            session()->setFlashdata('success', 'Semua data berhasil dihapus!');
            return redirect()->to('user/barang/peralatandanmesin/alatkantorrt/kelompokalatkantorrt');
            
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
            // Ambil data dari API
            $apiData = $this->getApiData();

            if (empty($apiData)) {
                session()->setFlashdata('error', 'Tidak ada data dari API atau API tidak dapat diakses!');
                return redirect()->back();
            }

            $this->alatKantorRumahTanggaModel->skipValidation(true);

            // Kelompok yang valid untuk alat kantor dan rumah tangga
            $validKelompok = [
                'ALAT KANTOR',
                'ALAT RUMAH TANGGA'
            ];

            foreach ($apiData as $index => $item) {
                try {
                    $kode_barang = trim($item['kode_barang'] ?? '');
                    $kelompok_api_raw = strtoupper(trim($item['kelompok'] ?? ''));
                    $kelompok_api = $this->mapKelompokFromApi($kelompok_api_raw);
                    
                    if (empty($kode_barang)) {
                        $skipped++;
                        continue;
                    }

                    // FILTER: Hanya import data dengan kelompok alat kantor dan rumah tangga yang valid
                    if (!in_array($kelompok_api, $validKelompok)) {
                        $filtered++;
                        log_message('info', "Filtered out: {$kode_barang} - Kelompok: '{$kelompok_api}' (bukan alat kantor/rumah tangga)");
                        continue;
                    }

                    log_message('info', "Importing: {$kode_barang} - Kelompok: '{$kelompok_api}'");

                    $unique_kode = $kode_barang . '_' . $index;

                    // Mapping kelompok menggunakan method dari model
                    $kategori_detail = $this->alatKantorRumahTanggaModel->mapKelompokToKategori($kelompok_api);

                    $data = [
                        'kode_barang' => $unique_kode,
                        'nama_barang' => trim($item['nama_barang'] ?? '') ?: 'Unknown',
                        'nup' => trim($item['nup'] ?? ''),
                        'merk' => trim($item['merk'] ?? ''),
                        'kelompok' => $kelompok_api,
                        'sub_kelompok' => trim($item['sub_kelompok'] ?? ''),
                        'kondisi' => trim($item['kondisi'] ?? ''),
                        'kuantitas' => intval($item['kuantitas'] ?? 1),
                        'status_penggunaan' => trim($item['status_penggunaan'] ?? ''),
                        'nilai_perolehan' => $this->safeFloat($item['nilai_perolehan'] ?? 0),
                        'nilai_buku' => $this->safeFloat($item['nilai_buku'] ?? 0),
                        'tanggal_perolehan' => !empty($item['tanggal_perolehan']) ? $item['tanggal_perolehan'] : null,
                        'nama_satker' => trim($item['nama_satker'] ?? ''),
                        'spek_lain' => trim($item['spek_lain'] ?? ''),
                        'kategori_utama' => 'ALAT KANTOR DAN RUMAH TANGGA',
                        'kategori_detail' => $kategori_detail,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];

                    if ($this->alatKantorRumahTanggaModel->insert($data)) {
                        $imported++;
                        log_message('info', "Successfully imported: {$kode_barang}");
                    } else {
                        $errors[] = $kode_barang;
                        log_message('error', "Failed to import: {$kode_barang}");
                    }

                } catch (\Exception $e) {
                    $errors[] = ($kode_barang ?? 'unknown') . ': ' . $e->getMessage();
                    log_message('error', "Exception importing {$kode_barang}: " . $e->getMessage());
                }
            }

            $this->alatKantorRumahTanggaModel->skipValidation(false);

            $total = count($apiData);
            $message = "Import selesai! Total API: {$total}, Berhasil: {$imported}, Dilewati: {$skipped}, Difilter (bukan alat kantor/rumah tangga): {$filtered}";
            
            if (!empty($errors)) {
                $message .= ", Error: " . count($errors);
                log_message('error', 'Import errors: ' . implode(', ', $errors));
            }

            session()->setFlashdata('success', $message);
            return redirect()->to('user/barang/peralatandanmesin/alatkantorrt/kelompokalatkantorrt');

        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Gagal import data: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    // Method export ke CSV
    public function exportAlatKantorRumahTanggaList($jenis = 'semua')
    {
        $jenisValid = ['alatkantor', 'alatrumahangga', 'semua'];
        if (!in_array($jenis, $jenisValid)) {
            $jenis = 'semua';
        }

        $allAlatKantorRumahTanggaList = $this->alatKantorRumahTanggaModel->findAll();
        
        // Filter berdasarkan jenis
        if ($jenis !== 'semua') {
            $alatKantorRumahTanggaList = array_filter($allAlatKantorRumahTanggaList, function($item) use ($jenis) {
                $kelompok = strtolower($item['kelompok'] ?? '');
                
                switch ($jenis) {
                    case 'alatkantor':
                        return strpos($kelompok, 'alat kantor') !== false;
                    case 'alatrumahangga':
                        return strpos($kelompok, 'alat rumah tangga') !== false;
                    default:
                        return true;
                }
            });
            $alatKantorRumahTanggaList = array_values($alatKantorRumahTanggaList);
        } else {
            $alatKantorRumahTanggaList = $allAlatKantorRumahTanggaList;
        }

        $filename = 'alat_kantor_rumah_tangga_' . $jenis . '_' . date('Y-m-d') . '.csv';
        
        $response = service('response');
        $response->setHeader('Content-Type', 'text/csv');
        $response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        fputcsv($output, [
            'No', 'Kode Barang', 'Nama Barang', 'NUP', 'Merk', 'Kelompok', 'Sub Kelompok', 'Kondisi', 
            'Kuantitas', 'Status', 'Nilai Perolehan', 'Nilai Buku', 'Tanggal Perolehan', 'Nama Satker',
            'Spek Lain', 'Kategori Detail'
        ]);

        $no = 1;
        foreach ($alatKantorRumahTanggaList as $item) {
            fputcsv($output, [
                $no++,
                $item['kode_barang'] ?? '-',
                $item['nama_barang'] ?? '-',
                $item['nup'] ?? '-',
                $item['merk'] ?? '-',
                $item['kelompok'] ?? '-',
                $item['sub_kelompok'] ?? '-',
                $item['kondisi'] ?? '-',
                $item['kuantitas'] ?? '1',
                $item['status_penggunaan'] ?? '-',
                number_format(floatval($item['nilai_perolehan'] ?? 0), 2, ',', '.'),
                number_format(floatval($item['nilai_buku'] ?? 0), 2, ',', '.'),
                $item['tanggal_perolehan'] ?? '-',
                $item['nama_satker'] ?? '-',
                $item['spek_lain'] ?? '-',
                $item['kategori_detail'] ?? '-'
            ]);
        }

        fclose($output);
        return $response;
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

    // Method untuk cek statistik database
    public function stats()
    {
        $totalData = $this->alatKantorRumahTanggaModel->countAllResults();
        $apiData = $this->getApiData();
        $totalApi = count($apiData);
        
        // Statistik per kelompok
        $dbStats = [
            'total' => $totalData,
            'alat_kantor' => $this->alatKantorRumahTanggaModel->where('kelompok', 'ALAT KANTOR')->countAllResults(),
            'alat_rumah_tangga' => $this->alatKantorRumahTanggaModel->where('kelompok', 'ALAT RUMAH TANGGA')->countAllResults()
        ];
        
        return view('user/barang/peralatandanmesin/alatkantorrt/stats', [
            'totalData' => $totalData,
            'totalApi' => $totalApi,
            'dbStats' => $dbStats
        ]);
    }

    // Method untuk test API (debugging)
    public function testApi()
    {
        $apiData = $this->getApiData();
        
        echo "<h3>Test API Alat Kantor dan Rumah Tangga</h3>";
        echo "<p>Total data dari API: " . count($apiData) . "</p>";
        
        if (!empty($apiData)) {
            echo "<h4>Sample data pertama:</h4>";
            echo "<pre>" . json_encode($apiData[0], JSON_PRETTY_PRINT) . "</pre>";
            
            // Analisis kelompok
            $kelompokStats = [];
            foreach ($apiData as $item) {
                $kelompok = $item['kelompok'] ?? 'Unknown';
                $kelompokStats[$kelompok] = ($kelompokStats[$kelompok] ?? 0) + 1;
            }
            
            echo "<h4>Statistik Kelompok:</h4>";
            echo "<pre>" . json_encode($kelompokStats, JSON_PRETTY_PRINT) . "</pre>";
            
            // Filter untuk alat kantor dan rumah tangga
            $validKelompok = [
                'ALAT KANTOR',
                'ALAT RUMAH TANGGA'
            ];
            $filteredData = array_filter($apiData, function($item) use ($validKelompok) {
                return in_array(strtoupper($item['kelompok'] ?? ''), $validKelompok);
            });
            
            echo "<h4>Data yang akan diimport (kelompok alat kantor rumah tangga):</h4>";
            echo "<p>Total: " . count($filteredData) . " dari " . count($apiData) . " data</p>";
            
            if (!empty($filteredData)) {
                echo "<h5>Sample data alat kantor rumah tangga:</h5>";
                echo "<pre>" . json_encode(array_slice($filteredData, 0, 3), JSON_PRETTY_PRINT) . "</pre>";
            }
        } else {
            echo "<p style='color: red;'>Tidak ada data dari API atau terjadi error!</p>";
        }
    }

    // Method helper untuk mapping kelompok API
    private function mapKelompokFromApi($kelompok_api)
    {
        $kelompok_api = strtoupper(trim($kelompok_api));
        
        // Mapping berdasarkan nama kelompok yang ditemukan di API
        if (stripos($kelompok_api, 'ALAT KANTOR') !== false ||
            stripos($kelompok_api, 'KANTOR') !== false) {
            return 'ALAT KANTOR';
        } elseif (stripos($kelompok_api, 'ALAT RUMAH TANGGA') !== false ||
                  stripos($kelompok_api, 'RUMAH TANGGA') !== false) {
            return 'ALAT RUMAH TANGGA';
        }
        
        return $kelompok_api;
    }
}