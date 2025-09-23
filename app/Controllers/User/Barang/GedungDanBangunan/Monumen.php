<?php
namespace App\Controllers\User\Barang\GedungDanBangunan;

use App\Controllers\BaseController;
use App\Models\MonumenModel;

class Monumen extends BaseController
{
    protected $monumentModel;
    
    public function __construct()
    {
        $this->monumentModel = new MonumenModel();
    }

    // Method untuk mengambil data dari API
    private function getApiData($url = null)
    {
        $client = \Config\Services::curlrequest();
        $apiKey = 'c877acaa0de297a9e3b8bbdb101dd254d33a92a0444b979d599e04fdeaccdbc5';
        
        if (!$url) {
            $url = "https://apigw.pu.go.id/v1/siman/gedung-bangunan?api_key={$apiKey}";
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
        $dashboardData = $this->monumentModel->getDashboardData();
        return view('user/monumen/dashboardmonumen', [
            'dashboardData' => $dashboardData
        ]);
    }

    public function kelompokMonumen()
    {
        $sort = $this->request->getGet('sort') ?? 'kode_barang';
        $order = $this->request->getGet('order') ?? 'asc';
        $searchTerm = $this->request->getGet('search') ?? '';
        $perPage = 100;
        $page = $this->request->getGet('page') ?? 1;

        // Semua data monumen dalam satu tampilan karena hanya 1 kategori detail
        $builder = $this->monumentModel->builder();
        
        // Filter berdasarkan pencarian
        if (!empty($searchTerm)) {
            $builder->groupStart()
                ->like('nama_barang', $searchTerm)
                ->orLike('kode_barang', $searchTerm) 
                ->orLike('merk', $searchTerm)
                ->orLike('sub_kelompok', $searchTerm)
                ->orLike('kelompok', $searchTerm)
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
        $monumentList = $builder->limit($perPage, $offset)->get()->getResultArray();

        // Setup pagination
        $pager = service('pager');
        $totalPages = ceil($totalItems / $perPage);

        // Statistik per kelompok untuk card
        $allData = $this->monumentModel->findAll();
        
        return view('user/barang/gedungdanbangunan/monumen/kelompokmonumen', [
            'monumentList' => $monumentList,
            'allData' => $allData,
            'pager' => $pager,
            'searchTerm' => $searchTerm,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalItems' => $totalItems, 
            'sort' => $sort, 
            'order' => $order  
        ]);
    }

    // Method untuk menambah monumen manual
    public function tambah()
    {
        log_message('info', '=== TAMBAH MONUMEN METHOD DIPANGGIL ===');
        
        $method = $_SERVER['REQUEST_METHOD'] ?? 'unknown';
        $postData = $this->request->getPost();
        $postRaw = $_POST;
        
        $isPost = (strtoupper($method) === 'POST') || !empty($postData) || !empty($postRaw);
        
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
            $luas_dasar_bangunan = $data_source['luas_dasar_bangunan'] ?? '';
            $luas_bangunan = $data_source['luas_bangunan'] ?? '';
            $jumlah_lantai = $data_source['jumlah_lantai'] ?? '';
            
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
                'luas_dasar_bangunan' => $this->safeFloat($luas_dasar_bangunan),
                'luas_bangunan' => $this->safeFloat($luas_bangunan),
                'jumlah_lantai' => intval($jumlah_lantai ?: 1),
                'kategori_utama' => 'GEDUNG DAN BANGUNAN - MONUMEN',
                'kategori_detail' => 'Candi/Tugu Peringatan/Prasasti', // Semua monumen masuk kategori ini
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
            if (!$this->monumentModel->isValidKelompokMonumen($data['kelompok'])) {
                $errors[] = 'Kelompok harus salah satu dari: ' . implode(', ', $this->monumentModel->getValidKelompok());
            }

            if (!empty($errors)) {
                session()->setFlashdata('error', 'Error: ' . implode(', ', $errors));
                return redirect()->back()->withInput();
            }

            try {
                $this->monumentModel->skipValidation(true);
                $insertResult = $this->monumentModel->insert($data);
                
                if ($insertResult) {
                    $insertId = $this->monumentModel->getInsertID();
                    session()->setFlashdata('success', "Data monumen berhasil disimpan! ID: {$insertId}");
                } else {
                    $errors = $this->monumentModel->errors();
                    session()->setFlashdata('error', 'Gagal menyimpan data: ' . implode(', ', $errors));
                }
                
                $this->monumentModel->skipValidation(false);
                
            } catch (\Exception $e) {
                session()->setFlashdata('error', 'Error database: ' . $e->getMessage());
                $this->monumentModel->skipValidation(false);
            }
        }
        
        return redirect()->to('user/barang/gedungdanbangunan/monumen/kelompokmonumen');
    }

    // Method untuk reset semua data
    public function resetData()
    {
        try {
            $this->monumentModel->builder()->truncate();
            
            session()->setFlashdata('success', 'Semua data berhasil dihapus!');
            return redirect()->to('user/barang/gedungdanbangunan/monumen/kelompokmonumen');
            
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Gagal menghapus data: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    // Method import dari API
    // Method import dari API - DIPERBAIKI
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

        $this->monumentModel->skipValidation(true);

        // Kelompok yang valid untuk monumen - UPDATE INI
        $validKelompok = [
            'CANDI',
            'TUGU PERINGATAN', 
            'PRASASTI',
            'MONUMEN',
            'CANDI/TUGU PERINGATAN/PRASASTI' // Tambahkan ini!
        ];

        foreach ($apiData as $index => $item) {
            try {
                $kode_barang = trim($item['kode_barang'] ?? '');
                $kelompok_api = strtoupper(trim($item['kelompok'] ?? ''));
                
                if (empty($kode_barang)) {
                    $skipped++;
                    continue;
                }

                // FILTER: Hanya import data dengan kelompok monumen yang valid
                if (!in_array($kelompok_api, $validKelompok)) {
                    $filtered++;
                    log_message('info', "Filtered out: {$kode_barang} - Kelompok: '{$kelompok_api}' (bukan monumen)");
                    continue;
                }

                log_message('info', "Importing: {$kode_barang} - Kelompok: '{$kelompok_api}'");

                $unique_kode = $kode_barang . '_' . $index;

                // Clean data menggunakan method dari model
                $cleanedData = $this->monumentModel->cleanImportData($item);
                $cleanedData['kode_barang'] = $unique_kode;
                
                // Jika kelompok dari API adalah 'CANDI/TUGU PERINGATAN/PRASASTI', 
                // kita tetap simpan apa adanya
                $cleanedData['kelompok'] = $kelompok_api;
                
                $cleanedData['created_at'] = date('Y-m-d H:i:s');
                $cleanedData['updated_at'] = date('Y-m-d H:i:s');

                if ($this->monumentModel->insert($cleanedData)) {
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

        $this->monumentModel->skipValidation(false);

        $total = count($apiData);
        $message = "Import selesai! Total API: {$total}, Berhasil: {$imported}, Dilewati: {$skipped}, Difilter (bukan monumen): {$filtered}";
        
        if (!empty($errors)) {
            $message .= ", Error: " . count($errors);
            log_message('error', 'Import errors: ' . implode(', ', $errors));
        }

        session()->setFlashdata('success', $message);
        return redirect()->to('user/barang/gedungdanbangunan/monumen/kelompokmonumen');

    } catch (\Exception $e) {
        session()->setFlashdata('error', 'Gagal import data: ' . $e->getMessage());
        return redirect()->back();
    }
}

    // Method export ke CSV
    public function exportMonumenList($jenis = 'semua')
    {
        $jenisValid = ['candi', 'tuguperingatan', 'prasasti', 'monumen', 'semua'];
        if (!in_array($jenis, $jenisValid)) {
            $jenis = 'semua';
        }

        $allMonumenList = $this->monumentModel->findAll();
        
        // Filter berdasarkan jenis
        if ($jenis !== 'semua') {
            $monumentList = array_filter($allMonumenList, function($item) use ($jenis) {
                $kelompok = strtolower($item['kelompok'] ?? '');
                
                switch ($jenis) {
                    case 'candi':
                        return $kelompok === 'candi';
                    case 'tuguperingatan':
                        return $kelompok === 'tugu peringatan';
                    case 'prasasti':
                        return $kelompok === 'prasasti';
                    case 'monumen':
                        return $kelompok === 'monumen';
                    default:
                        return true;
                }
            });
            $monumentList = array_values($monumentList);
        } else {
            $monumentList = $allMonumenList;
        }

        $filename = 'monumen_' . $jenis . '_' . date('Y-m-d') . '.csv';
        
        $response = service('response');
        $response->setHeader('Content-Type', 'text/csv');
        $response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        fputcsv($output, [
            'No', 'Kode Barang', 'Nama Barang', 'NUP', 'Merk', 'Kelompok', 'Sub Kelompok', 'Kondisi', 
            'Kuantitas', 'Status', 'Nilai Perolehan', 'Nilai Buku', 'Tanggal Perolehan', 'Nama Satker',
            'Luas Dasar (m2)', 'Luas Bangunan (m2)', 'Jumlah Lantai', 'Kategori Detail'
        ]);

        $no = 1;
        foreach ($monumentList as $item) {
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
                number_format(floatval($item['luas_dasar_bangunan'] ?? 0), 2, ',', '.'),
                number_format(floatval($item['luas_bangunan'] ?? 0), 2, ',', '.'),
                $item['jumlah_lantai'] ?? '1',
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
        $totalData = $this->monumentModel->countAllResults();
        $apiData = $this->getApiData();
        $totalApi = count($apiData);
        
        // Statistik per kelompok menggunakan method dari model
        $dbStats = $this->monumentModel->getDashboardData();
        
        return view('user/monumen/stats', [
            'totalData' => $totalData,
            'totalApi' => $totalApi,
            'dbStats' => $dbStats
        ]);
    }

    // Method untuk test API (debugging)
    public function testApi()
    {
        $apiData = $this->getApiData();
        
        echo "<h3>Test API Monumen</h3>";
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
            
            // Filter untuk monumen
            $validKelompok = [
                'CANDI',
                'TUGU PERINGATAN',
                'PRASASTI',
                'MONUMEN'
            ];
            $filteredData = array_filter($apiData, function($item) use ($validKelompok) {
                return in_array(strtoupper($item['kelompok'] ?? ''), $validKelompok);
            });
            
            echo "<h4>Data yang akan diimport (kelompok monumen):</h4>";
            echo "<p>Total: " . count($filteredData) . " dari " . count($apiData) . " data</p>";
            
            if (!empty($filteredData)) {
                echo "<h5>Sample data monumen:</h5>";
                echo "<pre>" . json_encode(array_slice($filteredData, 0, 3), JSON_PRETTY_PRINT) . "</pre>";
            }
        } else {
            echo "<p style='color: red;'>Tidak ada data dari API atau terjadi error!</p>";
        }
    }

    // Method untuk search monumen
    public function search()
    {
        $searchTerm = $this->request->getGet('search') ?? '';
        $kelompok = $this->request->getGet('kelompok') ?? '';
        $limit = $this->request->getGet('limit') ?? 100;
        $offset = $this->request->getGet('offset') ?? 0;

        $results = $this->monumentModel->searchMonumen($searchTerm, $kelompok, $limit, $offset);

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $results,
            'total' => count($results)
        ]);
    }
}