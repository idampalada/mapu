<?php

namespace App\Controllers\User\Barang\JalanIrigasiJaringan;

use App\Controllers\BaseController;
use App\Models\JaringanModel;

class Jaringan extends BaseController
{
    protected $jaringanModel;
    
    public function __construct()
    {
        $this->jaringanModel = new JaringanModel();
    }

    public function kelompokJaringan()
    {
        $sort = $this->request->getGet('sort') ?? 'kode_barang';
        $order = $this->request->getGet('order') ?? 'asc';
        
        $allJaringanList = $this->jaringanModel->findAll();
        
        // Filter data berdasarkan kelompok jaringan
        $jaringanAirData = array_filter($allJaringanList, function ($item) {
            return strtoupper($item['kelompok'] ?? '') === 'JARINGAN AIR MINUM';
        });

        $jaringanListrikData = array_filter($allJaringanList, function ($item) {
            return strtoupper($item['kelompok'] ?? '') === 'JARINGAN LISTRIK';
        });

        $jaringanTeleponData = array_filter($allJaringanList, function ($item) {
            return strtoupper($item['kelompok'] ?? '') === 'JARINGAN TELEPON';
        });

        $jaringanGasData = array_filter($allJaringanList, function ($item) {
            return strtoupper($item['kelompok'] ?? '') === 'JARINGAN GAS';
        });

        // Reset array keys dan gabungkan
        $allData = array_merge(
            array_values($jaringanAirData),
            array_values($jaringanListrikData), 
            array_values($jaringanTeleponData),
            array_values($jaringanGasData)
        );

        return view('user/barang/jalanirigasijaringan/jaringan/kelompokjaringan', [
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
        if (!$this->jaringanModel->isValidKelompokJaringan($kelompok)) {
            session()->setFlashdata('error', 'Kelompok tidak valid: ' . $kelompok);
            return redirect()->to('user/barang/jalanirigasijaringan/jaringan/kelompokjaringan');
        }
        
        // Debug: cek data di database
        $debugCount = $this->jaringanModel->where('UPPER(kelompok)', strtoupper($kelompok))->countAllResults();
        log_message('info', "Data found for kelompok '{$kelompok}': {$debugCount}");
        
        if ($debugCount == 0) {
            session()->setFlashdata('error', "Tidak ada data untuk kelompok: {$kelompok}");
            return redirect()->to('user/barang/jalanirigasijaringan/jaringan/kelompokjaringan');
        }
        
        $searchTerm = $this->request->getGet('search') ?? '';
        $sort = $this->request->getGet('sort') ?? 'kode_barang';
        $order = $this->request->getGet('order') ?? 'asc';
        $perPage = 100;
        $page = $this->request->getGet('page') ?? 1;

        // Gunakan database sebagai sumber data
        $builder = $this->jaringanModel->builder();
        
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
        $jaringanList = $builder->limit($perPage, $offset)->get()->getResultArray();

        // Setup pagination
        $pager = service('pager');
        $pager->setPath('user/barang/jalanirigasijaringan/jaringan/kelompokjaringan/' . urlencode($kelompok));
        $totalPages = ceil($totalItems / $perPage);

        return view('user/barang/jalanirigasijaringan/jaringan/kelompokjaringan', [
            'jaringanList' => $jaringanList,
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

    // Method untuk menambah jaringan manual
    public function tambah()
    {
        log_message('info', '=== TAMBAH JARINGAN METHOD DIPANGGIL ===');
        
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
            
            // Mapping kelompok menggunakan method dari model
            $kategori_detail = $this->jaringanModel->mapKelompokToKategori($kelompok);
            
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
                'kategori_utama' => 'JARINGAN',
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
                $this->jaringanModel->skipValidation(true);
                $insertResult = $this->jaringanModel->insert($data);
                
                if ($insertResult) {
                    $insertId = $this->jaringanModel->getInsertID();
                    session()->setFlashdata('success', "Data jaringan berhasil disimpan! ID: {$insertId}");
                } else {
                    $errors = $this->jaringanModel->errors();
                    session()->setFlashdata('error', 'Gagal menyimpan data: ' . implode(', ', $errors));
                }
                
                $this->jaringanModel->skipValidation(false);
                
            } catch (\Exception $e) {
                session()->setFlashdata('error', 'Error database: ' . $e->getMessage());
                $this->jaringanModel->skipValidation(false);
            }
        }
        
        return redirect()->to('user/barang/jalanirigasijaringan/jaringan/kelompokjaringan');
    }

    // Method untuk reset semua data
    public function resetData()
    {
        try {
            $this->jaringanModel->builder()->truncate();
            
            session()->setFlashdata('success', 'Semua data berhasil dihapus!');
            return redirect()->to('user/barang/jalanirigasijaringan/jaringan/kelompokjaringan');
            
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
            // Ambil data dari API menggunakan model
            $apiData = $this->jaringanModel->getAllJaringanFromApi();

            if (empty($apiData)) {
                session()->setFlashdata('error', 'Tidak ada data dari API atau API tidak dapat diakses!');
                return redirect()->back();
            }

            $this->jaringanModel->skipValidation(true);

            // Kelompok yang valid untuk jaringan
            $validKelompok = [
                'JARINGAN AIR MINUM',
                'JARINGAN LISTRIK',
                'JARINGAN TELEPON',
                'JARINGAN GAS'
            ];

            foreach ($apiData as $index => $item) {
                try {
                    $kode_barang = trim($item['kode_barang'] ?? '');
                    $kelompok_api = strtoupper(trim($item['kelompok'] ?? ''));
                    
                    if (empty($kode_barang)) {
                        $skipped++;
                        continue;
                    }

                    // FILTER: Hanya import data dengan kelompok jaringan yang valid
                    if (!in_array($kelompok_api, $validKelompok)) {
                        $filtered++;
                        log_message('info', "Filtered out: {$kode_barang} - Kelompok: '{$kelompok_api}' (bukan jaringan)");
                        continue;
                    }

                    log_message('info', "Importing: {$kode_barang} - Kelompok: '{$kelompok_api}'");

                    $unique_kode = $kode_barang . '_' . $index;

                    // Mapping kelompok menggunakan method dari model
                    $kategori_detail = $this->jaringanModel->mapKelompokToKategori($kelompok_api);

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
                        'kategori_utama' => 'JARINGAN',
                        'kategori_detail' => $kategori_detail,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];

                    if ($this->jaringanModel->insert($data)) {
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

            $this->jaringanModel->skipValidation(false);

            $total = count($apiData);
            $message = "Import selesai! Total API: {$total}, Berhasil: {$imported}, Dilewati: {$skipped}, Difilter (bukan jaringan): {$filtered}";
            
            if (!empty($errors)) {
                $message .= ", Error: " . count($errors);
                log_message('error', 'Import errors: ' . implode(', ', $errors));
            }

            session()->setFlashdata('success', $message);
            return redirect()->to('user/barang/jalanirigasijaringan/jaringan/kelompokjaringan');

        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Gagal import data: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    // Method export ke CSV
    public function exportJaringanList($jenis = 'semua')
    {
        $jenisValid = ['airminum', 'listrik', 'telepon', 'gas', 'semua'];
        if (!in_array($jenis, $jenisValid)) {
            $jenis = 'semua';
        }

        $allJaringanList = $this->jaringanModel->findAll();
        
        // Filter berdasarkan jenis
        if ($jenis !== 'semua') {
            $jaringanList = array_filter($allJaringanList, function($item) use ($jenis) {
                $kelompok = strtolower($item['kelompok'] ?? '');
                
                switch ($jenis) {
                    case 'airminum':
                        return strpos($kelompok, 'jaringan air minum') !== false;
                    case 'listrik':
                        return strpos($kelompok, 'jaringan listrik') !== false;
                    case 'telepon':
                        return strpos($kelompok, 'jaringan telepon') !== false;
                    case 'gas':
                        return strpos($kelompok, 'jaringan gas') !== false;
                    default:
                        return true;
                }
            });
            $jaringanList = array_values($jaringanList);
        } else {
            $jaringanList = $allJaringanList;
        }

        $filename = 'jaringan_' . $jenis . '_' . date('Y-m-d') . '.csv';
        
        $response = service('response');
        $response->setHeader('Content-Type', 'text/csv');
        $response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        fputcsv($output, [
            'No', 'Kode Barang', 'Nama Barang', 'NUP', 'Merk', 'Kelompok', 'Sub Kelompok', 'Kondisi', 
            'Kuantitas', 'Status', 'Nilai Perolehan', 'Nilai Buku', 'Tanggal Perolehan', 'Nama Satker',
            'Kategori Detail'
        ]);

        $no = 1;
        foreach ($jaringanList as $item) {
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
        $totalData = $this->jaringanModel->countAllResults();
        
        // Statistik per kelompok
        $dbStats = [
            'total' => $totalData,
            'air_minum' => $this->jaringanModel->where('kelompok', 'JARINGAN AIR MINUM')->countAllResults(),
            'listrik' => $this->jaringanModel->where('kelompok', 'JARINGAN LISTRIK')->countAllResults(),
            'telepon' => $this->jaringanModel->where('kelompok', 'JARINGAN TELEPON')->countAllResults(),
            'gas' => $this->jaringanModel->where('kelompok', 'JARINGAN GAS')->countAllResults()
        ];
        
        return $this->response->setJSON($dbStats);
    }

    // Method untuk test API (debugging)
    public function testApi()
    {
        $apiData = $this->jaringanModel->getAllJaringanFromApi();
        
        echo "<h3>Test API Jaringan</h3>";
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
            
            // Filter untuk jaringan
            $validKelompok = [
                'JARINGAN AIR MINUM',
                'JARINGAN LISTRIK',
                'JARINGAN TELEPON',
                'JARINGAN GAS'
            ];
            $filteredData = array_filter($apiData, function($item) use ($validKelompok) {
                return in_array(strtoupper($item['kelompok'] ?? ''), $validKelompok);
            });
            
            echo "<h4>Data yang akan diimport (kelompok jaringan):</h4>";
            echo "<p>Total: " . count($filteredData) . " dari " . count($apiData) . " data</p>";
            
            if (!empty($filteredData)) {
                echo "<h5>Sample data jaringan:</h5>";
                echo "<pre>" . json_encode(array_slice($filteredData, 0, 3), JSON_PRETTY_PRINT) . "</pre>";
            }
        } else {
            echo "<p style='color: red;'>Tidak ada data dari API atau terjadi error!</p>";
        }
    }
}