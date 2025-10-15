<?php

namespace App\Controllers\User\Barang\PeralatanDanMesin;

use App\Controllers\BaseController;
use App\Models\PeralatanOlahragaModel;

class PeralatanOlahraga extends BaseController
{
    protected $peralatanOlahragaModel;
    
    public function __construct()
    {
        $this->peralatanOlahragaModel = new PeralatanOlahragaModel();
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
        $peralatanOlahragaList = $this->getApiData();
        return view('user/barang/peralatandanmesin/peralatanolahraga/dashboard', [
            'peralatanOlahragaList' => $peralatanOlahragaList
        ]);
    }

    public function kelompokPeralatanOlahraga()
    {
        $sort = $this->request->getGet('sort') ?? 'kode_barang';
        $order = $this->request->getGet('order') ?? 'asc';
        
        $allPeralatanOlahragaList = $this->peralatanOlahragaModel->findAll();
        
        // Filter data berdasarkan kelompok - 1 kategori
        $kelompokList = [
            'PERALATAN OLAHRAGA'
        ];

        $kelompokData = [];
        foreach ($kelompokList as $kelompok) {
            $data = array_filter($allPeralatanOlahragaList, function ($item) use ($kelompok) {
                return strtoupper($item['kelompok'] ?? '') === $kelompok;
            });
            $kelompokData[$kelompok] = array_values($data);
        }

        // Gabungkan semua data
        $allData = !empty($kelompokData) ? array_merge(...array_values($kelompokData)) : [];

        return view('user/barang/peralatandanmesin/peralatanolahraga/kelompokperalatanolahraga', [
            'sort' => $sort,
            'order' => $order,
            'allData' => $allData,
            'kelompokData' => $kelompokData
        ]);
    }

    public function kelompokDetail($kelompok)
    {
        // Decode URL untuk handle karakter khusus
        $kelompok = urldecode($kelompok);
        
        // Log untuk debugging
        log_message('info', "kelompokDetail called with: " . $kelompok);
        
        // Validasi kelompok yang valid
        if (!$this->peralatanOlahragaModel->isValidKelompokPeralatanOlahraga($kelompok)) {
            session()->setFlashdata('error', 'Kelompok tidak valid: ' . $kelompok);
            return redirect()->to('user/barang/peralatandanmesin/peralatanolahraga/kelompokperalatanolahraga');
        }
        
        $page = $this->request->getGet('page') ?? 1;
        $perPage = 50;
        $searchTerm = $this->request->getGet('search') ?? '';
        $sort = $this->request->getGet('sort') ?? 'kode_barang';
        $order = $this->request->getGet('order') ?? 'asc';

        // Get data dari model dengan search dan kelompok filter
        $peralatanOlahragaList = $this->peralatanOlahragaModel->searchPeralatanOlahraga(
            $searchTerm, 
            $kelompok, 
            $perPage, 
            ($page - 1) * $perPage
        );

        // Jika hasil kosong dan ada search term, coba tanpa filter kelompok
        if (empty($peralatanOlahragaList) && !empty($searchTerm)) {
            $peralatanOlahragaList = $this->peralatanOlahragaModel->searchPeralatanOlahraga(
                $searchTerm, 
                '', 
                $perPage, 
                ($page - 1) * $perPage
            );
        }

        // Sorting jika diperlukan
        if (!empty($peralatanOlahragaList)) {
            usort($peralatanOlahragaList, function($a, $b) use ($sort, $order) {
                $aVal = $a[$sort] ?? '';
                $bVal = $b[$sort] ?? '';
                
                if ($sort === 'nilai_perolehan') {
                    $aVal = floatval($aVal);
                    $bVal = floatval($bVal);
                    return $order === 'desc' ? $bVal <=> $aVal : $aVal <=> $bVal;
                }
                
                return $order === 'desc' ? strcasecmp($bVal, $aVal) : strcasecmp($aVal, $bVal);
            });
        }

        // Hitung total items untuk pagination
        $totalItems = !empty($searchTerm) 
            ? count($this->peralatanOlahragaModel->searchPeralatanOlahraga($searchTerm, $kelompok, 999999, 0))
            : $this->peralatanOlahragaModel->countByKelompok($kelompok);

        $pager = service('pager');
        $pager->setPath('user/barang/peralatandanmesin/peralatanolahraga/kelompokperalatanolahraga/' . 
                        urlencode($kelompok));
        $totalPages = ceil($totalItems / $perPage);

        return view('user/barang/peralatandanmesin/peralatanolahraga/kelompokperalatanolahraga', [
            'peralatanOlahragaList' => $peralatanOlahragaList,
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

    // Method untuk menambah peralatan olahraga manual
    public function tambah()
    {
        log_message('info', '=== TAMBAH PERALATAN OLAHRAGA METHOD DIPANGGIL ===');
        
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
            $kategori_detail = $this->peralatanOlahragaModel->mapKelompokToKategori($kelompok);
            
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
                'kategori_utama' => 'PERALATAN OLAHRAGA',
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
                $this->peralatanOlahragaModel->skipValidation(true);
                $insertResult = $this->peralatanOlahragaModel->insert($data);
                
                if ($insertResult) {
                    $insertId = $this->peralatanOlahragaModel->getInsertID();
                    session()->setFlashdata('success', "Data peralatan olahraga berhasil disimpan! ID: {$insertId}");
                } else {
                    $errors = $this->peralatanOlahragaModel->errors();
                    session()->setFlashdata('error', 'Gagal menyimpan data: ' . implode(', ', $errors));
                }
                
                $this->peralatanOlahragaModel->skipValidation(false);
                
            } catch (\Exception $e) {
                session()->setFlashdata('error', 'Error database: ' . $e->getMessage());
                $this->peralatanOlahragaModel->skipValidation(false);
            }
        }
        
        return redirect()->to('user/barang/peralatandanmesin/peralatanolahraga/kelompokperalatanolahraga');
    }

    // Method untuk import data dari API
    public function importFromApi()
    {
        log_message('info', '=== IMPORT PERALATAN OLAHRAGA FROM API STARTED ===');
        
        $imported = 0;
        $updated = 0;
        $skipped = 0;
        $filtered = 0;
        $errors = [];

        try {
            $allApiData = $this->getApiData();
            
            if (empty($allApiData)) {
                session()->setFlashdata('error', 'Tidak ada data yang ditemukan dari API atau API sedang bermasalah');
                return redirect()->back();
            }

            log_message('info', 'Total data dari API: ' . count($allApiData));
            
            $this->peralatanOlahragaModel->skipValidation(true);

            // Kelompok yang valid untuk kategori 3.19
            $validKelompok = [
                'PERALATAN OLAH RAGA'
            ];
            
            foreach ($allApiData as $index => $apiItem) {
                try {
                    $kode_barang_original = trim($apiItem['kode_barang'] ?? '');
                    $kelompok_raw = strtoupper(trim($apiItem['kelompok'] ?? ''));
                    
                    log_message('info', "Processing item {$index}: kode={$kode_barang_original}, kelompok={$kelompok_raw}");
                    
                    if (empty($kode_barang_original)) {
                        $skipped++;
                        log_message('info', "Skipped: empty kode_barang at index {$index}");
                        continue;
                    }

                    // FILTER: Hanya import data dengan kelompok kategori 3.19 yang valid
                    if (!in_array($kelompok_raw, $validKelompok)) {
                        $filtered++;
                        log_message('info', "Filtered out: {$kode_barang_original} - Kelompok: '{$kelompok_raw}' (bukan kategori 3.19)");
                        continue;
                    }

                    // BUAT KODE UNIK untuk menghindari duplikasi
                    $unique_kode = $kode_barang_original . '_' . $index;
                    
                    // Override kode_barang di apiItem
                    $apiItem['kode_barang'] = $unique_kode;

                    log_message('info', "Importing: {$kode_barang_original} -> {$unique_kode} - Kelompok: '{$kelompok_raw}'");

                    // Clean data menggunakan kode yang sudah unik
                    $cleanData = $this->peralatanOlahragaModel->cleanImportData($apiItem);
                    
                    // Pastikan kode unik digunakan
                    $cleanData['kode_barang'] = $unique_kode;
                    
                    // Debug: log cleaned data untuk item pertama saja
                    if ($index < 3) {
                        log_message('info', "Sample cleaned data for {$unique_kode}: " . json_encode($cleanData));
                    }
                    
                    // Basic validation
                    $validationErrors = [];
                    if (empty($cleanData['kode_barang']) || empty($cleanData['nama_barang']) || empty($cleanData['kelompok'])) {
                        $validationErrors[] = "Required field kosong";
                    }
                    
                    // Cek kelompok valid
                    if (!$this->peralatanOlahragaModel->isValidKelompokPeralatanOlahraga($cleanData['kelompok'])) {
                        $validationErrors[] = "Kelompok tidak valid: " . $cleanData['kelompok'];
                    }
                    
                    if (!empty($validationErrors)) {
                        $errors[] = "Kode: {$unique_kode} - " . implode(', ', $validationErrors);
                        log_message('error', "Validation failed for {$unique_kode}: " . implode(', ', $validationErrors));
                        continue;
                    }
                    
                    // Check apakah data sudah exist berdasarkan kode_barang unik
                    $existingData = $this->peralatanOlahragaModel->findByKodeBarang($unique_kode);
                    
                    if ($existingData) {
                        // Update existing data
                        $result = $this->peralatanOlahragaModel->updateData($existingData['id'], $cleanData);
                        if ($result) {
                            $updated++;
                            log_message('info', "Updated: {$unique_kode}");
                        } else {
                            $errors[] = "Kode: {$unique_kode} - Update failed";
                            log_message('error', "Update failed for {$unique_kode}");
                        }
                    } else {
                        // Insert new data
                        $result = $this->peralatanOlahragaModel->insertData($cleanData);
                        if ($result) {
                            $imported++;
                            log_message('info', "Inserted: {$unique_kode}");
                        } else {
                            $errors[] = "Kode: {$unique_kode} - Insert failed";
                            log_message('error', "Insert failed for {$unique_kode}");
                        }
                    }
                    
                } catch (\Exception $e) {
                    $kode = $apiItem['kode_barang'] ?? 'unknown';
                    $errors[] = "Kode: {$kode} - Database error: " . $e->getMessage();
                    log_message('error', "Exception importing {$kode}: " . $e->getMessage());
                }
            }
            
            $this->peralatanOlahragaModel->skipValidation(false);
            
            $total = count($allApiData);
            $processed = $imported + $updated;
            $message = "Import selesai! Total API: {$total}, Diproses: {$processed} (Baru: {$imported}, Update: {$updated}), Dilewati: {$skipped}, Difilter: {$filtered}";
            
            if (!empty($errors)) {
                $message .= ". Errors: " . count($errors) . " item(s)";
                log_message('error', 'Import errors: ' . json_encode($errors));
            }
            
            session()->setFlashdata('success', $message);
            log_message('info', $message);
            
        } catch (\Exception $e) {
            log_message('error', 'Import API Error: ' . $e->getMessage());
            session()->setFlashdata('error', 'Error saat import: ' . $e->getMessage());
        }
        
        return redirect()->to('user/barang/peralatandanmesin/peralatanolahraga/kelompokperalatanolahraga');
    }

    // Method untuk reset semua data
    public function resetData()
    {
        try {
            $this->peralatanOlahragaModel->builder()->truncate();
            
            session()->setFlashdata('success', 'Semua data berhasil dihapus!');
            return redirect()->to('user/barang/peralatandanmesin/peralatanolahraga/kelompokperalatanolahraga');
            
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Gagal menghapus data: ' . $e->getMessage());
            return redirect()->to('user/barang/peralatandanmesin/peralatanolahraga/kelompokperalatanolahraga');
        }
    }

    // Method untuk export data berdasarkan kelompok
    public function exportPeralatanOlahragaList($kelompokSlug)
    {
        // Mapping slug ke kelompok yang sebenarnya
        $kelompokMapping = [
            'peralatanolahraga' => 'PERALATAN OLAHRAGA'
        ];
        
        $kelompok = $kelompokMapping[$kelompokSlug] ?? '';
        
        if (empty($kelompok)) {
            session()->setFlashdata('error', 'Kelompok tidak valid untuk export');
            return redirect()->back();
        }
        
        try {
            $data = $this->peralatanOlahragaModel->getByKelompok($kelompok);
            
            if (empty($data)) {
                session()->setFlashdata('error', 'Tidak ada data untuk diekspor pada kelompok ' . $kelompok);
                return redirect()->back();
            }
            
            $filename = 'peralatan_olahraga_' . strtolower(str_replace([' ', '/', '&', '-'], '_', $kelompok)) . '_' . date('Y-m-d_H-i-s') . '.csv';
            
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            
            $output = fopen('php://output', 'w');
            
            // Header CSV
            fputcsv($output, [
                'No', 'Kode Barang', 'Nama Barang', 'Merk', 'NUP', 'Kelompok', 
                'Sub Kelompok', 'Kondisi', 'Kuantitas', 'Spek Lain', 'Nilai Perolehan', 
                'Nilai Buku', 'Tanggal Perolehan', 'Status Penggunaan', 'Nama Satker'
            ]);
            
            // Data rows
            $no = 1;
            foreach ($data as $item) {
                fputcsv($output, [
                    $no++,
                    $item['kode_barang'] ?? '',
                    $item['nama_barang'] ?? '',
                    $item['merk'] ?? '',
                    $item['nup'] ?? '',
                    $item['kelompok'] ?? '',
                    $item['sub_kelompok'] ?? '',
                    $item['kondisi'] ?? '',
                    $item['kuantitas'] ?? '',
                    $item['spek_lain'] ?? '',
                    $item['nilai_perolehan'] ?? '',
                    $item['nilai_buku'] ?? '',
                    $item['tanggal_perolehan'] ?? '',
                    $item['status_penggunaan'] ?? '',
                    $item['nama_satker'] ?? ''
                ]);
            }
            
            fclose($output);
            exit;
            
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Error saat export: ' . $e->getMessage());
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

    // Method untuk statistik (optional)
    public function stats()
    {
        try {
            $stats = $this->peralatanOlahragaModel->getDashboardData();
            return $this->response->setJSON($stats);
        } catch (\Exception $e) {
            return $this->response->setJSON(['error' => $e->getMessage()])->setStatusCode(500);
        }
    }

    // Method untuk test API connection dan debug
    public function testApi()
    {
        try {
            $apiData = $this->getApiData();
            
            echo "<h3>Test API Peralatan Olahraga</h3>";
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
                
                // Filter untuk kategori 3.19
                $validKelompok = [
                    'PERALATAN OLAHRAGA'
                ];
                
                $filteredData = array_filter($apiData, function($item) use ($validKelompok) {
                    return in_array(strtoupper($item['kelompok'] ?? ''), $validKelompok);
                });
                
                echo "<h4>Data yang akan diimport (kategori 3.19):</h4>";
                echo "<p>Total: " . count($filteredData) . " dari " . count($apiData) . " data</p>";
                
                if (!empty($filteredData)) {
                    echo "<h5>Sample data kategori 3.19:</h5>";
                    echo "<pre>" . json_encode(array_slice($filteredData, 0, 3), JSON_PRETTY_PRINT) . "</pre>";
                }
                
            } else {
                echo "<p style='color: red;'>Tidak ada data dari API atau terjadi error!</p>";
            }
            
            return $this->response->setJSON([
                'status' => 'success',
                'total_data' => count($apiData),
                'sample_data' => array_slice($apiData, 0, 3)
            ]);
        } catch (\Exception $e) {
            echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    // Method untuk search (optional)
    public function search()
    {
        $searchTerm = $this->request->getGet('q') ?? '';
        $kelompok = $this->request->getGet('kelompok') ?? '';
        
        if (empty($searchTerm)) {
            return $this->response->setJSON(['results' => []]);
        }
        
        try {
            $results = $this->peralatanOlahragaModel->searchPeralatanOlahraga($searchTerm, $kelompok, 10);
            return $this->response->setJSON(['results' => $results]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['error' => $e->getMessage()])->setStatusCode(500);
        }
    }
}