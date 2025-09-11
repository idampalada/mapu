<?php

namespace App\Controllers\User\Barang\AsetTetapLainnya;

use App\Controllers\BaseController;
use App\Models\IkanModel;

class Ikan extends BaseController
{
    protected $ikanModel;

    public function __construct()
    {
        $this->ikanModel = new IkanModel();
    }

    private function getApiData()
    {
        try {
            $apiUrl = 'http://apigw.pu.go.id/v1/siman/aset-tetap-lainnya?api_key=c877acaa0de297a9e3b8bbdb101dd254d33a92a0444b979d599e04fdeaccdbc5';
            
            $context = stream_context_create([
                'http' => [
                    'timeout' => 30,
                    'method' => 'GET',
                ]
            ]);

            $response = file_get_contents($apiUrl, false, $context);
            
            if ($response === false) {
                return [];
            }

            $data = json_decode($response, true);
            return is_array($data) ? $data : [];

        } catch (\Exception $e) {
            log_message('error', 'Exception in getApiData: ' . $e->getMessage());
            return [];
        }
    }

    private function safeFloat($value)
    {
        if (is_string($value)) {
            $value = str_replace([',', '.'], ['', '.'], $value);
        }
        return floatval($value);
    }

    public function kelompokIkan()
    {
        $searchTerm = $this->request->getGet('search');
        $activeKelompok = $this->request->getGet('kelompok', FILTER_SANITIZE_STRING);

        $allIkanList = $this->ikanModel->findAll();
        
        $validKelompok = [
            'IKAN BERSIRIP',
            'CRUSTEA',
            'MOLLUSCA',
            'COELENTERATA',
            'ECHINODERMATA',
            'AMPHIBIA',
            'REPTILIA',
            'MAMMALIA',
            'ALGAE',
            'BIOTA PERAIRAN LAINNYA'
        ];

        $ikanList = array_filter($allIkanList, function($item) use ($validKelompok) {
            return in_array(strtoupper($item['kelompok'] ?? ''), $validKelompok);
        });

        if (!empty($searchTerm)) {
            $ikanList = array_filter($ikanList, function($item) use ($searchTerm) {
                return stripos($item['nama_barang'] ?? '', $searchTerm) !== false ||
                       stripos($item['kode_barang'] ?? '', $searchTerm) !== false ||
                       stripos($item['merk'] ?? '', $searchTerm) !== false;
            });
        }

        if (!empty($activeKelompok)) {
            $ikanList = array_filter($ikanList, function($item) use ($activeKelompok) {
                return strtoupper($item['kelompok'] ?? '') === strtoupper($activeKelompok);
            });
        }

        $groupedData = [];
        foreach ($allIkanList as $item) {
            $kelompok = $item['kelompok'] ?? 'UNKNOWN';
            if (in_array(strtoupper($kelompok), $validKelompok)) {
                if (!isset($groupedData[$kelompok])) {
                    $groupedData[$kelompok] = [];
                }
                $groupedData[$kelompok][] = $item;
            }
        }

        $data = [
            'ikanList' => array_values($ikanList),
            'groupedData' => $groupedData,
            'totalCount' => count($ikanList),
            'searchTerm' => $searchTerm,
            'activeKelompok' => $activeKelompok
        ];

        return view('user/barang/asettetaplainnya/ikan/kelompokikan', $data);
    }

    public function kelompokDetail($kelompok)
    {
        $kelompok = urldecode($kelompok);
        $searchTerm = $this->request->getGet('search');
        return redirect()->to('user/barang/asettetaplainnya/ikan/kelompokikan?kelompok=' . urlencode($kelompok) . ($searchTerm ? '&search=' . urlencode($searchTerm) : ''));
    }

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

            $this->ikanModel->skipValidation(true);

            $validKelompok = [
                'IKAN BERSIRIP',
                'CRUSTEA',
                'MOLLUSCA',
                'COELENTERATA',
                'ECHINODERMATA',
                'AMPHIBIA',
                'REPTILIA',
                'MAMMALIA',
                'ALGAE',
                'BIOTA PERAIRAN LAINNYA'
            ];

            foreach ($apiData as $index => $item) {
                try {
                    $kode_barang = trim($item['kode_barang'] ?? '');
                    $kelompok_api = strtoupper(trim($item['kelompok'] ?? ''));
                    
                    if (empty($kode_barang)) {
                        $skipped++;
                        continue;
                    }

                    if (!in_array($kelompok_api, $validKelompok)) {
                        $filtered++;
                        continue;
                    }

                    $unique_kode = $kode_barang . '_' . $index;

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
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];

                    if ($this->ikanModel->insert($data)) {
                        $imported++;
                    } else {
                        $errors[] = $kode_barang;
                    }

                } catch (\Exception $e) {
                    $errors[] = ($kode_barang ?? 'unknown') . ': ' . $e->getMessage();
                }
            }

            $this->ikanModel->skipValidation(false);

            $total = count($apiData);
            $message = "Import selesai! Total API: {$total}, Berhasil: {$imported}, Dilewati: {$skipped}, Difilter: {$filtered}";
            
            if (!empty($errors)) {
                $message .= ", Error: " . count($errors);
            }

            session()->setFlashdata('success', $message);
            return redirect()->to('user/barang/asettetaplainnya/ikan/kelompokikan');

        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Gagal import data: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function resetData()
    {
        try {
            $this->ikanModel->builder()->truncate();
            session()->setFlashdata('success', 'Semua data berhasil dihapus!');
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
        return redirect()->to('user/barang/asettetaplainnya/ikan/kelompokikan');
    }

    public function exportIkanList($jenis = 'semua')
    {
        $allIkanList = $this->ikanModel->findAll();
        
        $filename = "ikan_{$jenis}_" . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        fputcsv($output, [
            'No', 'Kode Barang', 'Nama Barang', 'Kelompok', 'Sub Kelompok', 'Merk',
            'Kondisi', 'Kuantitas', 'Status Penggunaan', 'Nilai Perolehan', 'Nilai Buku',
            'Tanggal Perolehan', 'Satker'
        ]);

        $no = 1;
        foreach ($allIkanList as $item) {
            fputcsv($output, [
                $no++,
                $item['kode_barang'] ?? '-',
                $item['nama_barang'] ?? '-',
                $item['kelompok'] ?? '-',
                $item['sub_kelompok'] ?? '-',
                $item['merk'] ?? '-',
                $item['kondisi'] ?? '-',
                $item['kuantitas'] ?? '0',
                $item['status_penggunaan'] ?? '-',
                number_format(floatval($item['nilai_perolehan'] ?? 0), 2, ',', '.'),
                number_format(floatval($item['nilai_buku'] ?? 0), 2, ',', '.'),
                $item['tanggal_perolehan'] ?? '-',
                $item['nama_satker'] ?? '-'
            ]);
        }

        fclose($output);
        exit;
    }

    public function tambah()
    {
        if ($this->request->getMethod() === 'post') {
            $data = $this->request->getPost();

            try {
                $this->ikanModel->skipValidation(true);
                
                $dataToInsert = [
                    'kode_barang' => trim($data['kode_barang'] ?? ''),
                    'nama_barang' => trim($data['nama_barang'] ?? ''),
                    'kelompok' => trim($data['kelompok'] ?? ''),
                    'sub_kelompok' => trim($data['sub_kelompok'] ?? ''),
                    'merk' => trim($data['merk'] ?? ''),
                    'kondisi' => trim($data['kondisi'] ?? ''),
                    'kuantitas' => intval($data['kuantitas'] ?? 1),
                    'status_penggunaan' => trim($data['status_penggunaan'] ?? ''),
                    'nilai_perolehan' => $this->safeFloat($data['nilai_perolehan'] ?? 0),
                    'nilai_buku' => $this->safeFloat($data['nilai_buku'] ?? 0),
                    'tanggal_perolehan' => !empty($data['tanggal_perolehan']) ? $data['tanggal_perolehan'] : null,
                    'nama_satker' => trim($data['nama_satker'] ?? ''),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                if ($this->ikanModel->insert($dataToInsert)) {
                    session()->setFlashdata('success', 'Data berhasil ditambahkan');
                } else {
                    session()->setFlashdata('error', 'Gagal menyimpan data');
                }
                
                $this->ikanModel->skipValidation(false);
                
            } catch (\Exception $e) {
                session()->setFlashdata('error', 'Error database: ' . $e->getMessage());
                $this->ikanModel->skipValidation(false);
            }
        }
        
        return redirect()->to('user/barang/asettetaplainnya/ikan/kelompokikan');
    }
}