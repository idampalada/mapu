<?php

namespace App\Controllers\User\Barang\PeralatanDanMesin;

use App\Controllers\BaseController;
use App\Models\AlatBengkelAlatUkurModel;

class AlatBengkelAlatUkur extends BaseController
{
    protected $alatBengkelAlatUkurModel;
    
    public function __construct()
    {
        $this->alatBengkelAlatUkurModel = new AlatBengkelAlatUkurModel();
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
        $alatBengkelAlatUkurList = $this->getApiData();
        return view('user/barang/peralatandanmesin/alatbengkelukur/dashboardalatbengkelukur', [
            'alatBengkelAlatUkurList' => $alatBengkelAlatUkurList
        ]);
    }

    public function kelompokAlatBengkelAlatUkur()
    {
        $sort = $this->request->getGet('sort') ?? 'kode_barang';
        $order = $this->request->getGet('order') ?? 'asc';
        
        $allAlatBengkelAlatUkurList = $this->alatBengkelAlatUkurModel->findAll();
        
        // Filter data berdasarkan kelompok - SELARAS DENGAN BANGUNAN AIR
        $alatBengkelBermesinData = array_filter($allAlatBengkelAlatUkurList, function ($item) {
            return strtoupper($item['kelompok'] ?? '') === 'ALAT BENGKEL BERMESIN';
        });

        $alatBengkelTakBermesinData = array_filter($allAlatBengkelAlatUkurList, function ($item) {
            return strtoupper($item['kelompok'] ?? '') === 'ALAT BENGKEL TAK BERMESIN';
        });

        $alatUkurData = array_filter($allAlatBengkelAlatUkurList, function ($item) {
            return strtoupper($item['kelompok'] ?? '') === 'ALAT UKUR';
        });

        // Reset array keys dan gabungkan
        $allData = array_merge(
            array_values($alatBengkelBermesinData),
            array_values($alatBengkelTakBermesinData), 
            array_values($alatUkurData)
        );

        return view('user/barang/peralatandanmesin/alatbengkelukur/kelompokalatbengkelukur', [
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
        if (!$this->alatBengkelAlatUkurModel->isValidKelompokAlatBengkelAlatUkur($kelompok)) {
            session()->setFlashdata('error', 'Kelompok tidak valid: ' . $kelompok);
            return redirect()->to('user/barang/peralatandanmesin/alatbengkelukur/kelompokalatbengkelukur');
        }
        
        // Debug: cek data di database
        $debugCount = $this->alatBengkelAlatUkurModel->where('UPPER(kelompok)', strtoupper($kelompok))->countAllResults();
        log_message('info', "Data found for kelompok '{$kelompok}': {$debugCount}");
        
        if ($debugCount == 0) {
            session()->setFlashdata('error', "Tidak ada data untuk kelompok: {$kelompok}");
            return redirect()->to('user/barang/peralatandanmesin/alatbengkelukur/kelompokalatbengkelukur');
        }
        
        $searchTerm = $this->request->getGet('search') ?? '';
        $sort = $this->request->getGet('sort') ?? 'kode_barang';
        $order = $this->request->getGet('order') ?? 'asc';
        $perPage = 100;
        $page = $this->request->getGet('page') ?? 1;

        // Gunakan database sebagai sumber data
        $builder = $this->alatBengkelAlatUkurModel->builder();
        
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
        $alatBengkelAlatUkurList = $builder->limit($perPage, $offset)->get()->getResultArray();

        // Setup pagination
        $pager = service('pager');
        $pager->setPath('user/barang/peralatandanmesin/alatbengkelukur/kelompokalatbengkelukur/' . urlencode($kelompok));
        $totalPages = ceil($totalItems / $perPage);

        return view('user/barang/peralatandanmesin/alatbengkelukur/kelompokalatbengkelukur', [
            'alatBengkelAlatUkurList' => $alatBengkelAlatUkurList,
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

    // Method untuk menambah alat bengkel ukur manual - SELARAS DENGAN BANGUNAN AIR
    public function tambah()
    {
        log_message('info', '=== TAMBAH ALAT BENGKEL UKUR METHOD DIPANGGIL ===');
        
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
            $processor = $data_source['processor'] ?? '';
            $memori = $data_source['memori'] ?? '';
            $hardisk = $data_source['hardisk'] ?? '';
            $spek_lain = $data_source['spek_lain'] ?? '';
            
            // Mapping kelompok menggunakan method dari model
            $kategori_detail = $this->alatBengkelAlatUkurModel->mapKelompokToKategori($kelompok);
            
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
                'processor' => trim($processor),
                'memori' => trim($memori),
                'hardisk' => trim($hardisk),
                'spek_lain' => trim($spek_lain),
                'kategori_utama' => 'ALAT BENGKEL DAN ALAT UKUR',
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
                $this->alatBengkelAlatUkurModel->skipValidation(true);
                $insertResult = $this->alatBengkelAlatUkurModel->insert($data);
                
                if ($insertResult) {
                    $insertId = $this->alatBengkelAlatUkurModel->getInsertID();
                    session()->setFlashdata('success', "Data alat bengkel ukur berhasil disimpan! ID: {$insertId}");
                } else {
                    $errors = $this->alatBengkelAlatUkurModel->errors();
                    session()->setFlashdata('error', 'Gagal menyimpan data: ' . implode(', ', $errors));
                }
                
                $this->alatBengkelAlatUkurModel->skipValidation(false);
                
            } catch (\Exception $e) {
                session()->setFlashdata('error', 'Error database: ' . $e->getMessage());
                $this->alatBengkelAlatUkurModel->skipValidation(false);
            }
        }
        
        return redirect()->to('user/barang/peralatandanmesin/alatbengkelukur/kelompokalatbengkelukur');
    }

    // Method untuk reset semua data
    public function resetData()
    {
        try {
            $this->alatBengkelAlatUkurModel->builder()->truncate();
            
            session()->setFlashdata('success', 'Semua data berhasil dihapus!');
            return redirect()->to('user/barang/peralatandanmesin/alatbengkelukur/kelompokalatbengkelukur');
            
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Gagal menghapus data: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    // Method import dari API - SELARAS DENGAN BANGUNAN AIR
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

            $this->alatBengkelAlatUkurModel->skipValidation(true);

            // Kelompok yang valid untuk alat bengkel dan ukur
            $validKelompok = [
                'ALAT BENGKEL BERMESIN',
                'ALAT BENGKEL TAK BERMESIN',
                'ALAT UKUR'
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

                    // FILTER: Hanya import data dengan kelompok alat bengkel dan ukur yang valid
                    if (!in_array($kelompok_api, $validKelompok)) {
                        $filtered++;
                        log_message('info', "Filtered out: {$kode_barang} - Kelompok: '{$kelompok_api}' (bukan alat bengkel ukur)");
                        continue;
                    }

                    log_message('info', "Importing: {$kode_barang} - Kelompok: '{$kelompok_api}'");

                    $unique_kode = $kode_barang . '_' . $index;

                    // Mapping kelompok menggunakan method dari model
                    $kategori_detail = $this->alatBengkelAlatUkurModel->mapKelompokToKategori($kelompok_api);

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
                        'processor' => trim($item['processor'] ?? ''),
                        'memori' => trim($item['memori'] ?? ''),
                        'hardisk' => trim($item['hardisk'] ?? ''),
                        'monitor' => trim($item['monitor'] ?? ''),
                        'spek_lain' => trim($item['spek_lain'] ?? ''),
                        'kategori_utama' => 'ALAT BENGKEL DAN ALAT UKUR',
                        'kategori_detail' => $kategori_detail,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];

                    if ($this->alatBengkelAlatUkurModel->insert($data)) {
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

            $this->alatBengkelAlatUkurModel->skipValidation(false);

            $total = count($apiData);
            $message = "Import selesai! Total API: {$total}, Berhasil: {$imported}, Dilewati: {$skipped}, Difilter (bukan alat bengkel ukur): {$filtered}";
            
            if (!empty($errors)) {
                $message .= ", Error: " . count($errors);
                log_message('error', 'Import errors: ' . implode(', ', $errors));
            }

            session()->setFlashdata('success', $message);
            return redirect()->to('user/barang/peralatandanmesin/alatbengkelukur/kelompokalatbengkelukur');

        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Gagal import data: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    // Method export ke CSV - SELARAS DENGAN BANGUNAN AIR
    public function exportAlatBengkelAlatUkurList($jenis = 'semua')
    {
        $jenisValid = ['bengkelbermesin', 'bengkeltakbermesin', 'alatukur', 'semua'];
        if (!in_array($jenis, $jenisValid)) {
            $jenis = 'semua';
        }

        $allAlatBengkelAlatUkurList = $this->alatBengkelAlatUkurModel->findAll();
        
        // Filter berdasarkan jenis
        if ($jenis !== 'semua') {
            $alatBengkelAlatUkurList = array_filter($allAlatBengkelAlatUkurList, function($item) use ($jenis) {
                $kelompok = strtolower($item['kelompok'] ?? '');
                
                switch ($jenis) {
                    case 'bengkelbermesin':
                        return strpos($kelompok, 'alat bengkel bermesin') !== false;
                    case 'bengkeltakbermesin':
                        return strpos($kelompok, 'alat bengkel tak bermesin') !== false;
                    case 'alatukur':
                        return strpos($kelompok, 'alat ukur') !== false;
                    default:
                        return true;
                }
            });
            $alatBengkelAlatUkurList = array_values($alatBengkelAlatUkurList);
        } else {
            $alatBengkelAlatUkurList = $allAlatBengkelAlatUkurList;
        }

        $filename = 'alat_bengkel_ukur_' . $jenis . '_' . date('Y-m-d') . '.csv';
        
        $response = service('response');
        $response->setHeader('Content-Type', 'text/csv');
        $response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        fputcsv($output, [
            'No', 'Kode Barang', 'Nama Barang', 'NUP', 'Merk', 'Kelompok', 'Sub Kelompok', 'Kondisi', 
            'Kuantitas', 'Status', 'Nilai Perolehan', 'Nilai Buku', 'Tanggal Perolehan', 'Nama Satker',
            'Processor', 'Memori', 'Hardisk', 'Spek Lain', 'Kategori Detail'
        ]);

        $no = 1;
        foreach ($alatBengkelAlatUkurList as $item) {
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
                $item['processor'] ?? '-',
                $item['memori'] ?? '-',
                $item['hardisk'] ?? '-',
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
        $totalData = $this->alatBengkelAlatUkurModel->countAllResults();
        $apiData = $this->getApiData();
        $totalApi = count($apiData);
        
        // Statistik per kelompok
        $dbStats = [
            'total' => $totalData,
            'bengkel_bermesin' => $this->alatBengkelAlatUkurModel->where('kelompok', 'ALAT BENGKEL BERMESIN')->countAllResults(),
            'bengkel_tak_bermesin' => $this->alatBengkelAlatUkurModel->where('kelompok', 'ALAT BENGKEL TAK BERMESIN')->countAllResults(),
            'alat_ukur' => $this->alatBengkelAlatUkurModel->where('kelompok', 'ALAT UKUR')->countAllResults()
        ];
        
        return view('user/barang/peralatandanmesin/alatbengkelukur/stats', [
            'totalData' => $totalData,
            'totalApi' => $totalApi,
            'dbStats' => $dbStats
        ]);
    }

    // Method untuk test API (debugging)
    public function testApi()
    {
        $apiData = $this->getApiData();
        
        echo "<h3>Test API Alat Bengkel dan Alat Ukur</h3>";
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
            
            // Filter untuk alat bengkel dan ukur
            $validKelompok = [
                'ALAT BENGKEL BERMESIN',
                'ALAT BENGKEL TAK BERMESIN',
                'ALAT UKUR'
            ];
            $filteredData = array_filter($apiData, function($item) use ($validKelompok) {
                return in_array(strtoupper($item['kelompok'] ?? ''), $validKelompok);
            });
            
            echo "<h4>Data yang akan diimport (kelompok alat bengkel ukur):</h4>";
            echo "<p>Total: " . count($filteredData) . " dari " . count($apiData) . " data</p>";
            
            if (!empty($filteredData)) {
                echo "<h5>Sample data alat bengkel ukur:</h5>";
                echo "<pre>" . json_encode(array_slice($filteredData, 0, 3), JSON_PRETTY_PRINT) . "</pre>";
            }
        } else {
            echo "<p style='color: red;'>Tidak ada data dari API atau terjadi error!</p>";
        }
    }

    // Method helper untuk mapping kelompok API - SELARAS DENGAN BANGUNAN AIR
    private function mapKelompokFromApi($kelompok_api)
    {
        $kelompok_api = strtoupper(trim($kelompok_api));
        
        // Mapping berdasarkan nama kelompok yang ditemukan di API
        if (stripos($kelompok_api, 'ALAT BENGKEL BERMESIN') !== false ||
            stripos($kelompok_api, 'BENGKEL BERMESIN') !== false) {
            return 'ALAT BENGKEL BERMESIN';
        } elseif (stripos($kelompok_api, 'ALAT BENGKEL TAK BERMESIN') !== false ||
                  stripos($kelompok_api, 'BENGKEL TAK BERMESIN') !== false) {
            return 'ALAT BENGKEL TAK BERMESIN';
        } elseif (stripos($kelompok_api, 'ALAT UKUR') !== false ||
                  stripos($kelompok_api, 'UKUR') !== false) {
            return 'ALAT UKUR';
        }
        
        return $kelompok_api;
    }
}