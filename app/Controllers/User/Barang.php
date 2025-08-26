<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\BarangModel;
use App\Models\PinjamBarangModel;
use Myth\Auth\Models\UserModel;
use Myth\Auth\Entities\User;

class Barang extends BaseController
{
    public function index()
    {
        $kategoriBarang = [
            ['kode' => 'komputerjaringan', 'nama' => 'Komputer Jaringan', 'gambar' => 'komputerjaringan.jpeg'],
            ['kode' => 'personalkomputer', 'nama' => 'Personal Komputer', 'gambar' => 'gadget.jpeg'],
            ['kode' => 'komputerunit', 'nama' => 'Komputer Unit Lainnya', 'gambar' => 'pc.jpeg'],
            // ['kode' => 'peralatanmainframe', 'nama' => 'Peralatan Mainframe', 'gambar' => 'mainframe.jpeg'],
        ];

        return view('user/barang/index', ['kategoriBarang' => $kategoriBarang]);
    }

    public function detail($kategori)
{
    if (!logged_in()) {
        return redirect()->to('/login')->with('error', 'Silakan login untuk mengakses halaman ini');
    }

    $kategoriMap = [
        'komputerjaringan' => 'Komputer Jaringan',
        'personalkomputer' => 'Personal Komputer',
        'komputerunit' => 'Komputer Unit',
        'peralatanmainframe' => 'Peralatan Mainframe',
    ];

    $kategoriLabel = $kategoriMap[$kategori] ?? null;
    if (!$kategoriLabel) {
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }

    $barangModel = new BarangModel();
    $barang = $barangModel->where('kategori', $kategoriLabel)->findAll();

    // Tambahan ini supaya tahu id peminjaman!
    $pinjamModel = new PinjamBarangModel();
    // Di metode detail()
foreach ($barang as &$item) {
    $peminjaman = $pinjamModel
        ->where('barang_id', $item['id'])
        ->whereIn('status', ['diajukan', 'disetujui', 'dipinjam', 'proses_pengembalian', 'ditolak'])
        ->orderBy('created_at', 'DESC')
        ->first();
    
    if ($peminjaman) {
        $item['pinjam_id'] = $peminjaman['id'];
        $item['pinjam_status'] = $peminjaman['status'];
        // Tambahkan logging atau debug di sini
        log_message('debug', 'Barang: ' . $item['id'] . ', Status: ' . $item['status'] . ', Pinjam ID: ' . $item['pinjam_id'] . ', Pinjam Status: ' . $item['pinjam_status']);
    } else {
        $item['pinjam_id'] = null;
        $item['pinjam_status'] = null;
    }
}
    unset($item);

    return view('user/barang/detail', [
        'barang' => $barang,
        'kategori' => $kategori,
        'kategoriLabel' => $kategoriLabel,
    ]);
}

    public function tambah()
    {
        $barangModel = new BarangModel();

        $data = [
            'nama_barang' => $this->request->getPost('nama_barang'),
            'kategori'    => $this->request->getPost('kategori'),
            'kondisi'     => $this->request->getPost('kondisi'),
            'lokasi'      => $this->request->getPost('lokasi'),
            'status'      => 'Tersedia',
            'kode_barang' => $this->request->getPost('kode_barang'),
        ];

        $gambar = $this->request->getFile('gambar');
        if ($gambar && $gambar->isValid() && !$gambar->hasMoved()) {
            $namaGambar = $gambar->getRandomName();
            $gambar->move('uploads/barang', $namaGambar);
            $data['gambar'] = $namaGambar;
        }

        if (!$barangModel->insert($data)) {
            return redirect()->back()->withInput()->with('errors', $barangModel->errors())->with('error', 'Gagal menambahkan barang.');
        }

        return redirect()->back()->with('success', 'Barang berhasil ditambahkan.');
    }

    public function pinjam()
    {
        try {
            $userId = user_id();
            $barangId = $this->request->getPost('barang_id');

            $barangModel = new BarangModel();
            $barang = $barangModel->find($barangId);
            if (!$barang) {
                throw new \Exception('Barang tidak ditemukan');
            }

            $data = [
                'barang_id' => $barangId,
                'user_id' => $userId,
                'nama_peminjam' => $this->request->getPost('nama_peminjam'),
                'tanggal' => $this->request->getPost('tanggal'),
                'waktu_mulai' => $this->request->getPost('waktu_mulai'),
                'waktu_selesai' => $this->request->getPost('waktu_selesai'),
                'keperluan' => $this->request->getPost('keperluan'),
                'status' => 'diajukan'
            ];

            $db = \Config\Database::connect();
            $db->transStart();

            $pinjamModel = new PinjamBarangModel();
            if (!$pinjamModel->insert($data)) {
                throw new \Exception('Gagal menyimpan data peminjaman');
            }
            $emailData = [
                'user_email' => user()->email,
                'user_fullname' => user()->fullname,
                'nama_barang' => $barang['nama_barang'],
                'tanggal' => $data['tanggal'],
                'waktu_mulai' => $data['waktu_mulai'],
                'waktu_selesai' => $data['waktu_selesai'],
                'keperluan' => $data['keperluan'],
                'surat_permohonan' => $data['surat_permohonan'] ?? null
            ];
            
            helper('email');
            sendBarangPeminjamanNotification($emailData, 'new');

            $barangModel->update($barangId, [
                'status' => 'Menunggu Verifikasi',
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            $db->transComplete();
            return $this->response->setJSON(['success' => true, 'message' => 'Peminjaman berhasil diajukan']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function verifikasiPeminjaman()
    {
        try {
            $pinjam_id = $this->request->getPost('pinjam_id');
            $status = $this->request->getPost('status');
            $keterangan = $this->request->getPost('keterangan');

            if (!$pinjam_id || !$status) {
                throw new \Exception('Data verifikasi tidak lengkap');
            }

            $pinjamModel = new PinjamBarangModel();
            $barangModel = new BarangModel();
            $pinjamData = $pinjamModel->find($pinjam_id);

            if (!$pinjamData) {
                throw new \Exception('Data peminjaman tidak ditemukan');
            }

            $barang = $barangModel->find($pinjamData['barang_id']);
            if (!$barang) {
                throw new \Exception('Barang tidak ditemukan');
            }
            $userModel = new \Myth\Auth\Models\UserModel();
$peminjam = $userModel->find($pinjamData['user_id']);

$emailData = [
    'user_email' => $peminjam->email,
    'user_fullname' => $peminjam->fullname,
    'nama_barang' => $barang['nama_barang'],
    'tanggal' => $pinjamData['tanggal'],
    'waktu_mulai' => $pinjamData['waktu_mulai'],
    'waktu_selesai' => $pinjamData['waktu_selesai'],
    'keperluan' => $pinjamData['keperluan'],
    'status' => $status,
    'keterangan' => $keterangan,
'dokumen_tambahan' => $this->request->getFile('dokumen_tambahan') && $this->request->getFile('dokumen_tambahan')->isValid()
    ? $this->request->getFile('dokumen_tambahan')->getName()
    : null
];

helper('email');
sendBarangPeminjamanNotification($emailData, 'verified');


            $db = \Config\Database::connect();
            $db->transStart();

            $updatePinjam = [
                'id' => $pinjam_id,
                'status' => $status,
                'keterangan_status' => $keterangan,
                'verified_by' => user_id(),
                'verified_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $pinjamModel->save($updatePinjam);

            if ($status === 'disetujui') {
                $barangModel->update($barang['id'], ['status' => 'Dipinjam']);
            } elseif ($status === 'selesai' || $status === 'ditolak') {
                $barangModel->update($barang['id'], ['status' => 'Tersedia']);
            }

            $db->transComplete();
            return $this->response->setJSON(['success' => true, 'message' => 'Verifikasi berhasil']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'error' => $e->getMessage()]);
        }
    }
    public function kembalikanById()
{
    try {
        $request = $this->request->getJSON(true);
        $barangId = $request['barang_id'] ?? null;
        $userId = user_id();

        if (!$barangId || !$userId) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'ID barang atau User ID tidak ditemukan.'
            ]);
        }

        $pinjamModel = new \App\Models\PinjamBarangModel();

        // Cari peminjaman aktif terbaru
        $data = $pinjamModel->where('barang_id', $barangId)
                            ->where('user_id', $userId)
                            ->whereIn('status', ['diajukan', 'disetujui', 'dipinjam'])
                            ->orderBy('created_at', 'DESC')
                            ->first();

        // Atau cari peminjaman yang ditolak pengembaliannya
        if (!$data) {
            $data = $pinjamModel->where('barang_id', $barangId)
                                ->where('user_id', $userId)
                                ->where('status', 'ditolak')
                                ->orderBy('created_at', 'DESC')
                                ->first();
        }

        if (!$data) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Data peminjaman tidak ditemukan.'
            ]);
        }

        // Reset status jika sebelumnya ditolak
        if ($data['status'] === 'ditolak') {
            $pinjamModel->update($data['id'], [
                'status' => 'dipinjam',
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        $tanggalKembali = date('Y-m-d H:i:s');
        $update = $pinjamModel->update($data['id'], [
            'status' => 'proses_pengembalian',
            'tanggal_kembali' => $tanggalKembali
        ]);

        if ($update) {
            // === Kirim notifikasi email ===
            $barangModel = new \App\Models\BarangModel();
            $barang = $barangModel->find($barangId);
            $user = user(); // Authenticated user

            $emailData = [
                'user_email' => $user->email,
                'user_fullname' => $user->fullname,
                'nama_barang' => $barang['nama_barang'] ?? 'Barang Tidak Dikenal',
                'tanggal_kembali' => $tanggalKembali,
                'status' => 'proses_pengembalian',
                'tanggal' => $data['tanggal'] ?? null,
                'waktu_selesai' => $data['waktu_selesai'] ?? null,
                'keperluan' => $data['keperluan'] ?? null
            ];

            helper('email');
            sendBarangPengembalianNotification($emailData, 'new');

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Pengajuan pengembalian berhasil.'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Gagal mengupdate status.'
            ]);
        }
    } catch (\Exception $e) {
        return $this->response->setJSON([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
}
public function verifikasiPengembalian()
{
    try {
        $id = $this->request->getPost('id');
        $status = $this->request->getPost('status');

        if (!$id || !$status) {
            throw new \Exception("Data verifikasi tidak lengkap.");
        }

        $model = new \App\Models\PinjamBarangModel();
        $data = $model->find($id);

        if (!$data) {
            throw new \Exception("Data peminjaman tidak ditemukan.");
        }

        $barangModel = new \App\Models\BarangModel();
        $barang = $barangModel->find($data['barang_id']);

        if (!$barang) {
            throw new \Exception("Data barang tidak ditemukan.");
        }

        $verifiedAt = date('Y-m-d H:i:s');

        // Update status pinjam dan barang
        if ($status === 'disetujui') {
            $model->update($id, [
                'status' => 'selesai',
                'verified_by' => user_id(),
                'verified_at' => $verifiedAt
            ]);
            $barangModel->update($data['barang_id'], ['status' => 'Tersedia']);
        } else {
            $model->update($id, [
                'status' => 'ditolak',
                'verified_by' => user_id(),
                'verified_at' => $verifiedAt
            ]);
            $barangModel->update($data['barang_id'], ['status' => 'Dipinjam']);
        }

        // Ambil data user
        $userModel = new \Myth\Auth\Models\UserModel();
        $user = $userModel->find($data['user_id']);
        if (!$user) {
            throw new \Exception("User tidak ditemukan.");
        }

        // Kirim notifikasi email
        $emailData = [
            'user_email' => $user->email,
            'user_fullname' => $user->fullname,
            'nama_barang' => $barang['nama_barang'],
            'status' => $status,
            'tanggal' => $data['tanggal'] ?? null,
            'waktu_selesai' => $data['waktu_selesai'] ?? null,
            'keterangan' => $data['keterangan_status'] ?? null,
            'verified_at' => $verifiedAt
        ];

        helper('email');
        sendBarangPengembalianNotification($emailData, 'verified');

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Verifikasi pengembalian berhasil.'
        ]);

    } catch (\Exception $e) {
        return $this->response->setJSON([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
}
public function dashboard()
{
    // Ambil data tanah dari API
    $client = \Config\Services::curlrequest();
    $response = $client->get('http://apigw.pu.go.id/v1/siman/tanah?api_key=c877acaa0de297a9e3b8bbdb101dd254d33a92a0444b979d599e04fdeaccdbc5');
    $tanahList = [];

    if ($response->getStatusCode() == 200) {
        $result = json_decode($response->getBody(), true);
        $tanahList = $result['resource'] ?? [];
    }

    return view('user/barang/dashboardbarang', [
        'tanahList' => $tanahList
    ]);
}
// public function tanah()
// {
//     $client = \Config\Services::curlrequest();
//     $tanahList = [];

//     try {
//         $url = 'https://apigw.pu.go.id/v1/siman/tanah?api_key=c877acaa0de297a9e3b8bbdb101dd254d33a92a0444b979d599e04fdeaccdbc5';

//         $response = $client->get($url, [
//             'allow_redirects' => true  
//         ]);

//         $status = $response->getStatusCode();
//         $body = $response->getBody();

//         if ($status === 200) {
//             $data = json_decode($body, true);
//             $tanahList = $data['resource'] ?? [];
//         } else {
//             echo "Gagal ambil data. Status: " . $status;
//             exit;
//         }
//     } catch (\Exception $e) {
//         echo "Exception: " . $e->getMessage();
//         exit;
//     }

//     return view('user/barang/tanah', ['tanahList' => $tanahList]);
// }
public function kelompokTanah()
{
    $sort = $this->request->getGet('sort') ?? 'kode_barang';  // Mendapatkan nilai sort
    $order = $this->request->getGet('order') ?? 'asc';  // Mendapatkan nilai order
    
    // Logika untuk mengambil data dari API atau database
    // Misalnya, ambil data persil, non persil, dan lapangan
    $client = \Config\Services::curlrequest();
    $persilData = [];
    $nonPersilData = [];
    $lapanganData = [];

    try {
        $url = "https://apigw.pu.go.id/v1/siman/tanah?api_key=c877acaa0de297a9e3b8bbdb101dd254d33a92a0444b979d599e04fdeaccdbc5";
        $response = $client->get($url);

        if ($response->getStatusCode() === 200) {
            $result = json_decode($response->getBody(), true);
            $allTanahList = $result['resource'] ?? [];

            // Filter data berdasarkan kelompok (persil, non persil, lapangan)
            $persilData = array_filter($allTanahList, function ($item) {
                return strtolower($item['kelompok'] ?? '') === 'tanah persil';
            });

            $nonPersilData = array_filter($allTanahList, function ($item) {
                return strtolower($item['kelompok'] ?? '') === 'tanah non persil';
            });

            $lapanganData = array_filter($allTanahList, function ($item) {
                return strtolower($item['kelompok'] ?? '') === 'lapangan';
            });

            // Reset array keys setelah filter
            $persilData = array_values($persilData);
            $nonPersilData = array_values($nonPersilData);
            $lapanganData = array_values($lapanganData);
            
        } else {
            log_message('error', 'API error: ' . $response->getStatusCode());
        }
    } catch (\Exception $e) {
        log_message('error', 'API error: ' . $e->getMessage());
    }

    // Gabungkan ketiga data ini
    $allData = array_merge($persilData, $nonPersilData, $lapanganData);

    return view('user/barang/kelompoktanah', [
        'sort' => $sort,
        'order' => $order,
        'allData' => $allData,  // Kirimkan semua data
    ]);
}

public function kelompokDetail($kelompok)
{
    $client = \Config\Services::curlrequest();
    $tanahList = [];

    // Mengambil parameter pencarian, pengurutan, dan halaman
    $searchTerm = $this->request->getGet('search') ?? '';
    $sort = $this->request->getGet('sort') ?? 'kode_barang'; // Default sort by kode_barang
    $order = $this->request->getGet('order') ?? 'asc'; // Default ascending order
    $perPage = 100; // Jumlah data per halaman (diubah menjadi 100)
    $page = $this->request->getGet('page') ?? 1; // Halaman saat ini

    try {
        $apiKey = 'c877acaa0de297a9e3b8bbdb101dd254d33a92a0444b979d599e04fdeaccdbc5';
        $encodedKelompok = urlencode(strtoupper($kelompok)); // encode dan kapitalisasi

        // Ambil data tanah dari API
        $url = "https://apigw.pu.go.id/v1/siman/tanah?api_key=$apiKey";
        $response = $client->get($url);

        if ($response->getStatusCode() === 200) {
            $result = json_decode($response->getBody(), true);
            $allTanahList = $result['resource'] ?? [];

            // Memfilter data tanah berdasarkan pencarian
            $filteredTanahList = array_filter($allTanahList, function ($item) use ($searchTerm) {
                return empty($searchTerm) ||
                    stripos($item['nama_barang'], $searchTerm) !== false ||
                    stripos($item['kode_barang'], $searchTerm) !== false ||
                    stripos($item['alamat'], $searchTerm) !== false;
            });

            // Memisahkan data tanah berdasarkan kelompok
            foreach ($filteredTanahList as $item) {
                if (isset($item['kelompok'])) {
                    // Cek apakah kelompok sesuai dengan yang diminta
                    if (strtoupper($item['kelompok']) === strtoupper($kelompok)) {
                        $tanahList[] = $item; // Tambahkan item ke tanahList jika kelompok sesuai
                    }
                }
            }

            // Hitung total items untuk pagination
            $totalItems = count($tanahList); // Menghitung jumlah data yang difilter dan dikelompokkan

            // Pengurutan data berdasarkan parameter sort dan order
            if (!empty($sort)) {
                usort($tanahList, function($a, $b) use ($sort, $order) {
                    $valueA = $a[$sort] ?? '';
                    $valueB = $b[$sort] ?? '';
                    
                    if ($order === 'asc') {
                        return strcmp($valueA, $valueB);
                    } else {
                        return strcmp($valueB, $valueA);
                    }
                });
            }

            // Konfigurasi pagination
            $pager = service('pager');
            $pager->setPath('user/barang/kelompoktanah/' . urlencode($kelompok));
            $pager->makeLinks($page, $perPage, $totalItems);

            // Slice data tanah sesuai dengan halaman yang diminta
            $offset = ($page - 1) * $perPage;
            $tanahListForView = array_slice($tanahList, $offset, $perPage);
        } else {
            log_message('error', 'API error: ' . $response->getStatusCode());
        }
    } catch (\Exception $e) {
        log_message('error', 'API error: ' . $e->getMessage());
    }

    // Kirimkan data yang dibutuhkan ke view
    return view('user/barang/kelompoktanah', [
        'tanahList' => $tanahListForView,
        'kelompok' => strtoupper($kelompok),
        'activeKelompok' => strtoupper($kelompok),
        'pager' => $pager,
        'searchTerm' => $searchTerm,
        'currentPage' => $page,
        'totalPages' => ceil($totalItems / $perPage),
        'totalItems' => $totalItems, 
        'sort' => $sort, 
        'order' => $order  
    ]);
}


public function exportTanahList($jenis = 'semua')
{
    // Validasi jenis data yang diminta
    $jenisValid = ['persil', 'nonpersil', 'lapangan', 'semua'];
    if (!in_array($jenis, $jenisValid)) {
        $jenis = 'semua'; // Default jika parameter tidak valid
    }

    $client = \Config\Services::curlrequest();
    $tanahList = [];

    try {
        $url = "https://apigw.pu.go.id/v1/siman/tanah?api_key=c877acaa0de297a9e3b8bbdb101dd254d33a92a0444b979d599e04fdeaccdbc5";
        $response = $client->get($url);

        if ($response->getStatusCode() === 200) {
            $result = json_decode($response->getBody(), true);
            $allTanahList = $result['resource'] ?? [];
            
            // Filter data berdasarkan jenis yang diminta
            if ($jenis !== 'semua') {
                $tanahList = array_filter($allTanahList, function($item) use ($jenis) {
                    $kelompok = strtolower($item['kelompok'] ?? '');
                    
                    switch ($jenis) {
                        case 'persil':
                            return strpos($kelompok, 'persil') !== false;
                        case 'nonpersil':
                            return strpos($kelompok, 'non persil') !== false;
                        case 'lapangan':
                            return strpos($kelompok, 'lapangan') !== false;
                        default:
                            return true;
                    }
                });
                
                // Reset array keys setelah filter
                $tanahList = array_values($tanahList);
            } else {
                $tanahList = $allTanahList;
            }

            // Nama file berdasarkan jenis
            $jenisText = ucfirst($jenis);
            $filename = 'tanah_' . strtolower($jenisText) . '_' . date('Y-m-d') . '.csv';
            
            // Buat response object baru
            $response = service('response');
            $response->setHeader('Content-Type', 'text/csv');
            $response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
            $response->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            $response->setHeader('Pragma', 'no-cache');
            $response->setHeader('Expires', '0');

            // Buka file output untuk menulis
            $output = fopen('php://output', 'w');

            // Tulis header CSV
            fputcsv($output, ['No', 'Kode Barang', 'Nama Barang', 'Alamat', 'Kelompok', 'Luas (m2)', 'Status']);

            // Tulis data tanah ke CSV
            $no = 1;
            foreach ($tanahList as $item) {
                // Menyiapkan alamat lengkap seperti pada tampilan tabel
                $alamatParts = [];
                if (!empty($item['alamat'])) $alamatParts[] = $item['alamat'];
                if (!empty($item['rt_rw'])) $alamatParts[] = 'RT/RW ' . $item['rt_rw'];
                if (!empty($item['kelurahan_desa'])) $alamatParts[] = 'Kelurahan ' . $item['kelurahan_desa'];
                if (!empty($item['kecamatan'])) $alamatParts[] = 'Kecamatan ' . $item['kecamatan'];
                if (!empty($item['uraian_provinsi'])) $alamatParts[] = 'Provinsi ' . $item['uraian_provinsi'];
                if (!empty($item['kode_pos'])) $alamatParts[] = 'Kode Pos ' . $item['kode_pos'];
                $alamat = implode(', ', $alamatParts) ?: '-';
                
                fputcsv($output, [
                    $no++,
                    $item['kode_barang'] ?? '-',
                    $item['nama_barang'] ?? '-',
                    $alamat,
                    $item['kelompok'] ?? '-',
                    number_format(floatval($item['luas_tanah_seluruhnya'] ?? 0), 2, ',', '.'),
                    $item['status_penggunaan'] ?? '-',
                ]);
            }

            fclose($output);
            
            // Kembalikan response yang sudah dimodifikasi
            return $response;
        } else {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Gagal mengekspor data tanah'
            ]);
        }
    } catch (\Exception $e) {
        return $this->response->setJSON([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
}
public function tambahTanah()
{
    if ($this->request->getMethod() === 'post') {
        $data = [
            'kode_barang' => $this->request->getPost('kode_barang'),
            'nama_barang' => $this->request->getPost('nama_barang'),
            'alamat' => $this->request->getPost('alamat'),
            'kelompok' => $this->request->getPost('kelompok'),
            'luas_tanah_seluruhnya' => $this->request->getPost('luas_tanah_seluruhnya'),
            'status_penggunaan' => $this->request->getPost('status_penggunaan'),
        ];

        // Simpan ke database (gunakan model atau simpan ke file json sesuai kebutuhan kamu)
        // Misal disimpan ke database lokal, siapkan model bernama TanahModel
        $model = new \App\Models\TanahModel();
        if ($model->insert($data)) {
            return redirect()->back()->with('success', 'Data tanah berhasil ditambahkan.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan data tanah.')->with('errors', $model->errors());
        }
    }

    return view('user/barang/tambah_tanah'); // form input tanah
}

}