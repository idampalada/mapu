<?php

namespace App\Controllers\User\Barang\PeralatanDanMesin;

use App\Controllers\BaseController;
use App\Models\KomputerModel;

class Komputer extends BaseController
{
    protected $komputerModel;
    
    public function __construct()
    {
        $this->komputerModel = new KomputerModel();
    }

    // Method untuk mengambil data dari API
    private function getApiData($url = null)
    {
        $client = \Config\Services::curlrequest();
        $apiKey = 'c877acaa0de297a9e3b8bbdb101dd254d33a92a0444b979d599e04fdeaccdbc5';
        
        if (!$url) {
            $url = "https://apigw.pu.go.id/v1/siman/pm-tik?api_key={$apiKey}";
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
        $komputerList = $this->getApiData();
        return view('user/komputer/dashboardkomputer', [
            'komputerList' => $komputerList
        ]);
    }

    public function kelompokKomputer()
    {
        $sort = $this->request->getGet('sort') ?? 'kode_barang';
        $order = $this->request->getGet('order') ?? 'asc';
        
        // Menggunakan data dari database untuk konsistensi
        $allKomputerList = $this->komputerModel->findAll();
        
        // Filter data berdasarkan kelompok
        $komputerUnitData = array_filter($allKomputerList, function ($item) {
            return strtolower($item['kelompok'] ?? '') === 'komputer unit';
        });

        $peralatanKomputerData = array_filter($allKomputerList, function ($item) {
            return strtolower($item['kelompok'] ?? '') === 'peralatan komputer';
        });

        // Reset array keys
        $komputerUnitData = array_values($komputerUnitData);
        $peralatanKomputerData = array_values($peralatanKomputerData);
        
        // Gabungkan semua data
        $allData = array_merge($komputerUnitData, $peralatanKomputerData);

        return view('user/barang/peralatandanmesin/komputer/kelompokkomputer', [
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
        $builder = $this->komputerModel->builder();
        
        // Filter berdasarkan kelompok
        $builder->where('UPPER(kelompok)', strtoupper($kelompok));
        
        // Filter berdasarkan pencarian
        if (!empty($searchTerm)) {
            $builder->groupStart()
                ->like('nama_barang', $searchTerm)
                ->orLike('kode_barang', $searchTerm) 
                ->orLike('merk', $searchTerm)
                ->orLike('processor', $searchTerm)
                ->orLike('memori', $searchTerm)
                ->orLike('hardisk', $searchTerm)
                ->orLike('monitor', $searchTerm)
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
        $komputerList = $builder->limit($perPage, $offset)->get()->getResultArray();

        // Setup pagination
        $pager = service('pager');
        $pager->setPath('user/barang/peralatandanmesin/komputer/kelompokkomputer/' . urlencode($kelompok));
        $totalPages = ceil($totalItems / $perPage);

        return view('user/barang/peralatandanmesin/komputer/kelompokkomputer', [
            'komputerList' => $komputerList,
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

    // Method untuk menambah komputer manual
    public function tambah()
    {
        log_message('info', '=== TAMBAH KOMPUTER METHOD DIPANGGIL ===');
        
        $method1 = $this->request->getMethod();
        $method2 = $_SERVER['REQUEST_METHOD'] ?? 'unknown';
        $method3 = $this->request->getServer('REQUEST_METHOD');
        
        log_message('info', "Request Method (getMethod): '{$method1}'");
        log_message('info', "Request Method (\$_SERVER): '{$method2}'");
        log_message('info', "Request Method (getServer): '{$method3}'");
        
        $postData = $this->request->getPost();
        $postRaw = $_POST;
        
        log_message('info', 'POST data (request): ' . json_encode($postData));
        log_message('info', 'POST data (raw): ' . json_encode($postRaw));
        log_message('info', 'POST count: ' . count($postData));
        
        $isPost = (strtoupper($method2) === 'POST') || !empty($postData) || !empty($postRaw);
        
        log_message('info', "Is POST determined: " . ($isPost ? 'YES' : 'NO'));
        
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
            $processor = $data_source['processor'] ?? '';
            $memori = $data_source['memori'] ?? '';
            $hardisk = $data_source['hardisk'] ?? '';
            $monitor = $data_source['monitor'] ?? '';
            $spek_lain = $data_source['spek_lain'] ?? '';
            $nilai_perolehan = $data_source['nilai_perolehan'] ?? '';
            $tanggal_perolehan = $data_source['tanggal_perolehan'] ?? '';
            
            log_message('info', "Kode Barang: '{$kode_barang}'");
            log_message('info', "Nama Barang: '{$nama_barang}'");
            log_message('info', "Kelompok: '{$kelompok}'");
            
            $data = [
                'kode_barang' => trim($kode_barang),
                'nama_barang' => trim($nama_barang),
                'nup' => trim($nup),
                'merk' => trim($merk),
                'kelompok' => strtoupper(trim($kelompok)),
                'kondisi' => trim($kondisi),
                'kuantitas' => intval($kuantitas ?: 1),
                'status_penggunaan' => trim($status_penggunaan),
                'processor' => trim($processor),
                'memori' => trim($memori),
                'hardisk' => trim($hardisk),
                'monitor' => trim($monitor),
                'spek_lain' => trim($spek_lain),
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
                
                $this->komputerModel->skipValidation(true);
                
                $insertResult = $this->komputerModel->insert($data);
                
                if ($insertResult) {
                    $insertId = $this->komputerModel->getInsertID();
                    log_message('info', "QUERY BUILDER BERHASIL! Insert ID: {$insertId}");
                    
                    $insertedData = $this->komputerModel->find($insertId);
                    
                    if ($insertedData) {
                        log_message('info', 'DATA BERHASIL DIKONFIRMASI: ' . json_encode($insertedData));
                        session()->setFlashdata('success', "Data komputer berhasil disimpan! ID: {$insertId}");
                    } else {
                        log_message('error', 'INSERT ID ADA TAPI DATA TIDAK DITEMUKAN');
                        session()->setFlashdata('error', 'Data mungkin tersimpan tapi tidak dapat dikonfirmasi');
                    }
                } else {
                    $errors = $this->komputerModel->errors();
                    log_message('error', 'QUERY BUILDER GAGAL: ' . json_encode($errors));
                    session()->setFlashdata('error', 'Gagal menyimpan data: ' . implode(', ', $errors));
                }
                
                $this->komputerModel->skipValidation(false);
                
            } catch (\Exception $e) {
                log_message('error', 'EXCEPTION QUERY BUILDER: ' . $e->getMessage());
                session()->setFlashdata('error', 'Error database: ' . $e->getMessage());
                
                $this->komputerModel->skipValidation(false);
            }

            log_message('info', '=== TAMBAH KOMPUTER METHOD SELESAI ===');
        } else {
            log_message('info', 'TIDAK ADA DATA POST - SKIP PROSES');
        }
        
        return redirect()->to('user/barang/peralatandanmesin/komputer/kelompokkomputer');
    }

    // Method untuk reset semua data
    public function resetData()
    {
        try {
            $this->komputerModel->builder()->truncate();
            
            session()->setFlashdata('success', 'Semua data berhasil dihapus!');
            return redirect()->to('user/barang/peralatandanmesin/komputer/kelompokkomputer');
            
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

            $this->komputerModel->skipValidation(true);

            // Kelompok yang valid untuk komputer
            $validKelompok = ['KOMPUTER UNIT', 'PERALATAN KOMPUTER'];

            foreach ($apiData as $index => $item) {
                try {
                    $kode_barang = trim($item['kode_barang'] ?? '');
                    $kelompok_api = strtoupper(trim($item['kelompok'] ?? ''));
                    
                    if (empty($kode_barang)) {
                        $skipped++;
                        continue;
                    }

                    // FILTER: Hanya import data dengan kelompok komputer yang valid
                    if (!in_array($kelompok_api, $validKelompok)) {
                        $filtered++;
                        log_message('info', "Filtered out: {$kode_barang} - Kelompok: '{$kelompok_api}' (bukan komputer)");
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
                        'processor' => trim($item['processor'] ?? ''),
                        'memori' => trim($item['memori'] ?? ''),
                        'hardisk' => trim($item['hardisk'] ?? ''),
                        'monitor' => trim($item['monitor'] ?? ''),
                        'spek_lain' => trim($item['spek_lain'] ?? ''),
                        'nilai_perolehan' => $this->safeFloat($item['nilai_perolehan'] ?? 0),
                        'tanggal_perolehan' => !empty($item['tanggal_perolehan']) ? $item['tanggal_perolehan'] : null,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];

                    if ($this->komputerModel->insert($data)) {
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

            $this->komputerModel->skipValidation(false);

            $total = count($apiData);
            $message = "Import selesai! Total API: {$total}, Berhasil: {$imported}, Dilewati: {$skipped}, Difilter (bukan komputer): {$filtered}";
            
            if (!empty($errors)) {
                $message .= ", Error: " . count($errors);
                log_message('error', 'Import errors: ' . implode(', ', $errors));
            }

            session()->setFlashdata('success', $message);
            return redirect()->to('user/barang/peralatandanmesin/komputer/kelompokkomputer');

        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Gagal import data: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    // Method export ke CSV
    public function exportKomputerList($jenis = 'semua')
    {
        $jenisValid = ['komputer-unit', 'peralatan-komputer', 'semua'];
        if (!in_array($jenis, $jenisValid)) {
            $jenis = 'semua';
        }

        $allKomputerList = $this->komputerModel->findAll();
        
        // Filter berdasarkan jenis
        if ($jenis !== 'semua') {
            $komputerList = array_filter($allKomputerList, function($item) use ($jenis) {
                $kelompok = strtolower($item['kelompok'] ?? '');
                
                switch ($jenis) {
                    case 'komputer-unit':
                        return strpos($kelompok, 'komputer unit') !== false;
                    case 'peralatan-komputer':
                        return strpos($kelompok, 'peralatan komputer') !== false;
                    default:
                        return true;
                }
            });
            $komputerList = array_values($komputerList);
        } else {
            $komputerList = $allKomputerList;
        }

        $filename = 'komputer_' . $jenis . '_' . date('Y-m-d') . '.csv';
        
        $response = service('response');
        $response->setHeader('Content-Type', 'text/csv');
        $response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        fputcsv($output, [
            'No', 'Kode Barang', 'Nama Barang', 'NUP', 'Merk', 'Kelompok', 'Kondisi', 
            'Kuantitas', 'Status', 'Processor', 'Memori', 'Hardisk', 'Monitor', 'Spek Lain',
            'Nilai Perolehan', 'Tanggal Perolehan'
        ]);

        $no = 1;
        foreach ($komputerList as $item) {
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
                $item['processor'] ?? '-',
                $item['memori'] ?? '-',
                $item['hardisk'] ?? '-',
                $item['monitor'] ?? '-',
                $item['spek_lain'] ?? '-',
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
        $totalData = $this->komputerModel->countAllResults();
        $apiData = $this->getApiData();
        $totalApi = count($apiData);
        
        // Statistik per kelompok
        $dbStats = [
            'total' => $totalData,
            'komputer_unit' => $this->komputerModel->where('kelompok', 'KOMPUTER UNIT')->countAllResults(),
            'peralatan_komputer' => $this->komputerModel->where('kelompok', 'PERALATAN KOMPUTER')->countAllResults(),
        ];
        
        return view('user/komputer/stats', [
            'totalData' => $totalData,
            'totalApi' => $totalApi,
            'dbStats' => $dbStats
        ]);
    }

    // Method untuk test API (debugging)
    public function testApi()
    {
        $apiData = $this->getApiData();
        
        echo "<h3>Test API PM-TIK</h3>";
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
            
            // Filter untuk komputer
            $validKelompok = ['KOMPUTER UNIT', 'PERALATAN KOMPUTER'];
            $filteredData = array_filter($apiData, function($item) use ($validKelompok) {
                return in_array(strtoupper($item['kelompok'] ?? ''), $validKelompok);
            });
            
            echo "<h4>Data yang akan diimport (kelompok komputer):</h4>";
            echo "<p>Total: " . count($filteredData) . " dari " . count($apiData) . " data</p>";
            
            if (!empty($filteredData)) {
                echo "<h5>Sample data komputer:</h5>";
                echo "<pre>" . json_encode(array_slice($filteredData, 0, 3), JSON_PRETTY_PRINT) . "</pre>";
            }
        } else {
            echo "<p style='color: red;'>Tidak ada data dari API atau terjadi error!</p>";
        }
    }
}