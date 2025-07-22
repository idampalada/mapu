<?php

namespace App\Controllers\User\Barang\PeralatanDanMesin;

use App\Controllers\BaseController;
use App\Models\AlatBesarModel;

class AlatBesar extends BaseController
{
    protected $alatBesarModel;
    
    public function __construct()
    {
        $this->alatBesarModel = new AlatBesarModel();
    }

    // Method untuk mengambil data dari API
    private function getApiData($url = null)
    {
        $client = \Config\Services::curlrequest();
        $apiKey = 'c877acaa0de297a9e3b8bbdb101dd254d33a92a0444b979d599e04fdeaccdbc5';
        
        if (!$url) {
            $url = "https://apigw.pu.go.id/v1/siman/alat-berat?api_key={$apiKey}";
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
        $alatBesarList = $this->getApiData();
        return view('user/alatbesar/dashboardalatbesar', [
            'alatBesarList' => $alatBesarList
        ]);
    }

    public function kelompokAlatBesar()
    {
        $sort = $this->request->getGet('sort') ?? 'kode_barang';
        $order = $this->request->getGet('order') ?? 'asc';
        
        // Menggunakan data dari database untuk konsistensi
        $allAlatBesarList = $this->alatBesarModel->findAll();
        
        // Filter data berdasarkan kelompok
        $daratData = array_filter($allAlatBesarList, function ($item) {
            return strtolower($item['kelompok'] ?? '') === 'alat besar darat';
        });

        $bantuData = array_filter($allAlatBesarList, function ($item) {
            return strtolower($item['kelompok'] ?? '') === 'alat bantu';
        });

        $apungData = array_filter($allAlatBesarList, function ($item) {
            return strtolower($item['kelompok'] ?? '') === 'alat besar apung';
        });

        // Reset array keys
        $daratData = array_values($daratData);
        $bantuData = array_values($bantuData);
        $apungData = array_values($apungData);
        
        // Gabungkan semua data
        $allData = array_merge($daratData, $bantuData, $apungData);

        return view('user/barang/peralatandanmesin/alatbesar/kelompokalatbesar', [
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
        $builder = $this->alatBesarModel->builder();
        
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
        $alatBesarList = $builder->limit($perPage, $offset)->get()->getResultArray();

        // Setup pagination
        $pager = service('pager');
        $pager->setPath('user/barang/peralatandanmesin/alatbesar/kelompokalatbesar/' . urlencode($kelompok));
        $totalPages = ceil($totalItems / $perPage);

        return view('user/barang/peralatandanmesin/alatbesar/kelompokalatbesar', [
            'alatBesarList' => $alatBesarList,
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

    // Method untuk menambah alat besar manual
    public function tambah()
    {
        // Log bahwa method dipanggil
        log_message('info', '=== TAMBAH ALAT BESAR METHOD DIPANGGIL ===');
        
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
            $kondisi = $data_source['kondisi'] ?? '';
            $kuantitas = $data_source['kuantitas'] ?? '';
            $status_penggunaan = $data_source['status_penggunaan'] ?? '';
            $tahun_buat = $data_source['tahun_buat'] ?? '';
            $no_mesin = $data_source['no_mesin'] ?? '';
            $no_rangka = $data_source['no_rangka'] ?? '';
            $nilai_perolehan = $data_source['nilai_perolehan'] ?? '';
            $tanggal_perolehan = $data_source['tanggal_perolehan'] ?? '';
            
            log_message('info', "Kode Barang: '{$kode_barang}'");
            log_message('info', "Nama Barang: '{$nama_barang}'");
            log_message('info', "Kelompok: '{$kelompok}'");
            log_message('info', "NUP: '{$nup}'");
            log_message('info', "Merk: '{$merk}'");
            log_message('info', "Kondisi: '{$kondisi}'");
            
            $data = [
                'kode_barang' => trim($kode_barang),
                'nama_barang' => trim($nama_barang),
                'nup' => trim($nup),
                'merk' => trim($merk),
                'kelompok' => strtoupper(trim($kelompok)),
                'kondisi' => trim($kondisi),
                'kuantitas' => intval($kuantitas ?: 1),
                'status_penggunaan' => trim($status_penggunaan),
                'tahun_buat' => trim($tahun_buat),
                'no_mesin' => trim($no_mesin),
                'no_rangka' => trim($no_rangka),
                'nilai_perolehan' => $this->safeFloat($nilai_perolehan),
                'tanggal_perolehan' => !empty($tanggal_perolehan) ? $tanggal_perolehan : null,
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
                $this->alatBesarModel->skipValidation(true);
                
                // Insert menggunakan Query Builder
                $insertResult = $this->alatBesarModel->insert($data);
                
                if ($insertResult) {
                    $insertId = $this->alatBesarModel->getInsertID();
                    log_message('info', "QUERY BUILDER BERHASIL! Insert ID: {$insertId}");
                    
                    // Double check dengan Query Builder
                    $insertedData = $this->alatBesarModel->find($insertId);
                    
                    if ($insertedData) {
                        log_message('info', 'DATA BERHASIL DIKONFIRMASI: ' . json_encode($insertedData));
                        session()->setFlashdata('success', "Data alat besar berhasil disimpan! ID: {$insertId}");
                    } else {
                        log_message('error', 'INSERT ID ADA TAPI DATA TIDAK DITEMUKAN');
                        session()->setFlashdata('error', 'Data mungkin tersimpan tapi tidak dapat dikonfirmasi');
                    }
                } else {
                    $errors = $this->alatBesarModel->errors();
                    log_message('error', 'QUERY BUILDER GAGAL: ' . json_encode($errors));
                    session()->setFlashdata('error', 'Gagal menyimpan data: ' . implode(', ', $errors));
                }
                
                // Restore validation
                $this->alatBesarModel->skipValidation(false);
                
            } catch (\Exception $e) {
                log_message('error', 'EXCEPTION QUERY BUILDER: ' . $e->getMessage());
                session()->setFlashdata('error', 'Error database: ' . $e->getMessage());
                
                // Restore validation jika terjadi error
                $this->alatBesarModel->skipValidation(false);
            }

            log_message('info', '=== TAMBAH ALAT BESAR METHOD SELESAI ===');
        } else {
            log_message('info', 'TIDAK ADA DATA POST - SKIP PROSES');
        }
        
        return redirect()->to('user/barang/peralatandanmesin/alatbesar/kelompokalatbesar');
    }

    // Method untuk reset semua data
    public function resetData()
    {
        try {
            // Hapus semua data dari tabel
            $this->alatBesarModel->builder()->truncate();
            
            session()->setFlashdata('success', 'Semua data berhasil dihapus!');
            return redirect()->to('user/barang/peralatandanmesin/alatbesar/kelompokalatbesar');
            
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
        $filtered = 0; // Data yang difilter karena bukan kelompok alat besar
        $errors = [];

        try {
            // Ambil data dari API
            $apiData = $this->getApiData();

            if (empty($apiData)) {
                session()->setFlashdata('error', 'Tidak ada data dari API atau API tidak dapat diakses!');
                return redirect()->back();
            }

            // Nonaktifkan validation sementara
            $this->alatBesarModel->skipValidation(true);

            // Kelompok yang valid untuk alat besar
            $validKelompok = ['ALAT BESAR DARAT', 'ALAT BANTU', 'ALAT BESAR APUNG'];

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

                    // FILTER: Hanya import data dengan kelompok alat besar yang valid
                    if (!in_array($kelompok_api, $validKelompok)) {
                        $filtered++;
                        log_message('info', "Filtered out: {$kode_barang} - Kelompok: '{$kelompok_api}' (bukan alat besar)");
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
                        'kondisi' => trim($item['kondisi'] ?? ''),
                        'kuantitas' => intval($item['kuantitas'] ?? 1),
                        'status_penggunaan' => trim($item['status_penggunaan'] ?? ''),
                        'tahun_buat' => trim($item['tahun_buat'] ?? ''),
                        'no_mesin' => trim($item['no_mesin'] ?? ''),
                        'no_rangka' => trim($item['no_rangka'] ?? ''),
                        'nilai_perolehan' => $this->safeFloat($item['nilai_perolehan'] ?? 0),
                        'tanggal_perolehan' => !empty($item['tanggal_perolehan']) ? $item['tanggal_perolehan'] : null,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];

                    // Insert ke database
                    if ($this->alatBesarModel->insert($data)) {
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
            $this->alatBesarModel->skipValidation(false);

            // Set pesan hasil dengan info filter
            $total = count($apiData);
            $message = "Import selesai! Total API: {$total}, Berhasil: {$imported}, Dilewati: {$skipped}, Difilter (bukan alat besar): {$filtered}";
            
            if (!empty($errors)) {
                $message .= ", Error: " . count($errors);
                // Log errors untuk debugging
                log_message('error', 'Import errors: ' . implode(', ', $errors));
            }

            session()->setFlashdata('success', $message);
            return redirect()->to('user/barang/peralatandanmesin/alatbesar/kelompokalatbesar');

        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Gagal import data: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    // Method export ke CSV
    public function exportAlatBesarList($jenis = 'semua')
    {
        $jenisValid = ['darat', 'bantu', 'apung', 'semua'];
        if (!in_array($jenis, $jenisValid)) {
            $jenis = 'semua';
        }

        // Ambil data dari database
        $allAlatBesarList = $this->alatBesarModel->findAll();
        
        // Filter berdasarkan jenis
        if ($jenis !== 'semua') {
            $alatBesarList = array_filter($allAlatBesarList, function($item) use ($jenis) {
                $kelompok = strtolower($item['kelompok'] ?? '');
                
                switch ($jenis) {
                    case 'darat':
                        return strpos($kelompok, 'alat besar darat') !== false;
                    case 'bantu':
                        return strpos($kelompok, 'alat bantu') !== false;
                    case 'apung':
                        return strpos($kelompok, 'alat besar apung') !== false;
                    default:
                        return true;
                }
            });
            $alatBesarList = array_values($alatBesarList);
        } else {
            $alatBesarList = $allAlatBesarList;
        }

        // Generate CSV
        $filename = 'alat_besar_' . $jenis . '_' . date('Y-m-d') . '.csv';
        
        $response = service('response');
        $response->setHeader('Content-Type', 'text/csv');
        $response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        fputcsv($output, [
            'No', 'Kode Barang', 'Nama Barang', 'NUP', 'Merk', 'Kelompok', 'Kondisi', 
            'Kuantitas', 'Status', 'Tahun Buat', 'No Mesin', 'No Rangka',
            'Nilai Perolehan', 'Tanggal Perolehan'
        ]);

        $no = 1;
        foreach ($alatBesarList as $item) {
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
                $item['tahun_buat'] ?? '-',
                $item['no_mesin'] ?? '-',
                $item['no_rangka'] ?? '-',
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
        $totalData = $this->alatBesarModel->countAllResults();
        $apiData = $this->getApiData();
        $totalApi = count($apiData);
        
        // Statistik per kelompok
        $dbStats = [
            'total' => $totalData,
            'darat' => $this->alatBesarModel->where('kelompok', 'ALAT BESAR DARAT')->countAllResults(),
            'bantu' => $this->alatBesarModel->where('kelompok', 'ALAT BANTU')->countAllResults(),
            'apung' => $this->alatBesarModel->where('kelompok', 'ALAT BESAR APUNG')->countAllResults(),
        ];
        
        return view('user/alatbesar/stats', [
            'totalData' => $totalData,
            'totalApi' => $totalApi,
            'dbStats' => $dbStats
        ]);
    }
}