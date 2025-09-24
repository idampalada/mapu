<?php

namespace App\Controllers\User\Barang\JalanIrigasiJaringan;

use App\Controllers\BaseController;
use App\Models\JalanDanJembatanModel;

class JalanDanJembatan extends BaseController
{
    protected $jalanDanJembatanModel;
    
    public function __construct()
    {
        $this->jalanDanJembatanModel = new JalanDanJembatanModel();
    }

    // Method untuk mengambil data dari API
    private function getApiData($url = null)
    {
        $client = \Config\Services::curlrequest();
        $apiKey = 'c877acaa0de297a9e3b8bbdb101dd254d33a92a0444b979d599e04fdeaccdbc5';
        
        if (!$url) {
            $url = "https://apigw.pu.go.id/v1/siman/jalan-dan-jembatan?api_key={$apiKey}";
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
        $jalanDanJembatanList = $this->getApiData();
        return view('user/jalandanjembatan/dashboardjalandanjembatan', [
            'jalanDanJembatanList' => $jalanDanJembatanList
        ]);
    }

    public function kelompokJalanDanJembatan()
    {
        $sort = $this->request->getGet('sort') ?? 'kode_barang';
        $order = $this->request->getGet('order') ?? 'asc';
        
        $allJalanDanJembatanList = $this->jalanDanJembatanModel->findAll();
        
        // Filter berdasarkan kelompok
        $jalanData = array_filter($allJalanDanJembatanList, function ($item) {
            return strtoupper($item['kelompok'] ?? '') === 'JALAN';
        });

        $jembatanData = array_filter($allJalanDanJembatanList, function ($item) {
            return strtoupper($item['kelompok'] ?? '') === 'JEMBATAN';
        });

        // Reset array keys dan gabungkan
        $allData = array_merge(
            array_values($jalanData),
            array_values($jembatanData)
        );

        return view('user/barang/jalanirigasijaringan/jalandanjembatan/kelompokjalandanjembatan', [
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
        if (!$this->jalanDanJembatanModel->isValidKelompokJalanDanJembatan($kelompok)) {
            session()->setFlashdata('error', 'Kelompok tidak valid: ' . $kelompok);
            return redirect()->to('user/barang/jalanirigasijaringan/jalandanjembatan/kelompokjalandanjembatan');
        }
        
        // Debug: cek data di database
        $debugCount = $this->jalanDanJembatanModel->where('UPPER(kelompok)', strtoupper($kelompok))->countAllResults();
        log_message('info', "Data found for kelompok '{$kelompok}': {$debugCount}");
        
        if ($debugCount == 0) {
            session()->setFlashdata('error', "Tidak ada data untuk kelompok: {$kelompok}");
            return redirect()->to('user/barang/jalanirigasijaringan/jalandanjembatan/kelompokjalandanjembatan');
        }
        
        $searchTerm = $this->request->getGet('search') ?? '';
        $sort = $this->request->getGet('sort') ?? 'kode_barang';
        $order = $this->request->getGet('order') ?? 'asc';
        $perPage = 100;
        $page = $this->request->getGet('page') ?? 1;

        // Gunakan database sebagai sumber data
        $builder = $this->jalanDanJembatanModel->builder();
        
        // Filter berdasarkan kelompok
        $builder->where('UPPER(kelompok)', strtoupper($kelompok));
        
        // Filter berdasarkan pencarian
        if (!empty($searchTerm)) {
            $builder->groupStart()
                ->like('nama_barang', $searchTerm)
                ->orLike('kode_barang', $searchTerm) 
                ->orLike('merk', $searchTerm)
                ->orLike('sub_kelompok', $searchTerm)
                ->orLike('konstruksi', $searchTerm)
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
        $jalanDanJembatanList = $builder->limit($perPage, $offset)->get()->getResultArray();

        // Setup pagination
        $pager = service('pager');
        $pager->setPath('user/barang/jalanirigasijaringan/jalandanjembatan/kelompokjalandanjembatan/' . urlencode($kelompok));
        $totalPages = ceil($totalItems / $perPage);

        return view('user/barang/jalanirigasijaringan/jalandanjembatan/kelompokjalandanjembatan', [
            'jalanDanJembatanList' => $jalanDanJembatanList,
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

    // Method untuk menambah data manual
    public function tambah()
    {
        log_message('info', '=== TAMBAH JALAN DAN JEMBATAN METHOD DIPANGGIL ===');
        
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
            $panjang = $data_source['panjang'] ?? '';
            $lebar = $data_source['lebar'] ?? '';
            $luas = $data_source['luas'] ?? '';
            $konstruksi = $data_source['konstruksi'] ?? '';
            
            // Mapping kelompok
            $kategori_detail = $this->jalanDanJembatanModel->mapKelompokToKategori($kelompok);
            
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
                'panjang' => $this->safeFloat($panjang),
                'lebar' => $this->safeFloat($lebar),
                'luas' => $this->safeFloat($luas),
                'konstruksi' => trim($konstruksi),
                'kategori_utama' => 'JALAN DAN JEMBATAN',
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
                $this->jalanDanJembatanModel->skipValidation(true);
                $insertResult = $this->jalanDanJembatanModel->insert($data);
                
                if ($insertResult) {
                    $insertId = $this->jalanDanJembatanModel->getInsertID();
                    session()->setFlashdata('success', "Data berhasil disimpan! ID: {$insertId}");
                } else {
                    $errors = $this->jalanDanJembatanModel->errors();
                    session()->setFlashdata('error', 'Gagal menyimpan data: ' . implode(', ', $errors));
                }
                
                $this->jalanDanJembatanModel->skipValidation(false);
                
            } catch (\Exception $e) {
                session()->setFlashdata('error', 'Error database: ' . $e->getMessage());
                $this->jalanDanJembatanModel->skipValidation(false);
            }
        }
        
        return redirect()->to('user/barang/jalanirigasijaringan/jalandanjembatan/kelompokjalandanjembatan');
    }

    // Method untuk reset semua data
    public function resetData()
    {
        try {
            $this->jalanDanJembatanModel->builder()->truncate();
            
            session()->setFlashdata('success', 'Semua data berhasil dihapus!');
            return redirect()->to('user/barang/jalanirigasijaringan/jalandanjembatan/kelompokjalandanjembatan');
            
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

            $this->jalanDanJembatanModel->skipValidation(true);

            // Kelompok yang valid
            $validKelompok = ['JALAN', 'JEMBATAN'];

            foreach ($apiData as $index => $item) {
                try {
                    $kode_barang = trim($item['kode_barang'] ?? '');
                    $kelompok_api = strtoupper(trim($item['kelompok'] ?? ''));
                    
                    if (empty($kode_barang)) {
                        $skipped++;
                        continue;
                    }

                    // FILTER: Hanya import data dengan kelompok JALAN atau JEMBATAN
                    if (!in_array($kelompok_api, $validKelompok)) {
                        $filtered++;
                        log_message('info', "Filtered out: {$kode_barang} - Kelompok: '{$kelompok_api}' (bukan jalan/jembatan)");
                        continue;
                    }

                    log_message('info', "Importing: {$kode_barang} - Kelompok: '{$kelompok_api}'");

                    $unique_kode = $kode_barang . '_' . $index;

                    // Mapping kelompok
                    $kategori_detail = $this->jalanDanJembatanModel->mapKelompokToKategori($kelompok_api);

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
                        'panjang' => $this->safeFloat($item['panjang'] ?? 0),
                        'lebar' => $this->safeFloat($item['lebar'] ?? 0),
                        'luas' => $this->safeFloat($item['luas'] ?? 0),
                        'konstruksi' => trim($item['konstruksi'] ?? ''),
                        'kategori_utama' => 'JALAN DAN JEMBATAN',
                        'kategori_detail' => $kategori_detail,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];

                    if ($this->jalanDanJembatanModel->insert($data)) {
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

            $this->jalanDanJembatanModel->skipValidation(false);

            $total = count($apiData);
            $message = "Import selesai! Total API: {$total}, Berhasil: {$imported}, Dilewati: {$skipped}, Difilter (bukan jalan/jembatan): {$filtered}";
            
            if (!empty($errors)) {
                $message .= ", Error: " . count($errors);
                log_message('error', 'Import errors: ' . implode(', ', $errors));
            }

            session()->setFlashdata('success', $message);
            return redirect()->to('user/barang/jalanirigasijaringan/jalandanjembatan/kelompokjalandanjembatan');

        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Gagal import data: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    // Method export ke CSV
    public function exportJalanDanJembatanList($jenis = 'semua')
    {
        $jenisValid = ['jalan', 'jembatan', 'semua'];
        if (!in_array($jenis, $jenisValid)) {
            $jenis = 'semua';
        }

        $allList = $this->jalanDanJembatanModel->findAll();
        
        // Filter berdasarkan jenis
        if ($jenis !== 'semua') {
            $list = array_filter($allList, function($item) use ($jenis) {
                $kelompok = strtolower($item['kelompok'] ?? '');
                return strpos($kelompok, $jenis) !== false;
            });
            $list = array_values($list);
        } else {
            $list = $allList;
        }

        $filename = 'jalan_dan_jembatan_' . $jenis . '_' . date('Y-m-d') . '.csv';
        
        $response = service('response');
        $response->setHeader('Content-Type', 'text/csv');
        $response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        fputcsv($output, [
            'No', 'Kode Barang', 'Nama Barang', 'NUP', 'Merk', 'Kelompok', 'Sub Kelompok', 'Kondisi', 
            'Kuantitas', 'Status', 'Nilai Perolehan', 'Nilai Buku', 'Tanggal Perolehan', 'Nama Satker',
            'Panjang (m)', 'Lebar (m)', 'Luas (m²)', 'Konstruksi'
        ]);

        $no = 1;
        foreach ($list as $item) {
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
                number_format(floatval($item['panjang'] ?? 0), 2, ',', '.'),
                number_format(floatval($item['lebar'] ?? 0), 2, ',', '.'),
                number_format(floatval($item['luas'] ?? 0), 2, ',', '.'),
                $item['konstruksi'] ?? '-'
            ]);
        }

        fclose($output);
        return $response;
    }

    // Helper method
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

    // Method untuk test API
    public function testApi()
    {
        echo "<h2>🔍 DEBUG API JALAN DAN JEMBATAN</h2>";
        echo "<hr>";
        
        $apiKey = 'c877acaa0de297a9e3b8bbdb101dd254d33a92a0444b979d599e04fdeaccdbc5';
        $apiUrl = "https://apigw.pu.go.id/v1/siman/jalan-dan-jembatan?api_key={$apiKey}";
        
        echo "<h3>1️⃣ Info API</h3>";
        echo "<p><strong>URL:</strong> {$apiUrl}</p>";
        echo "<hr>";
        
        $apiData = $this->getApiData();
        
        echo "<p><strong>Total data dari API:</strong> " . count($apiData) . "</p>";
        
        if (empty($apiData)) {
            echo "<div style='background: #fee; padding: 15px;'>";
            echo "<h4 style='color: red;'>❌ TIDAK ADA DATA!</h4>";
            echo "</div>";
            return;
        }
        
        echo "<hr>";
        echo "<h3>Sample Data (3 items)</h3>";
        echo "<pre style='background: #f5f5f5; padding: 15px;'>";
        echo json_encode(array_slice($apiData, 0, 3), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        echo "</pre>";
        echo "<hr>";
        
        $kelompokStats = [];
        foreach ($apiData as $item) {
            $kelompok = strtoupper(trim($item['kelompok'] ?? 'UNKNOWN'));
            $kelompokStats[$kelompok] = ($kelompokStats[$kelompok] ?? 0) + 1;
        }
        
        arsort($kelompokStats);
        
        echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
        echo "<tr style='background: #333; color: white;'>";
        echo "<th>No</th><th>Kelompok</th><th>Jumlah</th></tr>";
        
        $no = 1;
        foreach ($kelompokStats as $kelompok => $jumlah) {
            echo "<tr><td>{$no}</td><td><strong>{$kelompok}</strong></td><td>{$jumlah}</td></tr>";
            $no++;
        }
        echo "</table>";
    }
}
    