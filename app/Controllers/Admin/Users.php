<?php

namespace App\Controllers\Admin;

use Myth\Auth\Models\UserModel;
use App\Models\PinjamModel;
use App\Models\KembaliModel;
use Myth\Auth\Models\LoginModel;
use CodeIgniter\Controller;
use App\Models\BarangModel;
use App\Models\PinjamBarangModel;
use App\Models\RuanganModel;
use App\Models\PinjamRuanganModel;


class Users extends Controller
{
    protected $userModel;
    protected $pinjamModel;
    protected $kembaliModel;
    protected $loginModel;
    protected $barangModel;
    protected $pinjamBarangModel;
    protected $pinjamRuanganModel;
    protected $ruanganModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->pinjamModel = new PinjamModel();
        $this->kembaliModel = new KembaliModel();
        $this->loginModel = new LoginModel();
        $this->pinjamModel = new \App\Models\PinjamModel();
        $this->barangModel = new \App\Models\BarangModel();
        $this->pinjamBarangModel = new \App\Models\PinjamBarangModel();
        $this->pinjamRuanganModel = new \App\Models\PinjamRuanganModel();
        $this->ruanganModel = new \App\Models\RuanganModel();

    }


public function index()
{
    if (!in_groups('admin')) {
        log_message('error', 'Akses Ditolak: User tidak memiliki izin');
        return redirect()->back()->with('error', 'Akses ditolak');
    }

    $users = $this->userModel->findAll();
    foreach ($users as $user) {
        $user->role = $this->getCurrentRole($user->id);
    }

    $data = [
        'title' => 'Daftar Pengguna',
        'users' => $users
    ];
    log_message('debug', 'Daftar pengguna berhasil di-load');
    return view('admin/daftar-pengguna', $data);
}

    public function changeRole()
    {
        if (!in_groups('admin') && !in_groups('admin_gedungutama')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Akses ditolak'
            ]);
        }

        try {
            $json = $this->request->getJSON();
            $userId = $json->user_id;
            $newRole = $json->role;

            $validRoles = [
                'user', 'admin',
                'admin_gedungutama',
                'admin_pusdatin',
                'admin_binamarga',
                'admin_ciptakarya',
                'admin_sda',
                'admin_gedungg',
                'admin_heritage',
                'admin_auditorium'
            ];

            if (!in_array($newRole, $validRoles)) {
                throw new \Exception('Role tidak valid');
            }

            if (!$userId || !$newRole) {
                throw new \Exception('Data tidak lengkap');
            }

            $user = $this->userModel->find($userId);
            if (!$user) {
                throw new \Exception('User tidak ditemukan');
            }

            $roleId = $this->getRoleId($newRole);
            if (!$roleId) {
                throw new \Exception('Role tidak valid');
            }

            $db = \Config\Database::connect();
            
            try {
                $db->transStart();
                
                $db->table('auth_groups_users')
                    ->where('user_id', $userId)
                    ->delete();

                $db->table('auth_groups_users')->insert([
                    'user_id' => $userId,
                    'group_id' => $roleId
                ]);

                $db->transComplete();

                if ($db->transStatus() === false) {
                    throw new \Exception('Gagal mengubah role pengguna');
                }

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Role berhasil diubah'
                ]);
            } catch (\Exception $e) {
                $db->transRollback();
                throw $e;
            }

        } catch (\Exception $e) {
            log_message('error', '[Users::changeRole] Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
            $this->userModel->update($userId, [
                'role' => $newRole
            ]);
        }
    }

    private function getRoleId($roleName)
    {
        $db = \Config\Database::connect();
        $role = $db->table('auth_groups')
            ->where('name', $roleName)
            ->get()
            ->getRow();
            
        if (!$role) {
            throw new \Exception('Role tidak ditemukan');
        }
        
        return $role->id;
    }

    protected function getCurrentRole($userId) 
    {
        $db = \Config\Database::connect();
    
        $role = $db->table('auth_groups_users')
            ->select('auth_groups.name AS role')
            ->join('auth_groups', 'auth_groups.id = auth_groups_users.group_id')
            ->where('auth_groups_users.user_id', $userId)
            ->limit(1)
            ->get()
            ->getRow();
    
        return $role ? $role->role : 'user'; // fallback ke 'user' kalau tidak ketemu
    }
    protected function getAllRoles($userId)
{
    $db = \Config\Database::connect();

    $roles = $db->table('auth_groups_users')
        ->select('auth_groups.name AS role')
        ->join('auth_groups', 'auth_groups.id = auth_groups_users.group_id')
        ->where('auth_groups_users.user_id', $userId)
        ->get()
        ->getResultArray();

    return array_column($roles, 'role'); // hasilnya array seperti ['admin', 'admin_gedungutama']
}
        
    public function deleteUser()
{
    if (!in_groups('admin')) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Akses ditolak!'
        ]);
    }

    try {
        $json = $this->request->getJSON();

        if (!isset($json->userId) || empty($json->userId)) {
            throw new \Exception('ID pengguna tidak valid');
        }

        $userId = $json->userId;

        log_message('info', "[Users::deleteUser] Menghapus user ID: " . $userId);

        $user = $this->userModel->find($userId);
        if (!$user) {
            throw new \Exception('Pengguna tidak ditemukan');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // Hapus data dari tabel terkait sebelum menghapus user
        $db->table('assets')->where('user_id', $userId)->delete();
        $db->table('pinjam_ruangan')->where('user_id', $userId)->delete();
        $db->table('pinjam')->where('user_id', $userId)->delete();
        $db->table('kembali')->where('user_id', $userId)->delete();
        $db->table('laporan')->where('user_id', $userId)->delete();
        $db->table('auth_tokens')->where('user_id', $userId)->delete();
        $db->table('auth_groups_users')->where('user_id', $userId)->delete();

        // Hapus user dari tabel users
        $db->table('users')->where('id', $userId)->delete();

        $db->transComplete();

        if ($db->transStatus() === false) {
            throw new \Exception('Gagal menghapus pengguna karena transaksi database gagal.');
        }

        log_message('info', "[Users::deleteUser] Berhasil menghapus user ID: " . $userId);
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Pengguna berhasil dihapus'
        ]);
    } catch (\Exception $e) {
        log_message('error', '[Users::deleteUser] Error: ' . $e->getMessage());
        return $this->response->setJSON([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}
public function getActivity($userId)
{
    try {
        if (!is_numeric($userId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID Pengguna tidak valid'
            ]);
        }

        $user = $this->userModel->find($userId);
        if (!$user) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Pengguna tidak ditemukan'
            ]);
        }

        // Login History
        $logins = $this->loginModel->builder()
            ->where('user_id', $userId)
            ->orderBy('date', 'DESC')
            ->get()
            ->getResultArray();
        log_message('debug', 'Login ditemukan: ' . count($logins));

        // === KENDARAAN ===
        $peminjamanKendaraan = $this->pinjamModel->builder()
            ->select('pinjam.*, assets.merk, assets.no_polisi')
            ->join('assets', 'assets.id = pinjam.kendaraan_id', 'left')
            ->where('pinjam.user_id', $userId)
            ->orderBy('pinjam.created_at', 'DESC')
            ->get()
            ->getResultArray();
        log_message('debug', 'Peminjaman kendaraan: ' . count($peminjamanKendaraan));

        $pengembalianKendaraan = $this->kembaliModel->builder()
            ->select('kembali.*, assets.merk, assets.no_polisi')
            ->join('assets', 'assets.id = kembali.kendaraan_id', 'left')
            ->where('kembali.user_id', $userId)
            ->orderBy('kembali.created_at', 'DESC')
            ->get()
            ->getResultArray();
        log_message('debug', 'Pengembalian kendaraan: ' . count($pengembalianKendaraan));

        // === BARANG ===
        $peminjamanBarang = $this->pinjamBarangModel->builder()
            ->select('pinjam_barang.*, barang.nama_barang')
            ->join('barang', 'barang.id = pinjam_barang.barang_id', 'left')
            ->where('pinjam_barang.user_id', $userId)
            ->orderBy('pinjam_barang.created_at', 'DESC')
            ->get()
            ->getResultArray();
        log_message('debug', 'Peminjaman barang: ' . count($peminjamanBarang));
        log_message('debug', 'Isi data peminjaman barang: ' . json_encode($peminjamanBarang));

        $pengembalianBarang = $this->pinjamBarangModel->builder()
            ->select('pinjam_barang.*, barang.nama_barang')
            ->join('barang', 'barang.id = pinjam_barang.barang_id', 'left')
            ->where('pinjam_barang.user_id', $userId)
            ->where('pinjam_barang.status', 'dikembalikan')
            ->orderBy('pinjam_barang.created_at', 'DESC')
            ->get()
            ->getResultArray();
        log_message('debug', 'Pengembalian barang: ' . count($pengembalianBarang));

        // === RUANGAN ===
        $peminjamanRuangan = $this->pinjamRuanganModel->builder()
            ->select('pinjam_ruangan.*, ruangan.nama_ruangan')
            ->join('ruangan', 'ruangan.id = pinjam_ruangan.ruangan_id', 'left')
            ->where('pinjam_ruangan.user_id', $userId)
            ->orderBy('pinjam_ruangan.created_at', 'DESC')
            ->get()
            ->getResultArray();
        log_message('debug', 'Peminjaman ruangan: ' . count($peminjamanRuangan));
        log_message('debug', 'Isi data peminjaman ruangan: ' . json_encode($peminjamanRuangan));

        $pengembalianRuangan = $this->pinjamRuanganModel->builder()
            ->select('pinjam_ruangan.*, ruangan.nama_ruangan')
            ->join('ruangan', 'ruangan.id = pinjam_ruangan.ruangan_id', 'left')
            ->where('pinjam_ruangan.user_id', $userId)
            ->where('pinjam_ruangan.status', 'dikembalikan')
            ->orderBy('pinjam_ruangan.created_at', 'DESC')
            ->get()
            ->getResultArray();
        log_message('debug', 'Pengembalian ruangan: ' . count($pengembalianRuangan));

        $userData = [
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'fullname' => $user->fullname ?? $user->username,
            'active' => $user->active
        ];

        return $this->response->setJSON([
            'success' => true,
            'user' => $userData,
            'logins' => $logins,
            'peminjaman' => $peminjamanKendaraan,
            'pengembalian' => $pengembalianKendaraan,
            'peminjaman_kendaraan' => $peminjamanKendaraan,
            'pengembalian_kendaraan' => $pengembalianKendaraan,
            'peminjaman_barang' => $peminjamanBarang,
            'pengembalian_barang' => $pengembalianBarang,
            'peminjaman_ruangan' => $peminjamanRuangan,
            'pengembalian_ruangan' => $pengembalianRuangan
        ]);
    } catch (\Exception $e) {
        log_message('error', 'Error in getActivity: ' . $e->getMessage());
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Terjadi kesalahan saat mengambil data',
            'debug_info' => ENVIRONMENT === 'development' ? $e->getMessage() : null
        ]);
    }
}



    public function activity($userId)
    {
        if (!in_groups('admin')) {
            return redirect()->back()->with('error', 'Akses ditolak');
        }

        try {
            $user = $this->userModel->find($userId);
            if (!$user) {
                return redirect()->to('/admin/daftar-pengguna')->with('error', 'Pengguna tidak ditemukan');
            }

            $logins = $this->loginModel->where('user_id', $userId)
                ->orderBy('created_at', 'DESC')
                ->findAll();

            $peminjaman = $this->pinjamModel->select('pinjam.*, assets.merk, assets.no_polisi')
                ->join('assets', 'assets.id = pinjam.kendaraan_id')
                ->where('pinjam.user_id', $userId)
                ->orderBy('pinjam.created_at', 'DESC')
                ->findAll();

            $pengembalian = $this->kembaliModel->select('kembali.*, assets.merk, assets.no_polisi')
                ->join('assets', 'assets.id = kembali.kendaraan_id')
                ->where('kembali.user_id', $userId)
                ->orderBy('kembali.created_at', 'DESC')
                ->findAll();

            $data = [
                'title' => 'Aktivitas Pengguna',
                'user' => $user,
                'logins' => $logins ?? [],
                'peminjaman' => $peminjaman ?? [],
                'pengembalian' => $pengembalian ?? []
            ];

            return view('admin/daftar-pengguna', $data);

        } catch (\Exception $e) {
            log_message('error', '[Users::activity] Error: ' . $e->getMessage());
            return redirect()->to('/admin/daftar-pengguna')->with('error', 'Terjadi kesalahan saat mengambil data aktivitas');
        }
    }

    public function edit($id)
    {
        $userModel = new UserModel();
        $data['user'] = $userModel->find($id);

        if (!$data['user']) {
            return redirect()->to('/admin/users')->with('error', 'User tidak ditemukan!');
        }

        return view('admin/users/edit', $data);
    }
    public function update($id)
    {
        log_message('error', 'Fungsi update() dipanggil dengan ID: ' . $id);
    
        try {
            $userModel = new UserModel();
            $data = $this->request->getPost();
    
            log_message('error', 'Data yang diterima: ' . json_encode($data));
    
            if (empty($data)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data tidak lengkap'
                ])->setStatusCode(400);
            }
    
            $existingUser = $userModel->find($id);
            if (!$existingUser) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'User tidak ditemukan'
                ])->setStatusCode(404);
            }
    
            // Jika email dikirim, pastikan email unik
            if (!empty($data['email']) && $data['email'] !== $existingUser->email) {
                if ($userModel->where('email', $data['email'])->where('id !=', $id)->countAllResults() > 0) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Email sudah digunakan oleh pengguna lain'
                    ])->setStatusCode(400);
                }
            }
    
            // Hanya update field yang diubah
            $updateData = [];
    
            if (!empty($data['fullname'])) {
                $updateData['fullname'] = $data['fullname'];
            }
            if (!empty($data['email']) && $data['email'] !== $existingUser->email) {
                $updateData['email'] = $data['email'];
            }
            if (!empty($data['unit_organisasi'])) {
                $updateData['unit_organisasi'] = $data['unit_organisasi'];
            }
            if (!empty($data['unit_kerja'])) {
                $updateData['unit_kerja'] = $data['unit_kerja'];
            }
            // if (!empty($data['role'])) {
            //     $updateData['role'] = $data['role'];
            // }
            if (isset($data['active'])) { // Pakai isset agar bisa update active = 0
                $updateData['active'] = (int) $data['active'];
            }
    
            if (empty($updateData)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Tidak ada perubahan data'
                ])->setStatusCode(400);
            }
    
            $updateSuccess = $userModel->update($id, $updateData);
    
            if (!$updateSuccess) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal update database'
                ])->setStatusCode(500);
            }
    
            return $this->response->setJSON([
                'success' => true,
                'message' => 'User berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error saat update: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan'
            ])->setStatusCode(500);
        }
    }    
    public function pending()
{
    $userModel = new \Myth\Auth\Models\UserModel();
    $pendingUsers = $userModel->where('active', 0)->findAll();

    return view('admin/users/pending', ['users' => $pendingUsers]);
}

public function activate()
{
    $userId = $this->request->getPost('user_id');
    $userModel = new \Myth\Auth\Models\UserModel();
    $userModel->update($userId, [
        'active' => 1,
        'status' => 'aktif' // opsional, kalau kamu pakai kolom status
    ]);

    return redirect()->back()->with('message', 'Akun berhasil diaktifkan.');
}              
    
}