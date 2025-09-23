<?php
namespace App\Controllers\User\Barang\GedungDanBangunan;

use App\Controllers\BaseController;
use App\Models\BangunanGedungModel;

class BangunanGedung extends BaseController
{
    protected $bangunanGedungModel;
    
    public function __construct()
    {
        $this->bangunanGedungModel = new BangunanGedungModel();
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
        $bangunanGedungList = $this->getApiData();
        return view('user/bangunangedung/dashboardbangunangedung', [
            'bangunanGedungList' => $bangunanGedungList
        ]);
    }

    public function kelompokBangunanGedung()
    {
        $sort = $this->request->getGet('sort') ?? 'kode_barang';
        $order = $this->request->getGet('order') ?? 'asc';
        
        $allBangunanGedungList = $this->bangunanGedungModel->findAll();
        
        // Filter berdasarkan kelompok - hanya 2 kelompok valid
        $tempatKerjaData = array_filter($allBangunanGedungList, function ($item) {
            return strtoupper($item['kelompok'] ?? '') === 'BANGUNAN GEDUNG TEMPAT KERJA';
        });

        $tempatTinggalData = array_filter($allBangunanGedungList, function ($item) {
            return strtoupper($item['kelompok'] ?? '') === 'BANGUNAN GEDUNG TEMPAT TINGGAL';
        });

        // Reset array keys dan gabungkan
        $allData = array_merge(
            array_values($tempatKerjaData),
            array_values($tempatTinggalData)
        );

        return view('user/barang/gedungdanbangunan/bangunangedung/kelompokbangunangedung', [
            'sort' => $sort,
            'order' => $order,
            'allData' => $allData,
        ]);
    }

    public function kelompokDetail($kelompok)
    {
        // Decode URL untuk handle karakter khusus
        $kelompok = urldecode($kelompok);
        
        log_message('info', "kelompokDetail called with: " . $kelompok);
        
        // Validasi kelompok yang valid
        if (!$this->bangunanGedungModel->isValidKelompokBangunanGedung($kelompok)) {
            session()->setFlashdata('error', 'Kelompok tidak valid: ' . $kelompok);
            return redirect()->to('user/barang/gedungdanbangunan/bangunangedung/kelompokbangunangedung');
        }
        
        // Debug: cek data di database
        $debugCount = $this->bangunanGedungModel->where('UPPER(kelompok)', strtoupper($kelompok))->countAllResults();
        log_message('info', "Data found for kelompok '{$kelompok}': {$debugCount}");
        
        if ($debugCount == 0) {
            session()->setFlashdata('error', "Tidak ada data untuk kelompok: {$kelompok}");
            return redirect()->to('user/barang/gedungdanbangunan/bangunangedung/kelompokbangunangedung');
        }
        
        $searchTerm = $this->request->getGet('search') ?? '';
        $sort = $this->request->getGet('sort') ?? 'kode_barang';
        $order = $this->request->getGet('order') ?? 'asc';
        $perPage = 100;
        $page = $this->request->getGet('page') ?? 1;

        // Gunakan database sebagai sumber data
        $builder = $this->bangunanGedungModel->builder();
        
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
        $bangunanGedungList = $builder->limit($perPage, $offset)->get()->getResultArray();

        // Setup pagination
        $pager = service('pager');
        $pager->setPath('user/barang/gedungdanbangunan/bangunangedung/kelompokbangunangedung/' . urlencode($kelompok));
        $totalPages = ceil($totalItems / $perPage);

        return view('user/barang/gedungdanbangunan/bangunangedung/kelompokbangunangedung', [
            'bangunanGedungList' => $bangunanGedungList,
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

    // Method untuk menambah bangunan gedung manual
    public function tambah()
    {
        log_message('info', '=== TAMBAH BANGUNAN GEDUNG METHOD DIPANGGIL ===');
        
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
            
            // Mapping kelompok menggunakan method dari model
            $kategori_detail = $this->bangunanGedungModel->mapKelompokToKategori($kelompok);
            
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
                'kategori_utama' => 'GEDUNG DAN BANGUNAN',
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
            if (!$this->bangunanGedungModel->isValidKelompokBangunanGedung($data['kelompok'])) {
                $errors[] = 'Kelompok harus salah satu dari: ' . implode(', ', $this->bangunanGedungModel->getValidKelompok());
            }

            if (!empty($errors)) {
                session()->setFlashdata('error', 'Error: ' . implode(', ', $errors));
                return redirect()->back()->withInput();
            }

            try {
                $this->bangunanGedungModel->skipValidation(true);
                $insertResult = $this->bangunanGedungModel->insert($data);
                
                if ($insertResult) {
                    $insertId = $this->bangunanGedungModel->getInsertID();
                    session()->setFlashdata('success', "Data bangunan gedung berhasil disimpan! ID: {$insertId}");
                } else {
                    $errors = $this->bangunanGedungModel->errors();
                    session()->setFlashdata('error', 'Gagal menyimpan data: ' . implode(', ', $errors));
                }
                
                $this->bangunanGedungModel->skipValidation(false);
                
            } catch (\Exception $e) {
                session()->setFlashdata('error', 'Error database: ' . $e->getMessage());
                $this->bangunanGedungModel->skipValidation(false);
            }
        }
        
        return redirect()->to('user/barang/gedungdanbangunan/bangunangedung/kelompokbangunangedung');
    }

    // Method untuk reset semua data
    public function resetData()
    {
        try {
            $this->bangunanGedungModel->builder()->truncate();
            
            session()->setFlashdata('success', 'Semua data berhasil dihapus!');
            return redirect()->to('user/barang/gedungdanbangunan/bangunangedung/kelompokbangunangedung');
            
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

            $this->bangunanGedungModel->skipValidation(true);

            // Kelompok yang valid untuk bangunan gedung - hanya 2
            $validKelompok = [
                'BANGUNAN GEDUNG TEMPAT KERJA',
                'BANGUNAN GEDUNG TEMPAT TINGGAL'
            ];

            foreach ($apiData as $index => $item) {
                try {
                    $kode_barang = trim($item['kode_barang'] ?? '');
                    $kelompok_api = strtoupper(trim($item['kelompok'] ?? ''));
                    
                    if (empty($kode_barang)) {
                        $skipped++;
                        continue;
                    }

                    // FILTER: Hanya import data dengan kelompok bangunan gedung yang valid
                    if (!in_array($kelompok_api, $validKelompok)) {
                        $filtered++;
                        log_message('info', "Filtered out: {$kode_barang} - Kelompok: '{$kelompok_api}' (bukan bangunan gedung)");
                        continue;
                    }

                    log_message('info', "Importing: {$kode_barang} - Kelompok: '{$kelompok_api}'");

                    $unique_kode = $kode_barang . '_' . $index;

                    // Mapping kelompok menggunakan method dari model
                    $kategori_detail = $this->bangunanGedungModel->mapKelompokToKategori($kelompok_api);

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
                        'luas_dasar_bangunan' => $this->safeFloat($item['luas_dasar_bangunan'] ?? 0),
                        'luas_bangunan' => $this->safeFloat($item['luas_bangunan'] ?? 0),
                        'jumlah_lantai' => intval($item['jumlah_lantai'] ?? 1),
                        'kategori_utama' => 'GEDUNG DAN BANGUNAN',
                        'kategori_detail' => $kategori_detail,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];

                    if ($this->bangunanGedungModel->insert($data)) {
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

            $this->bangunanGedungModel->skipValidation(false);

            $total = count($apiData);
            $message = "Import selesai! Total API: {$total}, Berhasil: {$imported}, Dilewati: {$skipped}, Difilter (bukan bangunan gedung): {$filtered}";
            
            if (!empty($errors)) {
                $message .= ", Error: " . count($errors);
                log_message('error', 'Import errors: ' . implode(', ', $errors));
            }

            session()->setFlashdata('success', $message);
            return redirect()->to('user/barang/gedungdanbangunan/bangunangedung/kelompokbangunangedung');

        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Gagal import data: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    // Method export ke CSV
    public function exportBangunanGedungList($jenis = 'semua')
    {
        $jenisValid = ['tempatkerja', 'tempattinggal', 'semua'];
        if (!in_array($jenis, $jenisValid)) {
            $jenis = 'semua';
        }

        $allBangunanGedungList = $this->bangunanGedungModel->findAll();
        
        // Filter berdasarkan jenis
        if ($jenis !== 'semua') {
            $bangunanGedungList = array_filter($allBangunanGedungList, function($item) use ($jenis) {
                $kelompok = strtolower($item['kelompok'] ?? '');
                
                switch ($jenis) {
                    case 'tempatkerja':
                        return strpos($kelompok, 'tempat kerja') !== false;
                    case 'tempattinggal':
                        return strpos($kelompok, 'tempat tinggal') !== false;
                    default:
                        return true;
                }
            });
            $bangunanGedungList = array_values($bangunanGedungList);
        } else {
            $bangunanGedungList = $allBangunanGedungList;
        }

        $filename = 'bangunan_gedung_' . $jenis . '_' . date('Y-m-d') . '.csv';
        
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
        foreach ($bangunanGedungList as $item) {
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
        $totalData = $this->bangunanGedungModel->countAllResults();
        $apiData = $this->getApiData();
        $totalApi = count($apiData);
        
        // Statistik per kelompok
        $dbStats = [
            'total' => $totalData,
            'tempat_kerja' => $this->bangunanGedungModel->countByKelompok('BANGUNAN GEDUNG TEMPAT KERJA'),
            'tempat_tinggal' => $this->bangunanGedungModel->countByKelompok('BANGUNAN GEDUNG TEMPAT TINGGAL')
        ];
        
        return view('user/bangunangedung/stats', [
            'totalData' => $totalData,
            'totalApi' => $totalApi,
            'dbStats' => $dbStats
        ]);
    }

    // Method untuk test API (debugging)
    public function testApi()
    {
        $apiData = $this->getApiData();
        
        echo "<h3>Test API Bangunan Gedung</h3>";
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
            
            // Filter untuk bangunan gedung
            $validKelompok = [
                'BANGUNAN GEDUNG TEMPAT KERJA',
                'BANGUNAN GEDUNG TEMPAT TINGGAL'
            ];
            $filteredData = array_filter($apiData, function($item) use ($validKelompok) {
                return in_array(strtoupper($item['kelompok'] ?? ''), $validKelompok);
            });
            
            echo "<h4>Data yang akan diimport (kelompok bangunan gedung):</h4>";
            echo "<p>Total: " . count($filteredData) . " dari " . count($apiData) . " data</p>";
            
            if (!empty($filteredData)) {
                echo "<h5>Sample data bangunan gedung:</h5>";
                echo "<pre>" . json_encode(array_slice($filteredData, 0, 3), JSON_PRETTY_PRINT) . "</pre>";
            }
        } else {
            echo "<p style='color: red;'>Tidak ada data dari API atau terjadi error!</p>";
        }
    }

    // Method khusus untuk handle gedung tempat kerja
    public function tempatKerja()
    {
        return $this->kelompokDetail('BANGUNAN GEDUNG TEMPAT KERJA');
    }

    // Method khusus untuk handle gedung tempat tinggal
    public function tempatTinggal()
    {
        return $this->kelompokDetail('BANGUNAN GEDUNG TEMPAT TINGGAL');
    }
}