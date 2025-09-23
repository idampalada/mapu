<?php

namespace App\Controllers\User\Barang\JalanIrigasiJaringan;

use App\Controllers\BaseController;
use App\Models\BangunanAirModel;

class BangunanAir extends BaseController
{
    protected $bangunanAirModel;
    
    public function __construct()
    {
        $this->bangunanAirModel = new BangunanAirModel();
    }

    // Method untuk mengambil data dari API
    private function getApiData($url = null)
    {
        $client = \Config\Services::curlrequest();
        $apiKey = 'c877acaa0de297a9e3b8bbdb101dd254d33a92a0444b979d599e04fdeaccdbc5';
        
        if (!$url) {
            $url = "https://apigw.pu.go.id/v1/siman/bangunan-air?api_key={$apiKey}";
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
        $bangunanAirList = $this->getApiData();
        return view('user/bangunanair/dashboardbangunanair', [
            'bangunanAirList' => $bangunanAirList
        ]);
    }

    public function kelompokBangunanAir()
{
    $sort = $this->request->getGet('sort') ?? 'kode_barang';
    $order = $this->request->getGet('order') ?? 'asc';
    
    $allBangunanAirList = $this->bangunanAirModel->findAll();
    
    // PERBAIKI FILTER - GUNAKAN strtoupper untuk konsistensi
    $irigasiData = array_filter($allBangunanAirList, function ($item) {
        return strtoupper($item['kelompok'] ?? '') === 'BANGUNAN AIR IRIGASI';
    });

    $pasangSurutData = array_filter($allBangunanAirList, function ($item) {
        return strtoupper($item['kelompok'] ?? '') === 'BANGUNAN PENGAIRAN PASANG SURUT';
    });

    $rawaPolderData = array_filter($allBangunanAirList, function ($item) {
        return strtoupper($item['kelompok'] ?? '') === 'BANGUNAN PENGEMBANGAN RAWA DAN POLDER';
    });

    $pengamanSungaiData = array_filter($allBangunanAirList, function ($item) {
        return strtoupper($item['kelompok'] ?? '') === 'BANGUNAN PENGAMAN SUNGAI/PANTAI & PENANGGULAN BENCANA ALAM';
    });

    $sumberAirData = array_filter($allBangunanAirList, function ($item) {
        return strtoupper($item['kelompok'] ?? '') === 'BANGUNAN PENGEMBANGAN SUMBER AIR DAN AIR TANAH';
    });

    $airBersihData = array_filter($allBangunanAirList, function ($item) {
        return strtoupper($item['kelompok'] ?? '') === 'BANGUNAN AIR BERSIH/AIR BAKU';  // PERBAIKAN INI
    });

    $airKotorData = array_filter($allBangunanAirList, function ($item) {
        return strtoupper($item['kelompok'] ?? '') === 'BANGUNAN AIR KOTOR';
    });

    // Reset array keys dan gabungkan
    $allData = array_merge(
        array_values($irigasiData),
        array_values($pasangSurutData), 
        array_values($rawaPolderData),
        array_values($pengamanSungaiData),
        array_values($sumberAirData),
        array_values($airBersihData), 
        array_values($airKotorData)
    );

    return view('user/barang/jalanirigasijaringan/bangunanair/kelompokbangunanair', [
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
    if (!$this->bangunanAirModel->isValidKelompokBangunanAir($kelompok)) {
        session()->setFlashdata('error', 'Kelompok tidak valid: ' . $kelompok);
        return redirect()->to('user/barang/jalanirigasijaringan/bangunanair/kelompokbangunanair');
    }
    
    // Debug: cek data di database
    $debugCount = $this->bangunanAirModel->where('UPPER(kelompok)', strtoupper($kelompok))->countAllResults();
    log_message('info', "Data found for kelompok '{$kelompok}': {$debugCount}");
    
    if ($debugCount == 0) {
        session()->setFlashdata('error', "Tidak ada data untuk kelompok: {$kelompok}");
        return redirect()->to('user/barang/jalanirigasijaringan/bangunanair/kelompokbangunanair');
    }
    
    $searchTerm = $this->request->getGet('search') ?? '';
    $sort = $this->request->getGet('sort') ?? 'kode_barang';
    $order = $this->request->getGet('order') ?? 'asc';
    $perPage = 100;
    $page = $this->request->getGet('page') ?? 1;

    // Gunakan database sebagai sumber data
    $builder = $this->bangunanAirModel->builder();
    
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
    $bangunanAirList = $builder->limit($perPage, $offset)->get()->getResultArray();

    // Setup pagination
    $pager = service('pager');
    $pager->setPath('user/barang/jalanirigasijaringan/bangunanair/kelompokbangunanair/' . urlencode($kelompok));
    $totalPages = ceil($totalItems / $perPage);

    return view('user/barang/jalanirigasijaringan/bangunanair/kelompokbangunanair', [
        'bangunanAirList' => $bangunanAirList,
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

    // Method untuk menambah bangunan air manual
    public function tambah()
    {
        log_message('info', '=== TAMBAH BANGUNAN AIR METHOD DIPANGGIL ===');
        
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
            $luas_dasar = $data_source['luas_dasar'] ?? '';
            $luas_bangunan = $data_source['luas_bangunan'] ?? '';
            
            // Mapping kelompok menggunakan method dari model
            $kategori_detail = $this->bangunanAirModel->mapKelompokToKategori($kelompok);
            
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
                'luas_dasar' => $this->safeFloat($luas_dasar),
                'luas_bangunan' => $this->safeFloat($luas_bangunan),
                'kategori_utama' => 'BANGUNAN AIR',
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
                $this->bangunanAirModel->skipValidation(true);
                $insertResult = $this->bangunanAirModel->insert($data);
                
                if ($insertResult) {
                    $insertId = $this->bangunanAirModel->getInsertID();
                    session()->setFlashdata('success', "Data bangunan air berhasil disimpan! ID: {$insertId}");
                } else {
                    $errors = $this->bangunanAirModel->errors();
                    session()->setFlashdata('error', 'Gagal menyimpan data: ' . implode(', ', $errors));
                }
                
                $this->bangunanAirModel->skipValidation(false);
                
            } catch (\Exception $e) {
                session()->setFlashdata('error', 'Error database: ' . $e->getMessage());
                $this->bangunanAirModel->skipValidation(false);
            }
        }
        
        return redirect()->to('user/barang/jalanirigasijaringan/bangunanair/kelompokbangunanair');
    }

    // Method untuk reset semua data
    public function resetData()
    {
        try {
            $this->bangunanAirModel->builder()->truncate();
            
            session()->setFlashdata('success', 'Semua data berhasil dihapus!');
            return redirect()->to('user/barang/jalanirigasijaringan/bangunanair/kelompokbangunanair');
            
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

            $this->bangunanAirModel->skipValidation(true);

            // Kelompok yang valid untuk bangunan air
            $validKelompok = [
                'BANGUNAN AIR IRIGASI',
                'BANGUNAN PENGAIRAN PASANG SURUT',
                'BANGUNAN PENGEMBANGAN RAWA DAN POLDER',
                'BANGUNAN PENGAMAN SUNGAI/PANTAI & PENANGGULAN BENCANA ALAM',
                'BANGUNAN PENGEMBANGAN SUMBER AIR DAN AIR TANAH',
                'BANGUNAN AIR BERSIH/AIR BAKU',
                'BANGUNAN AIR KOTOR'
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

                    // FILTER: Hanya import data dengan kelompok bangunan air yang valid
                    if (!in_array($kelompok_api, $validKelompok)) {
                        $filtered++;
                        log_message('info', "Filtered out: {$kode_barang} - Kelompok: '{$kelompok_api}' (bukan bangunan air)");
                        continue;
                    }

                    log_message('info', "Importing: {$kode_barang} - Kelompok: '{$kelompok_api}'");

                    $unique_kode = $kode_barang . '_' . $index;

                    // Mapping kelompok menggunakan method dari model
                    $kategori_detail = $this->bangunanAirModel->mapKelompokToKategori($kelompok_api);

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
                        'luas_dasar' => $this->safeFloat($item['luas_dasar'] ?? 0),
                        'luas_bangunan' => $this->safeFloat($item['luas_bangunan'] ?? 0),
                        'kategori_utama' => 'BANGUNAN AIR',
                        'kategori_detail' => $kategori_detail,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];

                    if ($this->bangunanAirModel->insert($data)) {
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

            $this->bangunanAirModel->skipValidation(false);

            $total = count($apiData);
            $message = "Import selesai! Total API: {$total}, Berhasil: {$imported}, Dilewati: {$skipped}, Difilter (bukan bangunan air): {$filtered}";
            
            if (!empty($errors)) {
                $message .= ", Error: " . count($errors);
                log_message('error', 'Import errors: ' . implode(', ', $errors));
            }

            session()->setFlashdata('success', $message);
            return redirect()->to('user/barang/jalanirigasijaringan/bangunanair/kelompokbangunanair');

        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Gagal import data: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    // Method export ke CSV
    public function exportBangunanAirList($jenis = 'semua')
    {
        $jenisValid = ['irigasi', 'pasangsurut', 'rawa', 'pengaman', 'sumberair', 'airbersih', 'airkotor', 'semua'];
        if (!in_array($jenis, $jenisValid)) {
            $jenis = 'semua';
        }

        $allBangunanAirList = $this->bangunanAirModel->findAll();
        
        // Filter berdasarkan jenis
        if ($jenis !== 'semua') {
            $bangunanAirList = array_filter($allBangunanAirList, function($item) use ($jenis) {
                $kelompok = strtolower($item['kelompok'] ?? '');
                
                switch ($jenis) {
                    case 'irigasi':
                        return strpos($kelompok, 'bangunan air irigasi') !== false;
                    case 'pasangsurut':
                        return strpos($kelompok, 'bangunan pengairan pasang surut') !== false;
                    case 'rawa':
                        return strpos($kelompok, 'bangunan pengembangan rawa dan polder') !== false;
                    case 'pengaman':
                        return strpos($kelompok, 'bangunan pengaman sungai/pantai') !== false;
                    case 'sumberair':
                        return strpos($kelompok, 'bangunan pengembangan sumber air dan air tanah') !== false;
                    case 'airbersih':
                        return strpos($kelompok, 'bangunan air bersih/air baku') !== false;
                    case 'airkotor':
                        return strpos($kelompok, 'bangunan air kotor') !== false;
                    default:
                        return true;
                }
            });
            $bangunanAirList = array_values($bangunanAirList);
        } else {
            $bangunanAirList = $allBangunanAirList;
        }

        $filename = 'bangunan_air_' . $jenis . '_' . date('Y-m-d') . '.csv';
        
        $response = service('response');
        $response->setHeader('Content-Type', 'text/csv');
        $response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        fputcsv($output, [
            'No', 'Kode Barang', 'Nama Barang', 'NUP', 'Merk', 'Kelompok', 'Sub Kelompok', 'Kondisi', 
            'Kuantitas', 'Status', 'Nilai Perolehan', 'Nilai Buku', 'Tanggal Perolehan', 'Nama Satker',
            'Luas Dasar (m2)', 'Luas Bangunan (m2)', 'Kategori Detail'
        ]);

        $no = 1;
        foreach ($bangunanAirList as $item) {
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
                number_format(floatval($item['luas_dasar'] ?? 0), 2, ',', '.'),
                number_format(floatval($item['luas_bangunan'] ?? 0), 2, ',', '.'),
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
        $totalData = $this->bangunanAirModel->countAllResults();
        $apiData = $this->getApiData();
        $totalApi = count($apiData);
        
        // Statistik per kelompok
        $dbStats = [
            'total' => $totalData,
            'irigasi' => $this->bangunanAirModel->where('kelompok', 'BANGUNAN AIR IRIGASI')->countAllResults(),
            'pasang_surut' => $this->bangunanAirModel->where('kelompok', 'BANGUNAN PENGAIRAN PASANG SURUT')->countAllResults(),
            'rawa_polder' => $this->bangunanAirModel->where('kelompok', 'BANGUNAN PENGEMBANGAN RAWA DAN POLDER')->countAllResults(),
            'pengaman_sungai' => $this->bangunanAirModel->where('kelompok', 'BANGUNAN PENGAMAN SUNGAI/PANTAI & PENANGGULAN BENCANA ALAM')->countAllResults(),
            'sumber_air' => $this->bangunanAirModel->where('kelompok', 'BANGUNAN PENGEMBANGAN SUMBER AIR DAN AIR TANAH')->countAllResults(),
            'air_bersih' => $this->bangunanAirModel->where('kelompok', 'BANGUNAN AIR BERSIH/AIR BAKU')->countAllResults(),
            'air_kotor' => $this->bangunanAirModel->where('kelompok', 'BANGUNAN AIR KOTOR')->countAllResults()
        ];
        
        return view('user/bangunanair/stats', [
            'totalData' => $totalData,
            'totalApi' => $totalApi,
            'dbStats' => $dbStats
        ]);
    }

    // Method untuk test API (debugging)
    public function testApi()
    {
        $apiData = $this->getApiData();
        
        echo "<h3>Test API Bangunan Air</h3>";
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
            
            // Filter untuk bangunan air
            $validKelompok = [
                'BANGUNAN AIR IRIGASI',
                'BANGUNAN PENGAIRAN PASANG SURUT',
                'BANGUNAN PENGEMBANGAN RAWA DAN POLDER',
                'BANGUNAN PENGAMAN SUNGAI/PANTAI & PENANGGULAN BENCANA ALAM',
                'BANGUNAN PENGEMBANGAN SUMBER AIR DAN AIR TANAH',
                'BANGUNAN AIR BERSIH/AIR BAKU',
                'BANGUNAN AIR KOTOR'
            ];
            $filteredData = array_filter($apiData, function($item) use ($validKelompok) {
                return in_array(strtoupper($item['kelompok'] ?? ''), $validKelompok);
            });
            
            echo "<h4>Data yang akan diimport (kelompok bangunan air):</h4>";
            echo "<p>Total: " . count($filteredData) . " dari " . count($apiData) . " data</p>";
            
            if (!empty($filteredData)) {
                echo "<h5>Sample data bangunan air:</h5>";
                echo "<pre>" . json_encode(array_slice($filteredData, 0, 3), JSON_PRETTY_PRINT) . "</pre>";
            }
        } else {
            echo "<p style='color: red;'>Tidak ada data dari API atau terjadi error!</p>";
        }
    }
    // Method khusus untuk handle air bersih (karena ada slash)
public function kelompokDetailSegments($segment1, $segment2 = null)
{
    if ($segment2) {
        // Gabungkan segment dengan slash
        $kelompok = $segment1 . '/' . $segment2;
    } else {
        $kelompok = $segment1;
    }
    
    return $this->kelompokDetail($kelompok);
}

// Method khusus untuk air bersih
public function airBersih()
{
    return $this->kelompokDetail('BANGUNAN AIR BERSIH/AIR BAKU');
}
// Method khusus untuk pengaman sungai (karena masalah & di URL)
public function pengamanSungai()
{
    return $this->kelompokDetail('BANGUNAN PENGAMAN SUNGAI/PANTAI & PENANGGULAN BENCANA ALAM');
}
// Method helper untuk mapping kelompok API yang terpotong
private function mapKelompokFromApi($kelompok_api)
{
    $kelompok_api = strtoupper(trim($kelompok_api));
    
    // Cek dengan partial matching untuk kelompok pengaman yang terpotong
    if (stripos($kelompok_api, 'BANGUNAN PENGAMAN SUNGAI/PANTAI') !== false && 
        stripos($kelompok_api, 'PENANGGULAN') !== false) {
        return 'BANGUNAN PENGAMAN SUNGAI/PANTAI & PENANGGULAN BENCANA ALAM';
    }
    
    return $kelompok_api;
}

}