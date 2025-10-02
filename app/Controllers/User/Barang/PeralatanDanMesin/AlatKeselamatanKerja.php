<?php

namespace App\Controllers\User\Barang\PeralatanDanMesin;

use App\Controllers\BaseController;
use App\Models\AlatKeselamatanKerjaModel;

class AlatKeselamatanKerja extends BaseController
{
    protected $alatKeselamatanKerjaModel;
    
    public function __construct()
    {
        $this->alatKeselamatanKerjaModel = new AlatKeselamatanKerjaModel();
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
        $alatKeselamatanKerjaList = $this->getApiData();
        return view('user/barang/peralatandanmesin/alatkeselamatankerja/dashboard', [
            'alatKeselamatanKerjaList' => $alatKeselamatanKerjaList
        ]);
    }

    public function kelompokAlatKeselamatanKerja()
    {
        $sort = $this->request->getGet('sort') ?? 'kode_barang';
        $order = $this->request->getGet('order') ?? 'asc';
        
        $allAlatKeselamatanKerjaList = $this->alatKeselamatanKerjaModel->findAll();
        
        // Filter data berdasarkan kelompok - 4 kategori
        $kelompokList = [
            'ALAT DETEKSI',
            'ALAT PELINDUNG',
            'ALAT SAR',
            'ALAT KERJA PENERBANGAN'
        ];

        $kelompokData = [];
        foreach ($kelompokList as $kelompok) {
            $data = array_filter($allAlatKeselamatanKerjaList, function ($item) use ($kelompok) {
                return strtoupper($item['kelompok'] ?? '') === $kelompok;
            });
            $kelompokData[$kelompok] = array_values($data);
        }

        // Gabungkan semua data
        $allData = array_merge(...array_values($kelompokData));

        return view('user/barang/peralatandanmesin/alatkeselamatankerja/kelompokalatkeselamatankerja', [
            'sort' => $sort,
            'order' => $order,
            'allData' => $allData,
            'kelompokData' => $kelompokData
        ]);
    }

    public function kelompokDetail($kelompok)
    {
        $kelompok = urldecode($kelompok);
        log_message('info', "kelompokDetail called with: " . $kelompok);
        
        if (!$this->alatKeselamatanKerjaModel->isValidKelompokAlatKeselamatanKerja($kelompok)) {
            session()->setFlashdata('error', 'Kelompok tidak valid: ' . $kelompok);
            return redirect()->to('user/barang/peralatandanmesin/alatkeselamatankerja/kelompokalatkeselamatankerja');
        }
        
        $page = $this->request->getGet('page') ?? 1;
        $perPage = 50;
        $searchTerm = $this->request->getGet('search') ?? '';
        $sort = $this->request->getGet('sort') ?? 'kode_barang';
        $order = $this->request->getGet('order') ?? 'asc';

        $alatKeselamatanKerjaList = $this->alatKeselamatanKerjaModel->searchAlatKeselamatanKerja(
            $searchTerm, 
            $kelompok, 
            $perPage, 
            ($page - 1) * $perPage
        );

        if (empty($alatKeselamatanKerjaList) && !empty($searchTerm)) {
            $alatKeselamatanKerjaList = $this->alatKeselamatanKerjaModel->searchAlatKeselamatanKerja(
                $searchTerm, 
                '', 
                $perPage, 
                ($page - 1) * $perPage
            );
        }

        if (!empty($alatKeselamatanKerjaList)) {
            usort($alatKeselamatanKerjaList, function($a, $b) use ($sort, $order) {
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

        $totalItems = !empty($searchTerm) 
            ? count($this->alatKeselamatanKerjaModel->searchAlatKeselamatanKerja($searchTerm, $kelompok, 999999, 0))
            : $this->alatKeselamatanKerjaModel->countByKelompok($kelompok);

        $pager = service('pager');
        $pager->setPath('user/barang/peralatandanmesin/alatkeselamatankerja/kelompokalatkeselamatankerja/' . 
                        urlencode($kelompok));
        $totalPages = ceil($totalItems / $perPage);

        return view('user/barang/peralatandanmesin/alatkeselamatankerja/kelompokalatkeselamatankerja', [
            'alatKeselamatanKerjaList' => $alatKeselamatanKerjaList,
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

    public function tambah()
    {
        log_message('info', '=== TAMBAH ALAT KESELAMATAN KERJA METHOD DIPANGGIL ===');
        
        $method2 = $_SERVER['REQUEST_METHOD'] ?? 'unknown';
        $postData = $this->request->getPost();
        $postRaw = $_POST;
        
        $isPost = (strtoupper($method2) === 'POST') || !empty($postData) || !empty($postRaw);
        
        if ($isPost && (!empty($postData) || !empty($postRaw))) {
            log_message('info', 'MASUK KE PROSES POST');
            
            $data_source = !empty($postData) ? $postData : $postRaw;
            
            $kategori_detail = $this->alatKeselamatanKerjaModel->mapKelompokToKategori($data_source['kelompok'] ?? '');
            
            $data = [
                'kode_barang' => trim($data_source['kode_barang'] ?? ''),
                'nama_barang' => trim($data_source['nama_barang'] ?? ''),
                'nup' => trim($data_source['nup'] ?? ''),
                'merk' => trim($data_source['merk'] ?? ''),
                'kelompok' => strtoupper(trim($data_source['kelompok'] ?? '')),
                'sub_kelompok' => trim($data_source['sub_kelompok'] ?? ''),
                'kondisi' => trim($data_source['kondisi'] ?? ''),
                'kuantitas' => intval($data_source['kuantitas'] ?? 1),
                'status_penggunaan' => trim($data_source['status_penggunaan'] ?? ''),
                'nilai_perolehan' => $this->safeFloat($data_source['nilai_perolehan'] ?? 0),
                'nilai_buku' => $this->safeFloat($data_source['nilai_buku'] ?? 0),
                'tanggal_perolehan' => !empty($data_source['tanggal_perolehan']) ? $data_source['tanggal_perolehan'] : null,
                'nama_satker' => trim($data_source['nama_satker'] ?? ''),
                'spek_lain' => trim($data_source['spek_lain'] ?? ''),
                'kategori_utama' => 'ALAT KESELAMATAN KERJA',
                'kategori_detail' => $kategori_detail,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $errors = [];
            if (empty($data['kode_barang'])) $errors[] = 'Kode barang harus diisi';
            if (empty($data['nama_barang'])) $errors[] = 'Nama barang harus diisi';
            if (empty($data['kelompok'])) $errors[] = 'Kelompok harus diisi';

            if (!empty($errors)) {
                session()->setFlashdata('error', 'Error: ' . implode(', ', $errors));
                return redirect()->back()->withInput();
            }

            try {
                $this->alatKeselamatanKerjaModel->skipValidation(true);
                $insertResult = $this->alatKeselamatanKerjaModel->insert($data);
                
                if ($insertResult) {
                    $insertId = $this->alatKeselamatanKerjaModel->getInsertID();
                    session()->setFlashdata('success', "Data berhasil disimpan! ID: {$insertId}");
                } else {
                    $errors = $this->alatKeselamatanKerjaModel->errors();
                    session()->setFlashdata('error', 'Gagal menyimpan data: ' . implode(', ', $errors));
                }
                
                $this->alatKeselamatanKerjaModel->skipValidation(false);
                
            } catch (\Exception $e) {
                session()->setFlashdata('error', 'Error database: ' . $e->getMessage());
                $this->alatKeselamatanKerjaModel->skipValidation(false);
            }
        }
        
        return redirect()->to('user/barang/peralatandanmesin/alatkeselamatankerja/kelompokalatkeselamatankerja');
    }

    public function importFromApi()
    {
        log_message('info', '=== IMPORT ALAT KESELAMATAN KERJA FROM API STARTED ===');
        
        $imported = 0;
        $updated = 0;
        $skipped = 0;
        $filtered = 0;
        $errors = [];

        try {
            $allApiData = $this->getApiData();
            
            if (empty($allApiData)) {
                session()->setFlashdata('error', 'Tidak ada data yang ditemukan dari API');
                return redirect()->back();
            }

            log_message('info', 'Total data dari API: ' . count($allApiData));
            
            $this->alatKeselamatanKerjaModel->skipValidation(true);

            $validKelompok = [
                'ALAT DETEKSI',
                'ALAT PELINDUNG',
                'ALAT SAR',
                'ALAT KERJA PENERBANGAN'
            ];
            
            foreach ($allApiData as $index => $apiItem) {
                try {
                    $kode_barang_original = trim($apiItem['kode_barang'] ?? '');
                    $kelompok_raw = strtoupper(trim($apiItem['kelompok'] ?? ''));
                    
                    if (empty($kode_barang_original)) {
                        $skipped++;
                        continue;
                    }

                    if (!in_array($kelompok_raw, $validKelompok)) {
                        $filtered++;
                        continue;
                    }

                    $unique_kode = $kode_barang_original . '_' . $index;
                    $apiItem['kode_barang'] = $unique_kode;

                    $cleanData = $this->alatKeselamatanKerjaModel->cleanImportData($apiItem);
                    $cleanData['kode_barang'] = $unique_kode;
                    
                    $validationErrors = [];
                    if (empty($cleanData['kode_barang']) || empty($cleanData['nama_barang']) || empty($cleanData['kelompok'])) {
                        $validationErrors[] = "Required field kosong";
                    }
                    
                    if (!$this->alatKeselamatanKerjaModel->isValidKelompokAlatKeselamatanKerja($cleanData['kelompok'])) {
                        $validationErrors[] = "Kelompok tidak valid";
                    }
                    
                    if (!empty($validationErrors)) {
                        $errors[] = "Kode: {$unique_kode} - " . implode(', ', $validationErrors);
                        continue;
                    }
                    
                    $existingData = $this->alatKeselamatanKerjaModel->findByKodeBarang($unique_kode);
                    
                    if ($existingData) {
                        if ($this->alatKeselamatanKerjaModel->updateData($existingData['id'], $cleanData)) {
                            $updated++;
                        }
                    } else {
                        if ($this->alatKeselamatanKerjaModel->insertData($cleanData)) {
                            $imported++;
                        }
                    }
                    
                } catch (\Exception $e) {
                    $kode = $apiItem['kode_barang'] ?? 'unknown';
                    $errors[] = "Kode: {$kode} - " . $e->getMessage();
                }
            }
            
            $this->alatKeselamatanKerjaModel->skipValidation(false);
            
            $total = count($allApiData);
            $processed = $imported + $updated;
            $message = "Import selesai! Total API: {$total}, Diproses: {$processed} (Baru: {$imported}, Update: {$updated}), Dilewati: {$skipped}, Difilter: {$filtered}";
            
            if (!empty($errors)) {
                $message .= ". Errors: " . count($errors) . " item(s)";
            }
            
            session()->setFlashdata('success', $message);
            
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Error saat import: ' . $e->getMessage());
        }
        
        return redirect()->to('user/barang/peralatandanmesin/alatkeselamatankerja/kelompokalatkeselamatankerja');
    }

    public function resetData()
    {
        try {
            $this->alatKeselamatanKerjaModel->builder()->truncate();
            session()->setFlashdata('success', 'Semua data berhasil dihapus!');
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
        
        return redirect()->to('user/barang/peralatandanmesin/alatkeselamatankerja/kelompokalatkeselamatankerja');
    }

    public function exportAlatKeselamatanKerjaList($kelompokSlug)
    {
        $kelompokMapping = [
            'alatdeteksi' => 'ALAT DETEKSI',
            'alatpelindung' => 'ALAT PELINDUNG',
            'alatsar' => 'ALAT SAR',
            'alatkerjapenerbangan' => 'ALAT KERJA PENERBANGAN'
        ];
        
        $kelompok = $kelompokMapping[$kelompokSlug] ?? '';
        
        if (empty($kelompok)) {
            session()->setFlashdata('error', 'Kelompok tidak valid');
            return redirect()->back();
        }
        
        try {
            $data = $this->alatKeselamatanKerjaModel->getByKelompok($kelompok);
            
            if (empty($data)) {
                session()->setFlashdata('error', 'Tidak ada data untuk diekspor');
                return redirect()->back();
            }
            
            $filename = 'alat_keselamatan_kerja_' . strtolower(str_replace([' ', '/'], '_', $kelompok)) . '_' . date('Y-m-d_H-i-s') . '.csv';
            
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            
            $output = fopen('php://output', 'w');
            
            fputcsv($output, [
                'No', 'Kode Barang', 'Nama Barang', 'Merk', 'NUP', 'Kelompok', 
                'Sub Kelompok', 'Kondisi', 'Kuantitas', 'Spek Lain', 'Nilai Perolehan', 
                'Nilai Buku', 'Tanggal Perolehan', 'Status Penggunaan', 'Nama Satker'
            ]);
            
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

    private function safeFloat($value)
    {
        if (is_null($value) || $value === '') return 0.0;
        if (is_string($value)) $value = str_replace(',', '.', $value);
        return floatval($value);
    }

    public function stats()
    {
        try {
            $stats = $this->alatKeselamatanKerjaModel->getDashboardData();
            return $this->response->setJSON($stats);
        } catch (\Exception $e) {
            return $this->response->setJSON(['error' => $e->getMessage()])->setStatusCode(500);
        }
    }

    public function testApi()
    {
        $apiData = $this->getApiData();
        echo "<h3>Test API Alat Keselamatan Kerja - kategori 3.15</h3>";
        echo "<p>Total: " . count($apiData) . "</p>";
        return $this->response->setJSON(['status' => 'success', 'total' => count($apiData)]);
    }

    public function search()
    {
        $searchTerm = $this->request->getGet('q') ?? '';
        $kelompok = $this->request->getGet('kelompok') ?? '';
        
        if (empty($searchTerm)) {
            return $this->response->setJSON(['results' => []]);
        }
        
        try {
            $results = $this->alatKeselamatanKerjaModel->searchAlatKeselamatanKerja($searchTerm, $kelompok, 10);
            return $this->response->setJSON(['results' => $results]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['error' => $e->getMessage()])->setStatusCode(500);
        }
    }
}