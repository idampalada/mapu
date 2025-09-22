<?php

namespace App\Controllers\User\Barang\AsetTetapLainnya;

use App\Controllers\BaseController;
use App\Models\BahanPerpustakaanModel;

class BahanPerpustakaan extends BaseController
{
    protected $bahanPerpustakaanModel;
    
    public function __construct()
    {
        $this->bahanPerpustakaanModel = new BahanPerpustakaanModel();
    }

    // Method untuk mengambil data dari API
private function getApiData($url = null)
{
    $client = \Config\Services::curlrequest();
    $apiKey = 'c877acaa0de297a9e3b8bbdb101dd254d33a92a0444b979d599e04fdeaccdbc5';
    
    if (!$url) {
        // PERBAIKAN: Gunakan HTTPS bukan HTTP
        $url = "https://apigw.pu.go.id/v1/siman/aset-tetap-lainnya?api_key={$apiKey}";
    }
    
    // Log API URL untuk debugging
    log_message('info', 'Calling API: ' . $url);
    
    try {
        $response = $client->get($url, [
            'timeout' => 30,
            'connect_timeout' => 10,
            'verify' => false, // Disable SSL verification untuk menghindari masalah sertifikat
            'allow_redirects' => true, // Ikuti redirect otomatis
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'Accept' => 'application/json'
            ]
        ]);
        
        log_message('info', 'API Response Status: ' . $response->getStatusCode());
        
        if ($response->getStatusCode() === 200) {
            $body = $response->getBody();
            $result = json_decode($body, true);
            
            if (json_last_error() === JSON_ERROR_NONE) {
                log_message('info', 'API JSON Parse Success. Records: ' . count($result));
                
                // Cek apakah response memiliki property 'resource' atau 'data'
                $data = $result;
                if (isset($result['resource'])) {
                    $data = $result['resource'];
                    log_message('info', 'Using resource property from API response');
                } elseif (isset($result['data'])) {
                    $data = $result['data'];
                    log_message('info', 'Using data property from API response');
                }
                
                if (!is_array($data)) {
                    log_message('error', 'API response data is not array');
                    return [];
                }
                
                // Filter hanya data bahan perpustakaan
                $validKelompok = [
                    'BAHAN PERPUSTAKAAN TERCETAK',
                    'BAHAN PERPUSTAKAAN TEREKAM DAN BENTUK MIKRO',
                    'KARTOGRAFI, NASKAH DAN LUKISAN'
                ];
                
                $filtered = array_filter($data, function($item) use ($validKelompok) {
                    return in_array(strtoupper($item['kelompok'] ?? ''), $validKelompok);
                });
                
                log_message('info', 'Filtered Bahan Perpustakaan Records: ' . count($filtered));
                
                return array_values($filtered);
            } else {
                log_message('error', 'JSON decode error: ' . json_last_error_msg());
            }
        } else {
            log_message('error', 'API HTTP Error: ' . $response->getStatusCode());
            
            // Log response body untuk debugging
            $body = $response->getBody();
            log_message('error', 'Response body: ' . substr($body, 0, 500));
        }
    } catch (\Exception $e) {
        log_message('error', 'API Exception: ' . $e->getMessage());
    }
    
    return [];
}

    public function dashboard()
    {
        $bahanPerpustakaanList = $this->getApiData();
        return view('user/barang/asettetaplainnya/bahanperpustakaan/dashboard', [
            'bahanPerpustakaanList' => $bahanPerpustakaanList
        ]);
    }

    public function kelompokBahanPerpustakaan()
    {
        $sort = $this->request->getGet('sort') ?? 'kode_barang';
        $order = $this->request->getGet('order') ?? 'asc';
        
        // Menggunakan data dari database untuk konsistensi
        $allBahanPerpustakaanList = $this->bahanPerpustakaanModel->findAll();
        
        // Filter data berdasarkan kelompok
        $tercetakData = array_filter($allBahanPerpustakaanList, function ($item) {
            return strtolower($item['kelompok'] ?? '') === 'bahan perpustakaan tercetak';
        });

        $terekamData = array_filter($allBahanPerpustakaanList, function ($item) {
            return strtolower($item['kelompok'] ?? '') === 'bahan perpustakaan terekam dan bentuk mikro';
        });

        $kartografiData = array_filter($allBahanPerpustakaanList, function ($item) {
            return strtolower($item['kelompok'] ?? '') === 'kartografi, naskah dan lukisan';
        });

        // Reset array keys
        $tercetakData = array_values($tercetakData);
        $terekamData = array_values($terekamData);
        $kartografiData = array_values($kartografiData);
        
        // Gabungkan semua data
        $allData = array_merge($tercetakData, $terekamData, $kartografiData);

        return view('user/barang/asettetaplainnya/bahanperpustakaan/kelompokbahanperpustakaan', [
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

    // Log untuk debugging
    log_message('info', "kelompokDetail called with kelompok: {$kelompok}");

    // Cek apakah ada data di database
    $dbCount = $this->bahanPerpustakaanModel->countAllResults();
    log_message('info', "Database records: {$dbCount}");
    
    if ($dbCount == 0) {
        // Jika database kosong, coba ambil dari API
        log_message('info', 'Database empty, trying to fetch from API...');
        $apiData = $this->getApiData();
        
        if (empty($apiData)) {
            log_message('error', 'No data from API');
            session()->setFlashdata('error', 'Tidak ada data dari API atau API tidak dapat diakses!');
        } else {
            log_message('info', 'API data available: ' . count($apiData) . ' records');
            session()->setFlashdata('info', 'Data tersedia dari API. Gunakan tombol Import/Sync untuk menyimpan ke database.');
        }
    }

    // Gunakan database sebagai sumber data
    $builder = $this->bahanPerpustakaanModel->builder();
    
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
    $bahanPerpustakaanList = $builder->limit($perPage, $offset)->get()->getResultArray();

    // Setup pagination
    $pager = service('pager');
    $pager->setPath('user/barang/asettetaplainnya/bahanperpustakaan/kelompokbahanperpustakaan/' . urlencode($kelompok));
    $totalPages = ceil($totalItems / $perPage);

    return view('user/barang/asettetaplainnya/bahanperpustakaan/kelompokbahanperpustakaan', [
        'bahanPerpustakaanList' => $bahanPerpustakaanList,
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

    // Method untuk menambah bahan perpustakaan manual
    public function tambah()
    {
        // Log bahwa method dipanggil
        log_message('info', '=== TAMBAH BAHAN PERPUSTAKAAN METHOD DIPANGGIL ===');
        
        // Debug request method dengan berbagai cara
        $method1 = $this->request->getMethod();
        $method2 = $_SERVER['REQUEST_METHOD'] ?? 'unknown';
        $method3 = $this->request->getServer('REQUEST_METHOD');
        
        log_message('info', "Request Method (getMethod): '{$method1}'");
        log_message('info', "Request Method (\$_SERVER): '{$method2}'");
        log_message('info', "Request Method (getServer): '{$method3}'");
        
        // Cek apakah ada data POST
        $postData = $this->request->getPost();
        $postRaw = $_POST;
        
        log_message('info', 'POST data (request): ' . json_encode($postData));
        log_message('info', 'POST data (raw): ' . json_encode($postRaw));
        log_message('info', 'POST count: ' . count($postData));
        
        // Gunakan pengecekan yang lebih reliable
        $isPost = (strtoupper($method2) === 'POST') || !empty($postData) || !empty($postRaw);
        
        log_message('info', "Is POST determined: " . ($isPost ? 'YES' : 'NO'));
        
        if ($isPost && (!empty($postData) || !empty($postRaw))) {
            log_message('info', 'MASUK KE PROSES POST');
            
            // Gunakan $_POST langsung jika $this->request->getPost() kosong
            $data_source = !empty($postData) ? $postData : $postRaw;
            
            // Ambil data satu per satu
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
            
            log_message('info', "Kode Barang: '{$kode_barang}'");
            log_message('info', "Nama Barang: '{$nama_barang}'");
            log_message('info', "Kelompok: '{$kelompok}'");
            log_message('info', "Sub Kelompok: '{$sub_kelompok}'");
            log_message('info', "NUP: '{$nup}'");
            log_message('info', "Merk: '{$merk}'");
            log_message('info', "Kondisi: '{$kondisi}'");
            
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
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            log_message('info', 'Data yang akan disimpan: ' . json_encode($data));

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
                log_message('error', 'Validation errors: ' . json_encode($errors));
                session()->setFlashdata('error', 'Error: ' . implode(', ', $errors));
                return redirect()->back()->withInput();
            }

            try {
                log_message('info', 'MENCOBA INSERT DENGAN QUERY BUILDER...');
                
                // Skip validation untuk operasi insert manual
                $this->bahanPerpustakaanModel->skipValidation(true);
                
                // Insert menggunakan Query Builder
                $insertResult = $this->bahanPerpustakaanModel->insert($data);
                
                if ($insertResult) {
                    $insertId = $this->bahanPerpustakaanModel->getInsertID();
                    log_message('info', "QUERY BUILDER BERHASIL! Insert ID: {$insertId}");
                    
                    // Double check dengan Query Builder
                    $insertedData = $this->bahanPerpustakaanModel->find($insertId);
                    
                    if ($insertedData) {
                        log_message('info', 'DATA BERHASIL DIKONFIRMASI: ' . json_encode($insertedData));
                        session()->setFlashdata('success', "Data bahan perpustakaan berhasil disimpan! ID: {$insertId}");
                    } else {
                        log_message('error', 'INSERT ID ADA TAPI DATA TIDAK DITEMUKAN');
                        session()->setFlashdata('error', 'Data mungkin tersimpan tapi tidak dapat dikonfirmasi');
                    }
                } else {
                    $errors = $this->bahanPerpustakaanModel->errors();
                    log_message('error', 'QUERY BUILDER GAGAL: ' . json_encode($errors));
                    session()->setFlashdata('error', 'Gagal menyimpan data: ' . implode(', ', $errors));
                }
                
                // Restore validation
                $this->bahanPerpustakaanModel->skipValidation(false);
                
            } catch (\Exception $e) {
                log_message('error', 'EXCEPTION QUERY BUILDER: ' . $e->getMessage());
                session()->setFlashdata('error', 'Error database: ' . $e->getMessage());
                
                // Restore validation jika terjadi error
                $this->bahanPerpustakaanModel->skipValidation(false);
            }

            log_message('info', '=== TAMBAH BAHAN PERPUSTAKAAN METHOD SELESAI ===');
        } else {
            log_message('info', 'TIDAK ADA DATA POST - SKIP PROSES');
        }
        
        return redirect()->to('user/barang/asettetaplainnya/bahanperpustakaan/kelompokbahanperpustakaan');
    }

    // Method untuk reset semua data
    public function resetData()
    {
        try {
            // Hapus semua data dari tabel
            $this->bahanPerpustakaanModel->builder()->truncate();
            
            session()->setFlashdata('success', 'Semua data berhasil dihapus!');
            return redirect()->to('user/barang/asettetaplainnya/bahanperpustakaan/kelompokbahanperpustakaan');
            
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Gagal menghapus data: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    // Method import dari API - Import data berdasarkan filter kelompok
    public function importFromApi()
    {
        $imported = 0;
        $skipped = 0;
        $filtered = 0; // Data yang difilter karena bukan kelompok bahan perpustakaan
        $errors = [];

        try {
            // Ambil data dari API
            $apiData = $this->getApiData();

            if (empty($apiData)) {
                session()->setFlashdata('error', 'Tidak ada data dari API atau API tidak dapat diakses!');
                return redirect()->back();
            }

            // Nonaktifkan validation sementara
            $this->bahanPerpustakaanModel->skipValidation(true);

            // Kelompok yang valid untuk bahan perpustakaan
            $validKelompok = [
                'BAHAN PERPUSTAKAAN TERCETAK',
                'BAHAN PERPUSTAKAAN TEREKAM DAN BENTUK MIKRO',
                'KARTOGRAFI, NASKAH DAN LUKISAN'
            ];

            foreach ($apiData as $index => $item) {
                try {
                    // Bersihkan data dan handle null/empty
                    $kode_barang = trim($item['kode_barang'] ?? '');
                    $kelompok_api = strtoupper(trim($item['kelompok'] ?? ''));
                    
                    // Skip jika kode barang kosong
                    if (empty($kode_barang)) {
                        $skipped++;
                        continue;
                    }

                    // FILTER: Hanya import data dengan kelompok bahan perpustakaan yang valid
                    if (!in_array($kelompok_api, $validKelompok)) {
                        $filtered++;
                        log_message('info', "Filtered out: {$kode_barang} - Kelompok: '{$kelompok_api}' (bukan bahan perpustakaan)");
                        continue;
                    }

                    // Log data yang akan diimport
                    log_message('info', "Importing: {$kode_barang} - Kelompok: '{$kelompok_api}'");

                    // Beri suffix untuk menghindari error unique constraint jika ada
                    $unique_kode = $kode_barang . '_' . $index;

                    // Siapkan data dengan default values untuk field kosong
                    $data = [
                        'kode_barang' => $unique_kode, // Buat unik dengan menambah index
                        'nama_barang' => trim($item['nama_barang'] ?? '') ?: 'Unknown',
                        'nup' => trim($item['nup'] ?? ''),
                        'merk' => trim($item['merk'] ?? ''),
                        'kelompok' => $kelompok_api, // Gunakan kelompok dari API
                        'sub_kelompok' => trim($item['sub_kelompok'] ?? ''),
                        'kondisi' => trim($item['kondisi'] ?? ''),
                        'kuantitas' => intval($item['kuantitas'] ?? 1),
                        'status_penggunaan' => trim($item['status_penggunaan'] ?? ''),
                        'nilai_perolehan' => $this->safeFloat($item['nilai_perolehan'] ?? 0),
                        'nilai_buku' => $this->safeFloat($item['nilai_buku'] ?? 0),
                        'tanggal_perolehan' => !empty($item['tanggal_perolehan']) ? $item['tanggal_perolehan'] : null,
                        'nama_satker' => trim($item['nama_satker'] ?? ''),
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];

                    // Insert ke database
                    if ($this->bahanPerpustakaanModel->insert($data)) {
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

            // Aktifkan kembali validation
            $this->bahanPerpustakaanModel->skipValidation(false);

            // Set pesan hasil dengan info filter
            $total = count($apiData);
            $message = "Import selesai! Total API: {$total}, Berhasil: {$imported}, Dilewati: {$skipped}, Difilter (bukan bahan perpustakaan): {$filtered}";
            
            if (!empty($errors)) {
                $message .= ", Error: " . count($errors);
                // Log errors untuk debugging
                log_message('error', 'Import errors: ' . implode(', ', $errors));
            }

            session()->setFlashdata('success', $message);
            return redirect()->to('user/barang/asettetaplainnya/bahanperpustakaan/kelompokbahanperpustakaan');

        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Gagal import data: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    // Method export ke CSV
    public function exportBahanPerpustakaanList($jenis = 'semua')
    {
        $jenisValid = ['tercetak', 'terekam', 'kartografi', 'semua'];
        if (!in_array($jenis, $jenisValid)) {
            $jenis = 'semua';
        }

        // Ambil data dari database
        $allBahanPerpustakaanList = $this->bahanPerpustakaanModel->findAll();
        
        // Filter berdasarkan jenis
        if ($jenis !== 'semua') {
            $bahanPerpustakaanList = array_filter($allBahanPerpustakaanList, function($item) use ($jenis) {
                $kelompok = strtolower($item['kelompok'] ?? '');
                
                switch ($jenis) {
                    case 'tercetak':
                        return strpos($kelompok, 'bahan perpustakaan tercetak') !== false;
                    case 'terekam':
                        return strpos($kelompok, 'bahan perpustakaan terekam dan bentuk mikro') !== false;
                    case 'kartografi':
                        return strpos($kelompok, 'kartografi, naskah dan lukisan') !== false;
                    default:
                        return true;
                }
            });
            $bahanPerpustakaanList = array_values($bahanPerpustakaanList);
        } else {
            $bahanPerpustakaanList = $allBahanPerpustakaanList;
        }

        // Generate CSV
        $filename = 'bahan_perpustakaan_' . $jenis . '_' . date('Y-m-d') . '.csv';
        
        $response = service('response');
        $response->setHeader('Content-Type', 'text/csv');
        $response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        fputcsv($output, [
            'No', 'Kode Barang', 'Nama Barang', 'NUP', 'Merk/Penerbit', 'Kelompok', 'Sub Kelompok', 'Kondisi', 
            'Kuantitas', 'Status', 'Nilai Perolehan', 'Nilai Buku', 'Tanggal Perolehan', 'Nama Satker'
        ]);

        $no = 1;
        foreach ($bahanPerpustakaanList as $item) {
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
        $totalData = $this->bahanPerpustakaanModel->countAllResults();
        $apiData = $this->getApiData();
        $totalApi = count($apiData);
        
        // Statistik per kelompok
        $dbStats = [
            'total' => $totalData,
            'tercetak' => $this->bahanPerpustakaanModel->where('kelompok', 'BAHAN PERPUSTAKAAN TERCETAK')->countAllResults(),
            'terekam' => $this->bahanPerpustakaanModel->where('kelompok', 'BAHAN PERPUSTAKAAN TEREKAM DAN BENTUK MIKRO')->countAllResults(),
            'kartografi' => $this->bahanPerpustakaanModel->where('kelompok', 'KARTOGRAFI, NASKAH DAN LUKISAN')->countAllResults(),
        ];
        
        return view('user/barang/asettetaplainnya/bahanperpustakaan/stats', [
            'totalData' => $totalData,
            'totalApi' => $totalApi,
            'dbStats' => $dbStats
        ]);
    }
    
public function testApi()
{
    echo "<h1>Debug API Bahan Perpustakaan</h1>";
    
    // Test kedua URL
    $urls = [
        'HTTP' => 'http://apigw.pu.go.id/v1/siman/aset-tetap-lainnya?api_key=c877acaa0de297a9e3b8bbdb101dd254d33a92a0444b979d599e04fdeaccdbc5',
        'HTTPS' => 'https://apigw.pu.go.id/v1/siman/aset-tetap-lainnya?api_key=c877acaa0de297a9e3b8bbdb101dd254d33a92a0444b979d599e04fdeaccdbc5'
    ];
    
    foreach ($urls as $protocol => $apiUrl) {
        echo "<h2>Test {$protocol}</h2>";
        echo "<p><strong>API URL:</strong> {$apiUrl}</p>";
        
        $client = \Config\Services::curlrequest();
        
        try {
            $response = $client->get($apiUrl, [
                'timeout' => 30,
                'connect_timeout' => 10,
                'verify' => false,
                'allow_redirects' => true,
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'Accept' => 'application/json'
                ]
            ]);
            
            echo "<p><strong>Status Code:</strong> " . $response->getStatusCode() . "</p>";
            
            if ($response->getStatusCode() === 200) {
                $body = $response->getBody();
                $data = json_decode($body, true);
                
                echo "<p><strong>Response Body Length:</strong> " . strlen($body) . " characters</p>";
                echo "<p><strong>JSON Decode Status:</strong> " . (json_last_error() === JSON_ERROR_NONE ? 'SUCCESS' : 'ERROR: ' . json_last_error_msg()) . "</p>";
                
                if (is_array($data)) {
                    // Cek struktur response
                    echo "<p><strong>Response Structure:</strong></p>";
                    echo "<ul>";
                    foreach (array_keys($data) as $key) {
                        echo "<li>{$key}</li>";
                    }
                    echo "</ul>";
                    
                    // Tentukan data array yang benar
                    $records = $data;
                    if (isset($data['resource']) && is_array($data['resource'])) {
                        $records = $data['resource'];
                        echo "<p><strong>Using 'resource' property</strong></p>";
                    } elseif (isset($data['data']) && is_array($data['data'])) {
                        $records = $data['data'];
                        echo "<p><strong>Using 'data' property</strong></p>";
                    }
                    
                    echo "<p><strong>Total Records:</strong> " . count($records) . "</p>";
                    
                    // Filter untuk bahan perpustakaan
                    $validKelompok = [
                        'BAHAN PERPUSTAKAAN TERCETAK',
                        'BAHAN PERPUSTAKAAN TEREKAM DAN BENTUK MIKRO',
                        'KARTOGRAFI, NASKAH DAN LUKISAN'
                    ];
                    
                    $filtered = array_filter($records, function($item) use ($validKelompok) {
                        return in_array(strtoupper($item['kelompok'] ?? ''), $validKelompok);
                    });
                    
                    echo "<p><strong>Bahan Perpustakaan Records:</strong> " . count($filtered) . "</p>";
                    
                    // Tampilkan contoh data
                    if (!empty($filtered)) {
                        echo "<h3>Sample Data (First 2 records):</h3>";
                        $sample = array_slice($filtered, 0, 2);
                        echo "<pre>" . print_r($sample, true) . "</pre>";
                        break; // Keluar dari loop jika sudah berhasil
                    } else {
                        echo "<p><strong>WARNING:</strong> Tidak ada data dengan kelompok bahan perpustakaan ditemukan!</p>";
                        
                        // Tampilkan kelompok yang ada (sample 10 saja)
                        $groups = array_slice(array_unique(array_column($records, 'kelompok')), 0, 10);
                        echo "<p><strong>Kelompok yang tersedia (sample):</strong></p>";
                        echo "<ul>";
                        foreach ($groups as $group) {
                            echo "<li>" . htmlspecialchars($group) . "</li>";
                        }
                        echo "</ul>";
                    }
                    
                } else {
                    echo "<p><strong>ERROR:</strong> Response bukan array</p>";
                    echo "<pre>" . htmlspecialchars(substr($body, 0, 1000)) . "</pre>";
                }
                
            } elseif ($response->getStatusCode() === 301) {
                echo "<p><strong>REDIRECT (301):</strong> URL moved permanently</p>";
                $headers = $response->getHeaders();
                if (isset($headers['Location'])) {
                    echo "<p><strong>Redirect to:</strong> " . htmlspecialchars($headers['Location']) . "</p>";
                }
            } else {
                echo "<p><strong>ERROR:</strong> HTTP Status " . $response->getStatusCode() . "</p>";
                echo "<pre>" . htmlspecialchars($response->getBody()) . "</pre>";
            }
            
        } catch (\Exception $e) {
            echo "<p><strong>Exception:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
        }
        
        echo "<hr>";
    }
    
    echo "<p><a href='" . base_url('user/barang/asettetaplainnya/bahanperpustakaan/kelompokbahanperpustakaan') . "'>Kembali ke Kelompok Bahan Perpustakaan</a></p>";
    
    exit;
}

}