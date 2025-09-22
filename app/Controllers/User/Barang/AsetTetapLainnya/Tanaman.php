<?php

namespace App\Controllers\User\Barang\AsetTetapLainnya;

use App\Controllers\BaseController;
use App\Models\TanamanModel;

class Tanaman extends BaseController
{
    protected $tanamanModel;
    
    public function __construct()
    {
        $this->tanamanModel = new TanamanModel();
    }

    // Method untuk mengambil data dari API
    private function getApiData($url = null)
    {
        $client = \Config\Services::curlrequest();
        $apiKey = 'c877acaa0de297a9e3b8bbdb101dd254d33a92a0444b979d599e04fdeaccdbc5';
        
        if (!$url) {
            $url = "https://apigw.pu.go.id/v1/siman/aset-tetap-lainnya?api_key={$apiKey}";
        }
        
        log_message('info', 'Calling API: ' . $url);
        
        try {
            $response = $client->get($url, [
                'timeout' => 30,
                'connect_timeout' => 10,
                'verify' => false,
                'allow_redirects' => true,
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
                    // Cek struktur response
                    $data = $result;
                    if (isset($result['resource'])) {
                        $data = $result['resource'];
                    } elseif (isset($result['data'])) {
                        $data = $result['data'];
                    }
                    
                    if (!is_array($data)) {
                        log_message('error', 'API response data is not array');
                        return [];
                    }
                    
                    log_message('info', 'API JSON Parse Success. Records: ' . count($data));
                    
                    // Filter hanya data tanaman
                    $filtered = array_filter($data, function($item) {
                        return strtoupper($item['kelompok'] ?? '') === 'TANAMAN';
                    });
                    
                    log_message('info', 'Filtered Tanaman Records: ' . count($filtered));
                    
                    return array_values($filtered);
                } else {
                    log_message('error', 'JSON decode error: ' . json_last_error_msg());
                }
            } else {
                log_message('error', 'API HTTP Error: ' . $response->getStatusCode());
            }
        } catch (\Exception $e) {
            log_message('error', 'API Exception: ' . $e->getMessage());
        }
        
        return [];
    }

    public function dashboard()
    {
        $tanamanList = $this->getApiData();
        return view('user/barang/asettetaplainnya/tanaman/dashboard', [
            'tanamanList' => $tanamanList
        ]);
    }

    public function kelompokTanaman()
    {
        $searchTerm = $this->request->getGet('search') ?? '';
        $sort = $this->request->getGet('sort') ?? 'kode_barang';
        $order = $this->request->getGet('order') ?? 'asc';

        // Menggunakan data dari database
        $allTanamanList = $this->tanamanModel->findAll();
        
        // Filter hanya data tanaman
        $tanamanList = array_filter($allTanamanList, function($item) {
            return strtoupper($item['kelompok'] ?? '') === 'TANAMAN';
        });

        // Filter berdasarkan pencarian
        if (!empty($searchTerm)) {
            $tanamanList = array_filter($tanamanList, function($item) use ($searchTerm) {
                return stripos($item['nama_barang'] ?? '', $searchTerm) !== false ||
                       stripos($item['kode_barang'] ?? '', $searchTerm) !== false ||
                       stripos($item['sub_kelompok'] ?? '', $searchTerm) !== false ||
                       stripos($item['jenis_tanaman'] ?? '', $searchTerm) !== false ||
                       stripos($item['lokasi_tanam'] ?? '', $searchTerm) !== false;
            });
        }

        return view('user/barang/asettetaplainnya/tanaman/kelompoktanaman', [
            'tanamanList' => array_values($tanamanList),
            'totalCount' => count($tanamanList),
            'searchTerm' => $searchTerm,
            'sort' => $sort,
            'order' => $order
        ]);
    }

    // Hapus method kelompokDetail karena tidak diperlukan untuk single category

    // Method tambah tanaman
    public function tambah()
    {
        log_message('info', '=== TAMBAH TANAMAN METHOD DIPANGGIL ===');
        
        $method = $_SERVER['REQUEST_METHOD'] ?? 'unknown';
        $postData = $this->request->getPost();
        $postRaw = $_POST;
        
        $isPost = (strtoupper($method) === 'POST') || !empty($postData) || !empty($postRaw);
        
        if ($isPost && (!empty($postData) || !empty($postRaw))) {
            $data_source = !empty($postData) ? $postData : $postRaw;
            
            $data = [
                'kode_barang' => trim($data_source['kode_barang'] ?? ''),
                'nama_barang' => trim($data_source['nama_barang'] ?? ''),
                'nup' => trim($data_source['nup'] ?? ''),
                'merk' => trim($data_source['merk'] ?? ''),
                'kelompok' => 'TANAMAN', // Selalu TANAMAN
                'sub_kelompok' => trim($data_source['sub_kelompok'] ?? ''),
                'kondisi' => trim($data_source['kondisi'] ?? ''),
                'kuantitas' => intval($data_source['kuantitas'] ?? 1),
                'status_penggunaan' => trim($data_source['status_penggunaan'] ?? ''),
                'lokasi_tanam' => trim($data_source['lokasi_tanam'] ?? ''),
                'umur_tanaman' => trim($data_source['umur_tanaman'] ?? ''),
                'jenis_tanaman' => trim($data_source['jenis_tanaman'] ?? ''),
                'nilai_perolehan' => $this->safeFloat($data_source['nilai_perolehan'] ?? 0),
                'tanggal_perolehan' => !empty($data_source['tanggal_perolehan']) ? $data_source['tanggal_perolehan'] : null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Validasi
            $errors = [];
            if (empty($data['kode_barang'])) $errors[] = 'Kode barang harus diisi';
            if (empty($data['nama_barang'])) $errors[] = 'Nama barang harus diisi';

            if (!empty($errors)) {
                session()->setFlashdata('error', 'Error: ' . implode(', ', $errors));
                return redirect()->back()->withInput();
            }

            try {
                $this->tanamanModel->skipValidation(true);
                $insertResult = $this->tanamanModel->insert($data);
                
                if ($insertResult) {
                    $insertId = $this->tanamanModel->getInsertID();
                    session()->setFlashdata('success', "Data tanaman berhasil disimpan! ID: {$insertId}");
                } else {
                    $errors = $this->tanamanModel->errors();
                    session()->setFlashdata('error', 'Gagal menyimpan data: ' . implode(', ', $errors));
                }
                
                $this->tanamanModel->skipValidation(false);
                
            } catch (\Exception $e) {
                session()->setFlashdata('error', 'Error database: ' . $e->getMessage());
                $this->tanamanModel->skipValidation(false);
            }
        }
        
        return redirect()->to('user/barang/asettetaplainnya/tanaman/kelompoktanaman');
    }

    // Method reset data
    public function resetData()
    {
        try {
            $this->tanamanModel->builder()->truncate();
            session()->setFlashdata('success', 'Semua data berhasil dihapus!');
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
        return redirect()->to('user/barang/asettetaplainnya/tanaman/kelompoktanaman');
    }

    // Method import dari API
    public function importFromApi()
    {
        $imported = 0;
        $skipped = 0;
        $filtered = 0;
        $errors = [];

        try {
            $apiData = $this->getApiData();

            if (empty($apiData)) {
                session()->setFlashdata('error', 'Tidak ada data dari API atau API tidak dapat diakses!');
                return redirect()->back();
            }

            $this->tanamanModel->skipValidation(true);

            foreach ($apiData as $index => $item) {
                try {
                    $kode_barang = trim($item['kode_barang'] ?? '');
                    $kelompok_api = strtoupper(trim($item['kelompok'] ?? ''));
                    
                    if (empty($kode_barang)) {
                        $skipped++;
                        continue;
                    }

                    if ($kelompok_api !== 'TANAMAN') {
                        $filtered++;
                        continue;
                    }

                    $unique_kode = $kode_barang . '_' . $index;

                    $data = [
                        'kode_barang' => $unique_kode,
                        'nama_barang' => trim($item['nama_barang'] ?? '') ?: 'Unknown',
                        'nup' => trim($item['nup'] ?? ''),
                        'merk' => trim($item['merk'] ?? ''),
                        'kelompok' => 'TANAMAN',
                        'sub_kelompok' => trim($item['sub_kelompok'] ?? ''),
                        'kondisi' => trim($item['kondisi'] ?? ''),
                        'kuantitas' => intval($item['kuantitas'] ?? 1),
                        'status_penggunaan' => trim($item['status_penggunaan'] ?? ''),
                        'lokasi_tanam' => trim($item['lokasi_tanam'] ?? ''),
                        'umur_tanaman' => trim($item['umur_tanaman'] ?? ''),
                        'jenis_tanaman' => trim($item['jenis_tanaman'] ?? ''),
                        'nilai_perolehan' => $this->safeFloat($item['nilai_perolehan'] ?? 0),
                        'nilai_penyusutan' => $this->safeFloat($item['nilai_penyusutan'] ?? 0),
                        'nilai_buku' => $this->safeFloat($item['nilai_buku'] ?? 0),
                        'tanggal_perolehan' => !empty($item['tanggal_perolehan']) ? $item['tanggal_perolehan'] : null,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];

                    if ($this->tanamanModel->insert($data)) {
                        $imported++;
                    } else {
                        $errors[] = $kode_barang;
                    }

                } catch (\Exception $e) {
                    $errors[] = ($kode_barang ?? 'unknown') . ': ' . $e->getMessage();
                }
            }

            $this->tanamanModel->skipValidation(false);

            $total = count($apiData);
            $message = "Import selesai! Total API: {$total}, Berhasil: {$imported}, Dilewati: {$skipped}, Difilter (bukan tanaman): {$filtered}";
            
            if (!empty($errors)) {
                $message .= ", Error: " . count($errors);
            }

            session()->setFlashdata('success', $message);
            return redirect()->to('user/barang/asettetaplainnya/tanaman/kelompoktanaman');

        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Gagal import data: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    // Method export
    public function exportTanamanList($jenis = 'semua')
    {
        $jenisValid = ['pohon_hias', 'tanaman_buah', 'tanaman_sayuran', 'tanaman_obat', 'semua'];
        if (!in_array($jenis, $jenisValid)) {
            $jenis = 'semua';
        }

        $allTanamanList = $this->tanamanModel->where('kelompok', 'TANAMAN')->findAll();
        
        if ($jenis !== 'semua') {
            $tanamanList = array_filter($allTanamanList, function($item) use ($jenis) {
                $subKelompok = strtolower($item['sub_kelompok'] ?? '');
                
                switch ($jenis) {
                    case 'pohon_hias':
                        return strpos($subKelompok, 'pohon hias') !== false;
                    case 'tanaman_buah':
                        return strpos($subKelompok, 'tanaman buah') !== false;
                    case 'tanaman_sayuran':
                        return strpos($subKelompok, 'tanaman sayuran') !== false;
                    case 'tanaman_obat':
                        return strpos($subKelompok, 'tanaman obat') !== false;
                    default:
                        return true;
                }
            });
            $tanamanList = array_values($tanamanList);
        } else {
            $tanamanList = $allTanamanList;
        }

        $filename = 'tanaman_' . $jenis . '_' . date('Y-m-d') . '.csv';
        
        $response = service('response');
        $response->setHeader('Content-Type', 'text/csv');
        $response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        fputcsv($output, [
            'No', 'Kode Barang', 'Nama Barang', 'NUP', 'Sub Kelompok', 'Jenis Tanaman',
            'Kondisi', 'Kuantitas', 'Lokasi Tanam', 'Umur Tanaman', 'Nilai Perolehan', 'Tanggal Perolehan'
        ]);

        $no = 1;
        foreach ($tanamanList as $item) {
            fputcsv($output, [
                $no++,
                $item['kode_barang'] ?? '-',
                $item['nama_barang'] ?? '-',
                $item['nup'] ?? '-',
                $item['sub_kelompok'] ?? '-',
                $item['jenis_tanaman'] ?? '-',
                $item['kondisi'] ?? '-',
                $item['kuantitas'] ?? '1',
                $item['lokasi_tanam'] ?? '-',
                $item['umur_tanaman'] ?? '-',
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
}