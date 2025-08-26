<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\TanahModel;

class Tanah extends BaseController
{
    protected $tanahModel;
    
    public function __construct()
    {
        $this->tanahModel = new TanahModel();
    }

    // Method untuk mengambil data dari API
    private function getApiData($url = null)
    {
        $client = \Config\Services::curlrequest();
        $apiKey = 'c877acaa0de297a9e3b8bbdb101dd254d33a92a0444b979d599e04fdeaccdbc5';
        
        if (!$url) {
            $url = "https://apigw.pu.go.id/v1/siman/tanah?api_key={$apiKey}";
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
        $tanahList = $this->getApiData();
        return view('user/tanah/dashboardtanah', [
            'tanahList' => $tanahList
        ]);
    }

    public function kelompokTanah()
    {
        $sort = $this->request->getGet('sort') ?? 'kode_barang';
        $order = $this->request->getGet('order') ?? 'asc';
        
        // Menggunakan data dari database untuk konsistensi
        $allTanahList = $this->tanahModel->findAll();
        
        // Filter data berdasarkan kelompok
        $persilData = array_filter($allTanahList, function ($item) {
            return strtolower($item['kelompok'] ?? '') === 'tanah persil';
        });

        $nonPersilData = array_filter($allTanahList, function ($item) {
            return strtolower($item['kelompok'] ?? '') === 'tanah non persil';
        });

        $lapanganData = array_filter($allTanahList, function ($item) {
            return strtolower($item['kelompok'] ?? '') === 'lapangan';
        });

        // Reset array keys
        $persilData = array_values($persilData);
        $nonPersilData = array_values($nonPersilData);
        $lapanganData = array_values($lapanganData);
        
        // Gabungkan semua data
        $allData = array_merge($persilData, $nonPersilData, $lapanganData);

        return view('user/barang/kelompoktanah', [
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
        $builder = $this->tanahModel->builder();
        
        // Filter berdasarkan kelompok
        $builder->where('UPPER(kelompok)', strtoupper($kelompok));
        
        // Filter berdasarkan pencarian
        if (!empty($searchTerm)) {
            $builder->groupStart()
                ->like('nama_barang', $searchTerm)
                ->orLike('kode_barang', $searchTerm) 
                ->orLike('alamat', $searchTerm)
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
        $tanahList = $builder->limit($perPage, $offset)->get()->getResultArray();

        // Setup pagination
        $pager = service('pager');
        $pager->setPath('user/tanah/kelompoktanah/' . urlencode($kelompok));
        $totalPages = ceil($totalItems / $perPage);

        return view('user/barang/kelompoktanah', [
            'tanahList' => $tanahList,
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

    // Method untuk menambah tanah manual - CONVERTED TO QUERY BUILDER
    public function tambahTanah()
    {
        // Log bahwa method dipanggil
        log_message('info', '=== TAMBAH TANAH METHOD DIPANGGIL ===');
        
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
            $alamat = $data_source['alamat'] ?? '';
            $kelompok = $data_source['kelompok'] ?? '';
            $luas = $data_source['luas_tanah_seluruhnya'] ?? '';
            $status = $data_source['status_penggunaan'] ?? '';
            
            log_message('info', "Kode Barang: '{$kode_barang}'");
            log_message('info', "Nama Barang: '{$nama_barang}'");
            log_message('info', "Alamat: '{$alamat}'");
            log_message('info', "Kelompok: '{$kelompok}'");
            log_message('info', "Luas: '{$luas}'");
            log_message('info', "Status: '{$status}'");
            
            $data = [
                'kode_barang' => trim($kode_barang),
                'nama_barang' => trim($nama_barang),
                'alamat' => trim($alamat),
                'kelompok' => strtoupper(trim($kelompok)),
                'luas_tanah_seluruhnya' => floatval($luas ?: 0),
                'status_penggunaan' => trim($status),
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

            // CONVERTED TO QUERY BUILDER
            try {
                log_message('info', 'MENCOBA INSERT DENGAN QUERY BUILDER...');
                
                // Skip validation untuk operasi insert manual
                $this->tanahModel->skipValidation(true);
                
                // Insert menggunakan Query Builder
                $insertResult = $this->tanahModel->insert($data);
                
                if ($insertResult) {
                    $insertId = $this->tanahModel->getInsertID();
                    log_message('info', "QUERY BUILDER BERHASIL! Insert ID: {$insertId}");
                    
                    // Double check dengan Query Builder
                    $insertedData = $this->tanahModel->find($insertId);
                    
                    if ($insertedData) {
                        log_message('info', 'DATA BERHASIL DIKONFIRMASI: ' . json_encode($insertedData));
                        session()->setFlashdata('success', "Data tanah berhasil disimpan! ID: {$insertId}");
                    } else {
                        log_message('error', 'INSERT ID ADA TAPI DATA TIDAK DITEMUKAN');
                        session()->setFlashdata('error', 'Data mungkin tersimpan tapi tidak dapat dikonfirmasi');
                    }
                } else {
                    $errors = $this->tanahModel->errors();
                    log_message('error', 'QUERY BUILDER GAGAL: ' . json_encode($errors));
                    session()->setFlashdata('error', 'Gagal menyimpan data: ' . implode(', ', $errors));
                }
                
                // Restore validation
                $this->tanahModel->skipValidation(false);
                
            } catch (\Exception $e) {
                log_message('error', 'EXCEPTION QUERY BUILDER: ' . $e->getMessage());
                session()->setFlashdata('error', 'Error database: ' . $e->getMessage());
                
                // Restore validation jika terjadi error
                $this->tanahModel->skipValidation(false);
            }

            log_message('info', '=== TAMBAH TANAH METHOD SELESAI ===');
        } else {
            log_message('info', 'TIDAK ADA DATA POST - SKIP PROSES');
        }
        
        return redirect()->to('user/tanah/kelompoktanah');
    }

    
// Method untuk reset semua data
public function resetData()
{
    try {
        // Hapus semua data dari tabel
        $this->tanahModel->builder()->truncate();
        
        session()->setFlashdata('success', 'Semua data berhasil dihapus!');
        return redirect()->to('user/tanah/kelompoktanah');
        
    } catch (\Exception $e) {
        session()->setFlashdata('error', 'Gagal menghapus data: ' . $e->getMessage());
        return redirect()->back();
    }
}

// Method import dari API - Import SEMUA data tanpa cek duplikasi
public function importFromApi()
{
    $imported = 0;
    $skipped = 0;
    $errors = [];

    try {
        // Ambil data dari API
        $apiData = $this->getApiData();

        if (empty($apiData)) {
            session()->setFlashdata('error', 'Tidak ada data dari API atau API tidak dapat diakses!');
            return redirect()->back();
        }

        // Nonaktifkan validation sementara
        $this->tanahModel->skipValidation(true);

        foreach ($apiData as $index => $item) {
            try {
                // Bersihkan data dan handle null/empty
                $kode_barang = trim($item['kode_barang'] ?? '');
                
                // Skip HANYA jika kode barang benar-benar kosong
                if (empty($kode_barang)) {
                    $skipped++;
                    continue;
                }

                // TIDAK CEK DUPLIKASI - Import semua data
                // Beri suffix untuk menghindari error unique constraint jika ada
                $unique_kode = $kode_barang . '_' . $index;

                // Siapkan data dengan default values untuk field kosong
                $data = [
                    'kode_barang' => $unique_kode, // Buat unik dengan menambah index
                    'nama_barang' => trim($item['nama_barang'] ?? '') ?: 'Unknown',
                    'alamat' => trim($item['alamat'] ?? ''),
                    'kelompok' => strtoupper(trim($item['kelompok'] ?? 'UNKNOWN')),
                    'luas_tanah_seluruhnya' => $this->safeFloat($item['luas_tanah_seluruhnya'] ?? 0),
                    'status_penggunaan' => trim($item['status_penggunaan'] ?? ''),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                // Insert ke database tanpa cek duplikasi
                if ($this->tanahModel->insert($data)) {
                    $imported++;
                } else {
                    $errors[] = $kode_barang;
                }

            } catch (\Exception $e) {
                $errors[] = ($kode_barang ?? 'unknown') . ': ' . $e->getMessage();
            }
        }

        // Aktifkan kembali validation
        $this->tanahModel->skipValidation(false);

        // Set pesan hasil
        $total = count($apiData);
        $message = "Import selesai! Total API: {$total}, Berhasil: {$imported}, Dilewati: {$skipped}";
        
        if (!empty($errors)) {
            $message .= ", Error: " . count($errors);
            // Log errors untuk debugging
            log_message('error', 'Import errors: ' . implode(', ', $errors));
        }

        session()->setFlashdata('success', $message);
        return redirect()->to('user/tanah/kelompoktanah');

    } catch (\Exception $e) {
        session()->setFlashdata('error', 'Gagal import data: ' . $e->getMessage());
        return redirect()->back();
    }
}

    // Method export ke CSV
    public function exportTanahList($jenis = 'semua')
    {
        $jenisValid = ['persil', 'nonpersil', 'lapangan', 'semua'];
        if (!in_array($jenis, $jenisValid)) {
            $jenis = 'semua';
        }

        // Ambil data dari database
        $allTanahList = $this->tanahModel->findAll();
        
        // Filter berdasarkan jenis
        if ($jenis !== 'semua') {
            $tanahList = array_filter($allTanahList, function($item) use ($jenis) {
                $kelompok = strtolower($item['kelompok'] ?? '');
                
                switch ($jenis) {
                    case 'persil':
                        return strpos($kelompok, 'persil') !== false && strpos($kelompok, 'non') === false;
                    case 'nonpersil':
                        return strpos($kelompok, 'non persil') !== false;
                    case 'lapangan':
                        return strpos($kelompok, 'lapangan') !== false;
                    default:
                        return true;
                }
            });
            $tanahList = array_values($tanahList);
        } else {
            $tanahList = $allTanahList;
        }

        // Generate CSV
        $filename = 'tanah_' . $jenis . '_' . date('Y-m-d') . '.csv';
        
        $response = service('response');
        $response->setHeader('Content-Type', 'text/csv');
        $response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['No', 'Kode Barang', 'Nama Barang', 'Alamat', 'Kelompok', 'Luas (m2)', 'Status']);

        $no = 1;
        foreach ($tanahList as $item) {
            fputcsv($output, [
                $no++,
                $item['kode_barang'] ?? '-',
                $item['nama_barang'] ?? '-',
                $item['alamat'] ?? '-',
                $item['kelompok'] ?? '-',
                number_format(floatval($item['luas_tanah_seluruhnya'] ?? 0), 2, ',', '.'),
                $item['status_penggunaan'] ?? '-',
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
        $totalData = $this->tanahModel->countAllResults();
        $apiData = $this->getApiData();
        $totalApi = count($apiData);
        
        // Statistik per kelompok
        $dbStats = $this->tanahModel->getStatistikKelompok();
        
        return view('user/tanah/stats', [
            'totalData' => $totalData,
            'totalApi' => $totalApi,
            'dbStats' => $dbStats
        ]);
    }
}