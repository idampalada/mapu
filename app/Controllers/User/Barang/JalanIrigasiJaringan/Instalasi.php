<?php

namespace App\Controllers\User\Barang\JalanIrigasiJaringan;

use App\Controllers\BaseController;
use App\Models\InstalasiModel;

class Instalasi extends BaseController
{
    protected $instalasiModel;
    
    public function __construct()
    {
        $this->instalasiModel = new InstalasiModel();
    }

    // Method untuk mengambil data dari API
    private function getApiData($url = null)
    {
        $client = \Config\Services::curlrequest();
        $apiKey = 'c877acaa0de297a9e3b8bbdb101dd254d33a92a0444b979d599e04fdeaccdbc5';
        
        if (!$url) {
            $url = "https://apigw.pu.go.id/v1/siman/instalasi-jaringan?api_key={$apiKey}";
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
        $instalasiList = $this->getApiData();
        return view('user/instalasi/dashboardinstalasi', [
            'instalasiList' => $instalasiList
        ]);
    }

    public function kelompokInstalasi()
    {
        $sort = $this->request->getGet('sort') ?? 'kode_barang';
        $order = $this->request->getGet('order') ?? 'asc';
        
        $allInstalasiList = $this->instalasiModel->findAll();
        
        // FILTER berdasarkan kelompok - gunakan strtoupper untuk konsistensi
        $airBersihData = array_filter($allInstalasiList, function ($item) {
            return strtoupper($item['kelompok'] ?? '') === 'INSTALASI AIR BERSIH/AIR BAKU';
        });

        $airKotorData = array_filter($allInstalasiList, function ($item) {
            return strtoupper($item['kelompok'] ?? '') === 'INSTALASI AIR KOTOR';
        });

        $sampahData = array_filter($allInstalasiList, function ($item) {
            return strtoupper($item['kelompok'] ?? '') === 'INSTALASI PENGOLAHAN SAMPAH';
        });

        $bahanBangunanData = array_filter($allInstalasiList, function ($item) {
            return strtoupper($item['kelompok'] ?? '') === 'INSTALASI PENGOLAHAN BAHAN BANGUNAN';
        });

        $listrikData = array_filter($allInstalasiList, function ($item) {
            return strtoupper($item['kelompok'] ?? '') === 'INSTALASI PEMBANGKIT LISTRIK';
        });

        $garduData = array_filter($allInstalasiList, function ($item) {
            return strtoupper($item['kelompok'] ?? '') === 'INSTALASI GARDU LISTRIK';
        });

        $pertahananData = array_filter($allInstalasiList, function ($item) {
            return strtoupper($item['kelompok'] ?? '') === 'INSTALASI PERTAHANAN';
        });

        $gasData = array_filter($allInstalasiList, function ($item) {
            return strtoupper($item['kelompok'] ?? '') === 'INSTALASI GAS';
        });

        $pengamanData = array_filter($allInstalasiList, function ($item) {
            return strtoupper($item['kelompok'] ?? '') === 'INSTALASI PENGAMAN';
        });

        $lainData = array_filter($allInstalasiList, function ($item) {
            return strtoupper($item['kelompok'] ?? '') === 'INSTALASI LAIN';
        });

        // Reset array keys dan gabungkan
        $allData = array_merge(
            array_values($airBersihData),
            array_values($airKotorData), 
            array_values($sampahData),
            array_values($bahanBangunanData),
            array_values($listrikData),
            array_values($garduData),
            array_values($pertahananData),
            array_values($gasData),
            array_values($pengamanData),
            array_values($lainData)
        );

        return view('user/barang/jalanirigasijaringan/instalasi/kelompokinstalasi', [
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
        if (!$this->instalasiModel->isValidKelompokInstalasi($kelompok)) {
            session()->setFlashdata('error', 'Kelompok tidak valid: ' . $kelompok);
            return redirect()->to('user/barang/jalanirigasijaringan/instalasi/kelompokinstalasi');
        }
        
        // Debug: cek data di database
        $debugCount = $this->instalasiModel->where('UPPER(kelompok)', strtoupper($kelompok))->countAllResults();
        log_message('info', "Data found for kelompok '{$kelompok}': {$debugCount}");
        
        if ($debugCount == 0) {
            session()->setFlashdata('error', "Tidak ada data untuk kelompok: {$kelompok}");
            return redirect()->to('user/barang/jalanirigasijaringan/instalasi/kelompokinstalasi');
        }
        
        $searchTerm = $this->request->getGet('search') ?? '';
        $sort = $this->request->getGet('sort') ?? 'kode_barang';
        $order = $this->request->getGet('order') ?? 'asc';
        $perPage = 100;
        $page = $this->request->getGet('page') ?? 1;

        // Gunakan database sebagai sumber data
        $builder = $this->instalasiModel->builder();
        
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
        $instalasiList = $builder->limit($perPage, $offset)->get()->getResultArray();

        // Setup pagination
        $pager = service('pager');
        $pager->setPath('user/barang/jalanirigasijaringan/instalasi/kelompokinstalasi/' . urlencode($kelompok));
        $totalPages = ceil($totalItems / $perPage);

        return view('user/barang/jalanirigasijaringan/instalasi/kelompokinstalasi', [
            'instalasiList' => $instalasiList,
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

    // Method untuk menambah instalasi manual
    public function tambah()
    {
        log_message('info', '=== TAMBAH INSTALASI METHOD DIPANGGIL ===');
        
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
            $kapasitas = $data_source['kapasitas'] ?? '';
            
            // Mapping kelompok menggunakan method dari model
            $kategori_detail = $this->instalasiModel->mapKelompokToKategori($kelompok);
            
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
                'kapasitas' => $this->safeFloat($kapasitas),
                'kategori_utama' => 'INSTALASI',
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
                $this->instalasiModel->skipValidation(true);
                $insertResult = $this->instalasiModel->insert($data);
                
                if ($insertResult) {
                    $insertId = $this->instalasiModel->getInsertID();
                    session()->setFlashdata('success', "Data instalasi berhasil disimpan! ID: {$insertId}");
                } else {
                    $errors = $this->instalasiModel->errors();
                    session()->setFlashdata('error', 'Gagal menyimpan data: ' . implode(', ', $errors));
                }
                
                $this->instalasiModel->skipValidation(false);
                
            } catch (\Exception $e) {
                session()->setFlashdata('error', 'Error database: ' . $e->getMessage());
                $this->instalasiModel->skipValidation(false);
            }
        }
        
        return redirect()->to('user/barang/jalanirigasijaringan/instalasi/kelompokinstalasi');
    }

    // Method untuk reset semua data
    public function resetData()
    {
        try {
            $this->instalasiModel->builder()->truncate();
            
            session()->setFlashdata('success', 'Semua data berhasil dihapus!');
            return redirect()->to('user/barang/jalanirigasijaringan/instalasi/kelompokinstalasi');
            
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

            $this->instalasiModel->skipValidation(true);

            // Kelompok yang valid untuk instalasi
            $validKelompok = [
                'INSTALASI AIR BERSIH/AIR BAKU',
                'INSTALASI AIR KOTOR',
                'INSTALASI PENGOLAHAN SAMPAH',
                'INSTALASI PENGOLAHAN BAHAN BANGUNAN',
                'INSTALASI PEMBANGKIT LISTRIK',
                'INSTALASI GARDU LISTRIK',
                'INSTALASI PERTAHANAN',
                'INSTALASI GAS',
                'INSTALASI PENGAMAN',
                'INSTALASI LAIN'
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

                    // FILTER: Hanya import data dengan kelompok instalasi yang valid
                    if (!in_array($kelompok_api, $validKelompok)) {
                        $filtered++;
                        log_message('info', "Filtered out: {$kode_barang} - Kelompok: '{$kelompok_api}' (bukan instalasi)");
                        continue;
                    }

                    log_message('info', "Importing: {$kode_barang} - Kelompok: '{$kelompok_api}'");

                    $unique_kode = $kode_barang . '_' . $index;

                    // Mapping kelompok menggunakan method dari model
                    $kategori_detail = $this->instalasiModel->mapKelompokToKategori($kelompok_api);

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
                        'kapasitas' => $this->safeFloat($item['kapasitas'] ?? 0),
                        'kategori_utama' => 'INSTALASI',
                        'kategori_detail' => $kategori_detail,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];

                    if ($this->instalasiModel->insert($data)) {
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

            $this->instalasiModel->skipValidation(false);

            $total = count($apiData);
            $message = "Import selesai! Total API: {$total}, Berhasil: {$imported}, Dilewati: {$skipped}, Difilter (bukan instalasi): {$filtered}";
            
            if (!empty($errors)) {
                $message .= ", Error: " . count($errors);
                log_message('error', 'Import errors: ' . implode(', ', $errors));
            }

            session()->setFlashdata('success', $message);
            return redirect()->to('user/barang/jalanirigasijaringan/instalasi/kelompokinstalasi');

        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Gagal import data: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    // Method export ke CSV
    public function exportInstalasiList($jenis = 'semua')
    {
        $jenisValid = ['airbersih', 'airkotor', 'sampah', 'bahanbangunan', 'listrik', 'gardu', 'pertahanan', 'gas', 'pengaman', 'lain', 'semua'];
        if (!in_array($jenis, $jenisValid)) {
            $jenis = 'semua';
        }

        $allInstalasiList = $this->instalasiModel->findAll();
        
        // Filter berdasarkan jenis
        if ($jenis !== 'semua') {
            $instalasiList = array_filter($allInstalasiList, function($item) use ($jenis) {
                $kelompok = strtolower($item['kelompok'] ?? '');
                
                switch ($jenis) {
                    case 'airbersih':
                        return strpos($kelompok, 'instalasi air bersih/air baku') !== false;
                    case 'airkotor':
                        return strpos($kelompok, 'instalasi air kotor') !== false;
                    case 'sampah':
                        return strpos($kelompok, 'instalasi pengolahan sampah') !== false;
                    case 'bahanbangunan':
                        return strpos($kelompok, 'instalasi pengolahan bahan bangunan') !== false;
                    case 'listrik':
                        return strpos($kelompok, 'instalasi pembangkit listrik') !== false;
                    case 'gardu':
                        return strpos($kelompok, 'instalasi gardu listrik') !== false;
                    case 'pertahanan':
                        return strpos($kelompok, 'instalasi pertahanan') !== false;
                    case 'gas':
                        return strpos($kelompok, 'instalasi gas') !== false;
                    case 'pengaman':
                        return strpos($kelompok, 'instalasi pengaman') !== false;
                    case 'lain':
                        return strpos($kelompok, 'instalasi lain') !== false;
                    default:
                        return true;
                }
            });
            $instalasiList = array_values($instalasiList);
        } else {
            $instalasiList = $allInstalasiList;
        }

        $filename = 'instalasi_' . $jenis . '_' . date('Y-m-d') . '.csv';
        
        $response = service('response');
        $response->setHeader('Content-Type', 'text/csv');
        $response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        fputcsv($output, [
            'No', 'Kode Barang', 'Nama Barang', 'NUP', 'Merk', 'Kelompok', 'Sub Kelompok', 'Kondisi', 
            'Kuantitas', 'Status', 'Nilai Perolehan', 'Nilai Buku', 'Tanggal Perolehan', 'Nama Satker',
            'Kapasitas', 'Kategori Detail'
        ]);

        $no = 1;
        foreach ($instalasiList as $item) {
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
                number_format(floatval($item['kapasitas'] ?? 0), 2, ',', '.'),
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
        $totalData = $this->instalasiModel->countAllResults();
        
        // Statistik per kelompok
        $dbStats = [
            'total' => $totalData,
            'air_bersih' => $this->instalasiModel->where('kelompok', 'INSTALASI AIR BERSIH/AIR BAKU')->countAllResults(),
            'air_kotor' => $this->instalasiModel->where('kelompok', 'INSTALASI AIR KOTOR')->countAllResults(),
            'sampah' => $this->instalasiModel->where('kelompok', 'INSTALASI PENGOLAHAN SAMPAH')->countAllResults(),
            'bahan_bangunan' => $this->instalasiModel->where('kelompok', 'INSTALASI PENGOLAHAN BAHAN BANGUNAN')->countAllResults(),
            'listrik' => $this->instalasiModel->where('kelompok', 'INSTALASI PEMBANGKIT LISTRIK')->countAllResults(),
            'gardu' => $this->instalasiModel->where('kelompok', 'INSTALASI GARDU LISTRIK')->countAllResults(),
            'pertahanan' => $this->instalasiModel->where('kelompok', 'INSTALASI PERTAHANAN')->countAllResults(),
            'gas' => $this->instalasiModel->where('kelompok', 'INSTALASI GAS')->countAllResults(),
            'pengaman' => $this->instalasiModel->where('kelompok', 'INSTALASI PENGAMAN')->countAllResults(),
            'lain' => $this->instalasiModel->where('kelompok', 'INSTALASI LAIN')->countAllResults()
        ];
        
        return view('user/instalasi/stats', [
            'totalData' => $totalData,
            'dbStats' => $dbStats
        ]);
    }

    // Method untuk test API (debugging)
    public function testApi()
    {
        echo "<h2>🔍 DEBUG API INSTALASI</h2>";
        echo "<hr>";
        
        $apiKey = 'c877acaa0de297a9e3b8bbdb101dd254d33a92a0444b979d599e04fdeaccdbc5';
        $apiUrl = "https://apigw.pu.go.id/v1/siman/instalasi?api_key={$apiKey}";
        
        echo "<h3>1️⃣ Info API</h3>";
        echo "<p><strong>URL:</strong> {$apiUrl}</p>";
        echo "<hr>";
        
        echo "<h3>2️⃣ Mengambil Data dari API...</h3>";
        $apiData = $this->getApiData();
        
        echo "<p><strong>Total data dari API:</strong> " . count($apiData) . "</p>";
        
        if (empty($apiData)) {
            echo "<div style='background: #fee; padding: 15px; border-left: 4px solid red;'>";
            echo "<h4 style='color: red;'>❌ TIDAK ADA DATA!</h4>";
            echo "</div>";
            return;
        }
        
        echo "<hr>";
        echo "<h3>3️⃣ Sample Data (3 items pertama)</h3>";
        echo "<pre style='background: #f5f5f5; padding: 15px; overflow: auto;'>";
        echo json_encode(array_slice($apiData, 0, 3), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        echo "</pre>";
        echo "<hr>";
        
        echo "<h3>4️⃣ Analisis Kelompok di API</h3>";
        $kelompokStats = [];
        foreach ($apiData as $item) {
            $kelompok = strtoupper(trim($item['kelompok'] ?? 'UNKNOWN'));
            $kelompokStats[$kelompok] = ($kelompokStats[$kelompok] ?? 0) + 1;
        }
        
        arsort($kelompokStats);
        
        echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background: #333; color: white;'>";
        echo "<th>No</th><th>Kelompok</th><th>Jumlah Data</th></tr>";
        
        $no = 1;
        foreach ($kelompokStats as $kelompok => $jumlah) {
            echo "<tr><td>{$no}</td><td><strong>{$kelompok}</strong></td><td>{$jumlah}</td></tr>";
            $no++;
        }
        echo "</table>";
    }

    // Method helper untuk mapping kelompok API
    private function mapKelompokFromApi($kelompok_api)
    {
        $kelompok_api = strtoupper(trim($kelompok_api));
        
        // Normalisasi: hapus spasi di sekitar slash
        $kelompok_api = preg_replace('/\s*\/\s*/', '/', $kelompok_api);
        
        // Mapping jika ada perbedaan nama dari API
        $mapping = [
            'INSTALASI AIR BERSIH/AIR BAKU' => 'INSTALASI AIR BERSIH/AIR BAKU',
            'INSTALASI AIR KOTOR' => 'INSTALASI AIR KOTOR',
            'INSTALASI PENGOLAHAN SAMPAH' => 'INSTALASI PENGOLAHAN SAMPAH',
            'INSTALASI PENGOLAHAN BAHAN BANGUNAN' => 'INSTALASI PENGOLAHAN BAHAN BANGUNAN',
            'INSTALASI PEMBANGKIT LISTRIK' => 'INSTALASI PEMBANGKIT LISTRIK',
            'INSTALASI GARDU LISTRIK' => 'INSTALASI GARDU LISTRIK',
            'INSTALASI PERTAHANAN' => 'INSTALASI PERTAHANAN',
            'INSTALASI GAS' => 'INSTALASI GAS',
            'INSTALASI PENGAMAN' => 'INSTALASI PENGAMAN',
            'INSTALASI LAIN' => 'INSTALASI LAIN'
        ];
        
        return $mapping[$kelompok_api] ?? $kelompok_api;
    }

    // Method khusus untuk handle instalasi air bersih (karena ada slash)
    public function airBersih()
    {
        return $this->kelompokDetail('INSTALASI AIR BERSIH/AIR BAKU');
    }
}