<?php

namespace App\Controllers\User\Barang\PeralatanDanMesin;

use App\Controllers\BaseController;
use App\Models\AlatAngkutanModel;

class AlatAngkutan extends BaseController
{
    protected $alatAngkutanModel;
    
    public function __construct()
    {
        $this->alatAngkutanModel = new AlatAngkutanModel();
    }

    // Method untuk mengambil data dari API
    private function getApiData($url = null)
    {
        $client = \Config\Services::curlrequest();
        $apiKey = 'c877acaa0de297a9e3b8bbdb101dd254d33a92a0444b979d599e04fdeaccdbc5';
        
        if (!$url) {
            $url = "https://apigw.pu.go.id/v1/siman/alat-angkutan?api_key={$apiKey}";
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
        $alatAngkutanList = $this->getApiData();
        return view('user/alatangkutan/dashboardalatangkutan', [
            'alatAngkutanList' => $alatAngkutanList
        ]);
    }

    public function kelompokAlatAngkutan()
    {
        $sort = $this->request->getGet('sort') ?? 'kode_barang';
        $order = $this->request->getGet('order') ?? 'asc';
        
        // Menggunakan data dari database untuk konsistensi
        $allAlatAngkutanList = $this->alatAngkutanModel->findAll();
        
        // Filter data berdasarkan kelompok
        $daratBermotorData = array_filter($allAlatAngkutanList, function ($item) {
            return strtolower($item['kelompok'] ?? '') === 'alat angkutan darat bermotor';
        });

        $daratTakBermotorData = array_filter($allAlatAngkutanList, function ($item) {
            return strtolower($item['kelompok'] ?? '') === 'alat angkutan darat tak bermotor';
        });

        $apungBermotorData = array_filter($allAlatAngkutanList, function ($item) {
            return strtolower($item['kelompok'] ?? '') === 'alat angkutan apung bermotor';
        });

        $apungTakBermotorData = array_filter($allAlatAngkutanList, function ($item) {
            return strtolower($item['kelompok'] ?? '') === 'alat angkutan apung tak bermotor';
        });

        $bermotorUdaraData = array_filter($allAlatAngkutanList, function ($item) {
            return strtolower($item['kelompok'] ?? '') === 'alat angkutan bermotor udara';
        });

        // Reset array keys
        $daratBermotorData = array_values($daratBermotorData);
        $daratTakBermotorData = array_values($daratTakBermotorData);
        $apungBermotorData = array_values($apungBermotorData);
        $apungTakBermotorData = array_values($apungTakBermotorData);
        $bermotorUdaraData = array_values($bermotorUdaraData);
        
        // Gabungkan semua data
        $allData = array_merge($daratBermotorData, $daratTakBermotorData, $apungBermotorData, $apungTakBermotorData, $bermotorUdaraData);

        return view('user/barang/peralatandanmesin/alatangkutan/kelompokalatangkutan', [
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
        $builder = $this->alatAngkutanModel->builder();
        
        // Filter berdasarkan kelompok
        $builder->where('UPPER(kelompok)', strtoupper($kelompok));
        
        // Filter berdasarkan pencarian
        if (!empty($searchTerm)) {
            $builder->groupStart()
                ->like('nama_barang', $searchTerm)
                ->orLike('kode_barang', $searchTerm) 
                ->orLike('merk', $searchTerm)
                ->orLike('no_mesin', $searchTerm)
                ->orLike('no_rangka', $searchTerm)
                ->orLike('no_polisi', $searchTerm)
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
        $alatAngkutanList = $builder->limit($perPage, $offset)->get()->getResultArray();

        // Setup pagination
        $pager = service('pager');
        $pager->setPath('user/barang/peralatandanmesin/alatangkutan/kelompokalatangkutan/' . urlencode($kelompok));
        $totalPages = ceil($totalItems / $perPage);

        return view('user/barang/peralatandanmesin/alatangkutan/kelompokalatangkutan', [
            'alatAngkutanList' => $alatAngkutanList,
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

    // Method untuk menambah alat angkutan manual
    public function tambah()
    {
        log_message('info', '=== TAMBAH ALAT ANGKUTAN METHOD DIPANGGIL ===');
        
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
            $kondisi = $data_source['kondisi'] ?? '';
            $kuantitas = $data_source['kuantitas'] ?? '';
            $status_penggunaan = $data_source['status_penggunaan'] ?? '';
            $thn_buat = $data_source['thn_buat'] ?? '';
            $no_mesin = $data_source['no_mesin'] ?? '';
            $no_rangka = $data_source['no_rangka'] ?? '';
            $no_polisi = $data_source['no_polisi'] ?? '';
            $daya_mesin = $data_source['daya_mesin'] ?? '';
            $bhn_bakar = $data_source['bhn_bakar'] ?? '';
            $nilai_perolehan = $data_source['nilai_perolehan'] ?? '';
            $tanggal_perolehan = $data_source['tanggal_perolehan'] ?? '';
            
            $data = [
                'kode_barang' => trim($kode_barang),
                'nama_barang' => trim($nama_barang),
                'nup' => trim($nup),
                'merk' => trim($merk),
                'kelompok' => strtoupper(trim($kelompok)),
                'kondisi' => trim($kondisi),
                'kuantitas' => intval($kuantitas ?: 1),
                'status_penggunaan' => trim($status_penggunaan),
                'thn_buat' => trim($thn_buat),
                'no_mesin' => trim($no_mesin),
                'no_rangka' => trim($no_rangka),
                'no_polisi' => trim($no_polisi),
                'daya_mesin' => trim($daya_mesin),
                'bhn_bakar' => trim($bhn_bakar),
                'nilai_perolehan' => $this->safeFloat($nilai_perolehan),
                'tanggal_perolehan' => !empty($tanggal_perolehan) ? $tanggal_perolehan : null,
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
                $this->alatAngkutanModel->skipValidation(true);
                $insertResult = $this->alatAngkutanModel->insert($data);
                
                if ($insertResult) {
                    $insertId = $this->alatAngkutanModel->getInsertID();
                    session()->setFlashdata('success', "Data alat angkutan berhasil disimpan! ID: {$insertId}");
                } else {
                    $errors = $this->alatAngkutanModel->errors();
                    session()->setFlashdata('error', 'Gagal menyimpan data: ' . implode(', ', $errors));
                }
                
                $this->alatAngkutanModel->skipValidation(false);
                
            } catch (\Exception $e) {
                session()->setFlashdata('error', 'Error database: ' . $e->getMessage());
                $this->alatAngkutanModel->skipValidation(false);
            }
        }
        
        return redirect()->to('user/barang/peralatandanmesin/alatangkutan/kelompokalatangkutan');
    }

    // Method untuk reset semua data
    public function resetData()
    {
        try {
            $this->alatAngkutanModel->builder()->truncate();
            
            session()->setFlashdata('success', 'Semua data berhasil dihapus!');
            return redirect()->to('user/barang/peralatandanmesin/alatangkutan/kelompokalatangkutan');
            
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

            $this->alatAngkutanModel->skipValidation(true);

            // Kelompok yang valid untuk alat angkutan
            $validKelompok = [
                'ALAT ANGKUTAN DARAT BERMOTOR', 
                'ALAT ANGKUTAN DARAT TAK BERMOTOR',
                'ALAT ANGKUTAN APUNG BERMOTOR', 
                'ALAT ANGKUTAN APUNG TAK BERMOTOR',
                'ALAT ANGKUTAN BERMOTOR UDARA'
            ];

            foreach ($apiData as $index => $item) {
                try {
                    $kode_barang = trim($item['kode_barang'] ?? '');
                    $kelompok_api = strtoupper(trim($item['kelompok'] ?? ''));
                    
                    if (empty($kode_barang)) {
                        $skipped++;
                        continue;
                    }

                    // FILTER: Hanya import data dengan kelompok alat angkutan yang valid
                    if (!in_array($kelompok_api, $validKelompok)) {
                        $filtered++;
                        log_message('info', "Filtered out: {$kode_barang} - Kelompok: '{$kelompok_api}' (bukan alat angkutan)");
                        continue;
                    }

                    log_message('info', "Importing: {$kode_barang} - Kelompok: '{$kelompok_api}'");

                    $unique_kode = $kode_barang . '_' . $index;

                    $data = [
                        'kode_barang' => $unique_kode,
                        'nama_barang' => trim($item['nama_barang'] ?? '') ?: 'Unknown',
                        'nup' => trim($item['nup'] ?? ''),
                        'merk' => trim($item['merk'] ?? ''),
                        'kelompok' => $kelompok_api,
                        'kondisi' => trim($item['kondisi'] ?? ''),
                        'kuantitas' => intval($item['kuantitas'] ?? 1),
                        'status_penggunaan' => trim($item['status_penggunaan'] ?? ''),
                        'thn_buat' => trim($item['thn_buat'] ?? ''),
                        'no_mesin' => trim($item['no_mesin'] ?? ''),
                        'no_rangka' => trim($item['no_rangka'] ?? ''),
                        'no_polisi' => trim($item['no_polisi'] ?? ''),
                        'daya_mesin' => trim($item['daya_mesin'] ?? ''),
                        'bhn_bakar' => trim($item['bhn_bakar'] ?? ''),
                        'nilai_perolehan' => $this->safeFloat($item['nilai_perolehan'] ?? 0),
                        'tanggal_perolehan' => !empty($item['tanggal_perolehan']) ? $item['tanggal_perolehan'] : null,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];

                    if ($this->alatAngkutanModel->insert($data)) {
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

            $this->alatAngkutanModel->skipValidation(false);

            $total = count($apiData);
            $message = "Import selesai! Total API: {$total}, Berhasil: {$imported}, Dilewati: {$skipped}, Difilter (bukan alat angkutan): {$filtered}";
            
            if (!empty($errors)) {
                $message .= ", Error: " . count($errors);
                log_message('error', 'Import errors: ' . implode(', ', $errors));
            }

            session()->setFlashdata('success', $message);
            return redirect()->to('user/barang/peralatandanmesin/alatangkutan/kelompokalatangkutan');

        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Gagal import data: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    // Method export ke CSV
    public function exportAlatAngkutanList($jenis = 'semua')
    {
        $jenisValid = ['darat-bermotor', 'darat-tak-bermotor', 'apung-bermotor', 'apung-tak-bermotor', 'bermotor-udara', 'semua'];
        if (!in_array($jenis, $jenisValid)) {
            $jenis = 'semua';
        }

        $allAlatAngkutanList = $this->alatAngkutanModel->findAll();
        
        // Filter berdasarkan jenis
        if ($jenis !== 'semua') {
            $alatAngkutanList = array_filter($allAlatAngkutanList, function($item) use ($jenis) {
                $kelompok = strtolower($item['kelompok'] ?? '');
                
                switch ($jenis) {
                    case 'darat-bermotor':
                        return strpos($kelompok, 'alat angkutan darat bermotor') !== false;
                    case 'darat-tak-bermotor':
                        return strpos($kelompok, 'alat angkutan darat tak bermotor') !== false;
                    case 'apung-bermotor':
                        return strpos($kelompok, 'alat angkutan apung bermotor') !== false;
                    case 'apung-tak-bermotor':
                        return strpos($kelompok, 'alat angkutan apung tak bermotor') !== false;
                    case 'bermotor-udara':
                        return strpos($kelompok, 'alat angkutan bermotor udara') !== false;
                    default:
                        return true;
                }
            });
            $alatAngkutanList = array_values($alatAngkutanList);
        } else {
            $alatAngkutanList = $allAlatAngkutanList;
        }

        $filename = 'alat_angkutan_' . $jenis . '_' . date('Y-m-d') . '.csv';
        
        $response = service('response');
        $response->setHeader('Content-Type', 'text/csv');
        $response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        fputcsv($output, [
            'No', 'Kode Barang', 'Nama Barang', 'NUP', 'Merk', 'Kelompok', 'Kondisi', 
            'Kuantitas', 'Status', 'Tahun Buat', 'No Mesin', 'No Rangka', 'No Polisi',
            'Daya Mesin', 'Bahan Bakar', 'Nilai Perolehan', 'Tanggal Perolehan'
        ]);

        $no = 1;
        foreach ($alatAngkutanList as $item) {
            fputcsv($output, [
                $no++,
                $item['kode_barang'] ?? '-',
                $item['nama_barang'] ?? '-',
                $item['nup'] ?? '-',
                $item['merk'] ?? '-',
                $item['kelompok'] ?? '-',
                $item['kondisi'] ?? '-',
                $item['kuantitas'] ?? '1',
                $item['status_penggunaan'] ?? '-',
                $item['thn_buat'] ?? '-',
                $item['no_mesin'] ?? '-',
                $item['no_rangka'] ?? '-',
                $item['no_polisi'] ?? '-',
                $item['daya_mesin'] ?? '-',
                $item['bhn_bakar'] ?? '-',
                number_format(floatval($item['nilai_perolehan'] ?? 0), 2, ',', '.'),
                $item['tanggal_perolehan'] ?? '-',
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
        $totalData = $this->alatAngkutanModel->countAllResults();
        $apiData = $this->getApiData();
        $totalApi = count($apiData);
        
        // Statistik per kelompok
        $dbStats = [
            'total' => $totalData,
            'darat_bermotor' => $this->alatAngkutanModel->where('kelompok', 'ALAT ANGKUTAN DARAT BERMOTOR')->countAllResults(),
            'darat_tak_bermotor' => $this->alatAngkutanModel->where('kelompok', 'ALAT ANGKUTAN DARAT TAK BERMOTOR')->countAllResults(),
            'apung_bermotor' => $this->alatAngkutanModel->where('kelompok', 'ALAT ANGKUTAN APUNG BERMOTOR')->countAllResults(),
            'apung_tak_bermotor' => $this->alatAngkutanModel->where('kelompok', 'ALAT ANGKUTAN APUNG TAK BERMOTOR')->countAllResults(),
            'bermotor_udara' => $this->alatAngkutanModel->where('kelompok', 'ALAT ANGKUTAN BERMOTOR UDARA')->countAllResults(),
        ];
        
        return view('user/alatangkutan/stats', [
            'totalData' => $totalData,
            'totalApi' => $totalApi,
            'dbStats' => $dbStats
        ]);
    }

    // Method untuk test API (debugging)
    public function testApi()
    {
        $apiData = $this->getApiData();
        
        echo "<h3>Test API Alat Angkutan</h3>";
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
            
            // Filter untuk alat angkutan
            $validKelompok = [
                'ALAT ANGKUTAN DARAT BERMOTOR', 
                'ALAT ANGKUTAN DARAT TAK BERMOTOR',
                'ALAT ANGKUTAN APUNG BERMOTOR', 
                'ALAT ANGKUTAN APUNG TAK BERMOTOR',
                'ALAT ANGKUTAN BERMOTOR UDARA'
            ];
            $filteredData = array_filter($apiData, function($item) use ($validKelompok) {
                return in_array(strtoupper($item['kelompok'] ?? ''), $validKelompok);
            });
            
            echo "<h4>Data yang akan diimport (kelompok alat angkutan):</h4>";
            echo "<p>Total: " . count($filteredData) . " dari " . count($apiData) . " data</p>";
            
            if (!empty($filteredData)) {
                echo "<h5>Sample data alat angkutan:</h5>";
                echo "<pre>" . json_encode(array_slice($filteredData, 0, 3), JSON_PRETTY_PRINT) . "</pre>";
            }
        } else {
            echo "<p style='color: red;'>Tidak ada data dari API atau terjadi error!</p>";
        }
    }
}