<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\PinjamRuanganModel;
use App\Models\RuanganModel;

class Ruangan extends BaseController {

    private function initCurlWithSSL($url, $isPost = true) 
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_CAINFO, APPPATH . 'ThirdParty/cacert.pem');
        
        if ($isPost) {
            curl_setopt($ch, CURLOPT_POST, true);
        }
        
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        return $ch;
    }

    private function check_file_with_virustotal($file)
    {
        if ($file->getMimeType() !== 'application/pdf' || $file->getSize() > 32 * 1024 * 1024) {
            return true; 
        }

        $api_key = '964f15a6e58be968be71f229b33c52b56a9ba2ccfd8969df075e2700dc584d4a';
        $api_url_scan = 'https://www.virustotal.com/vtapi/v2/file/scan';
        $api_url_report = 'https://www.virustotal.com/vtapi/v2/file/report';

        try {
            $post = [
                'apikey' => $api_key,
                'file' => new \CURLFile($file->getTempName(), 'application/pdf', $file->getName())
            ];

            $ch = $this->initCurlWithSSL($api_url_scan);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            
            $scan_response = curl_exec($ch);
            
            if (curl_errno($ch)) {
                log_message('error', 'Curl error: ' . curl_error($ch));
                curl_close($ch);
                return true;
            }
            curl_close($ch);

            $scan_result = json_decode($scan_response, true);
            if (!isset($scan_result['scan_id'])) {
                log_message('error', 'Invalid scan response: ' . json_encode($scan_result));
                return true;
            }

            sleep(5);

            $post = [
                'apikey' => $api_key,
                'resource' => $scan_result['scan_id']
            ];

            $ch = $this->initCurlWithSSL($api_url_report);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
            
            $report_response = curl_exec($ch);
            curl_close($ch);

            $report_result = json_decode($report_response, true);

            if (!isset($report_result['response_code']) || $report_result['response_code'] === 0) {
                log_message('warning', 'File belum pernah di-scan sebelumnya');
                return false;
            }

            return isset($report_result['positives']) && $report_result['positives'] > 0;

        } catch (\Exception $e) {
            log_message('error', 'Error checking file: ' . $e->getMessage());
            return true;
        }
    }

    public function index() 
    {
        $unitOrganisasi = [
            [
                'nama' => 'Gedung Utama',
                'gambar' => 'gedung-utama.jpg',
                'kode' => 'gedungutama'
            ],
            [
                'nama' => 'Pusat Data dan Teknologi Informasi',
                'gambar' => 'pusdatin.jpg',
                'kode' => 'pusdatin'
            ],
            [
                'nama' => 'Bina Marga',
                'gambar' => 'bina-marga.jpg',
                'kode' => 'binamarga'
            ],
            [
                'nama' => 'Cipta Karya',
                'gambar' => 'cipta-karya.jpg',
                'kode' => 'ciptakarya'
            ],
            [
                'nama' => 'Sumber Daya Air',
                'gambar' => 'bpiw.jpg',
                'kode' => 'sda'
            ],
            [
                'nama' => 'Gedung G',
                'gambar' => 'gedung-g.jpg',
                'kode' => 'gedungg'
            ],
            [
                'nama' => 'Heritage',
                'gambar' => 'bpsdm.jpg',
                'kode' => 'heritage'
            ],
            [
                'nama' => 'Auditorium',
                'gambar' => 'auditorium.jpg',
                'kode' => 'auditorium'
            ]
        ];

        return view('user/ruangan/index', ['unitOrganisasi' => $unitOrganisasi]);
    }

    public function detail($kode)
{
    $lokasiMap = [
        'pusdatin' => 'Pusat Data dan Teknologi Informasi',
        'gedungutama' => 'Gedung Utama', 
        'binamarga' => 'Bina Marga',
        'ciptakarya' => 'Cipta Karya',
        'sda' => 'Sumber Daya Air',
        'gedungg' => 'Gedung G',
        'heritage' => 'Heritage',
        'auditorium' => 'Auditorium'
    ];

    $lokasi = $lokasiMap[$kode] ?? '';
    
    if (empty($lokasi)) {
        return redirect()->to('/user/ruangan');
    }

    // PERBAIKAN: Cek expired bookings dulu
    $this->checkExpiredBookings();
    
    $model = new RuanganModel();
    $pinjamModel = new PinjamRuanganModel();
    
    $ruangans = $model->where('lokasi', $lokasi)
                    ->where('deleted_at', null)
                    ->findAll();

    // PERBAIKAN: Untuk setiap ruangan, cek status booking hari ini
    foreach ($ruangans as &$ruangan) {
        // Cek apakah ada booking aktif untuk hari ini
        $activePeminjaman = $pinjamModel->where('ruangan_id', $ruangan['id'])
            ->where('status', 'disetujui')
            ->where('tanggal', date('Y-m-d'))
            ->where('deleted_at', null)
            ->first();

        // Cek apakah ada booking pending untuk hari ini
        $pendingPeminjaman = $pinjamModel->where('ruangan_id', $ruangan['id'])
            ->where('status', 'pending')
            ->where('tanggal', date('Y-m-d'))
            ->where('deleted_at', null)
            ->first();

        if ($activePeminjaman) {
            $ruangan['status'] = 'Dibooking';
            $ruangan['peminjam_id'] = $activePeminjaman['user_id'];
            $ruangan['jam_mulai'] = $activePeminjaman['waktu_mulai'];
            $ruangan['jam_selesai'] = $activePeminjaman['waktu_selesai'];
        } elseif ($pendingPeminjaman) {
            $ruangan['status'] = 'Menunggu Verifikasi';
            $ruangan['peminjam_id'] = $pendingPeminjaman['user_id'];
            $ruangan['jam_mulai'] = $pendingPeminjaman['waktu_mulai'];
            $ruangan['jam_selesai'] = $pendingPeminjaman['waktu_selesai'];
        } else {
            $ruangan['status'] = 'Tersedia';
            $ruangan['jam_mulai'] = null;
            $ruangan['jam_selesai'] = null;
        }
    }

    return view('user/ruangan/detail', [
        'ruangans' => $ruangans,
        'lokasi' => $lokasi
    ]);
}

             public function tambah()
{
    try {
        // ===== TAMBAHKAN PENGECEKAN PERMISSION BERDASARKAN GEDUNG =====
        
        $lokasiRuangan = $this->request->getPost('lokasi');
        
        if (!$lokasiRuangan) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Lokasi gedung harus dipilih'
            ]);
        }
        
        // Cek permission berdasarkan lokasi gedung
        $gedungRole = $this->getGedungRole($lokasiRuangan);
        
        if (!in_groups('admin') && !in_groups($gedungRole)) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Anda tidak memiliki akses untuk menambah ruangan di gedung ini'
            ]);
        }
        
        // ===== VALIDASI FORM =====
        
        $validation = \Config\Services::validation();
        $validation->setRules([
            'nama_ruangan' => 'required',
            'lokasi' => 'required',
            'kapasitas' => 'required|numeric',
            'foto_ruangan' => [
                'rules' => 'uploaded[foto_ruangan]|max_size[foto_ruangan,2048]|is_image[foto_ruangan]',
                'errors' => [
                    'uploaded' => 'Foto ruangan harus diupload',
                    'max_size' => 'Ukuran foto maksimal 2MB',
                    'is_image' => 'File harus berupa gambar'
                ]
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'error' => $validation->getErrors()
            ]);
        }

        // ===== UPLOAD FOTO =====
        
        $files = $this->request->getFiles();
        $paths = [];
        foreach($files['foto_ruangan'] as $file) {
            if ($file->isValid() && !$file->hasMoved()) {
                $newName = $file->getRandomName();
                $file->move(ROOTPATH . 'public/uploads/ruangan', $newName);
                $paths[] = $newName;
            }
        }

        // ===== PROSES FASILITAS =====
        
        // Proses fasilitas checkbox
        $fasilitasArray = $this->request->getPost('fasilitas');
        $fasilitasFromCheckbox = '';
        if (is_array($fasilitasArray) && !empty($fasilitasArray)) {
            $fasilitasFromCheckbox = implode(', ', $fasilitasArray);
        }
        
        // Proses keterangan dari textarea
        $keteranganText = $this->request->getPost('keterangan');
        $keteranganClean = !empty($keteranganText) ? trim($keteranganText) : '';
        
        // GABUNGKAN fasilitas checkbox + keterangan → masuk ke kolom fasilitas
        $fasilitasGabungan = '';
        if (!empty($fasilitasFromCheckbox) && !empty($keteranganClean)) {
            // Jika ada checkbox dan keterangan
            $fasilitasGabungan = $fasilitasFromCheckbox . '. ' . $keteranganClean;
        } elseif (!empty($fasilitasFromCheckbox)) {
            // Jika hanya ada checkbox
            $fasilitasGabungan = $fasilitasFromCheckbox;
        } elseif (!empty($keteranganClean)) {
            // Jika hanya ada keterangan
            $fasilitasGabungan = $keteranganClean;
        }

        // ===== SIMPAN DATA =====
        
        $data = [
            'nama_ruangan' => $this->request->getPost('nama_ruangan'),
            'lokasi' => $lokasiRuangan, // Gunakan variable yang sudah di-validate
            'kapasitas' => $this->request->getPost('kapasitas'),
            'fasilitas' => $fasilitasGabungan, // Gabungan checkbox + keterangan
            'foto_ruangan' => json_encode($paths),
            'status' => 'Tersedia'
        ];

        $model = new RuanganModel();
        $inserted = $model->insert($data);

        if (!$inserted) {
            throw new \Exception('Gagal menyimpan data ke database');
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Ruangan berhasil ditambahkan di ' . $lokasiRuangan
        ]);

    } catch (\Exception $e) {
        log_message('error', 'Error tambah ruangan: ' . $e->getMessage());
        return $this->response->setJSON([
            'success' => false,
            'error' => 'Gagal menyimpan data: ' . $e->getMessage()
        ]);
    }
}

public function edit($id)
{
    try {
        $ruanganModel = new RuanganModel();
        $ruangan = $ruanganModel->find($id);
        
        if (!$ruangan) {
            throw new \Exception('Ruangan tidak ditemukan');
        }
        
        $gedungRole = $this->getGedungRole($ruangan['lokasi']);
        if (!in_groups('admin') && !in_groups($gedungRole)) {
            throw new \Exception('Anda tidak memiliki akses untuk mengedit ruangan');
        }

        // Cek status ruangan hanya jika ada peminjaman aktif
        $pinjamModel = new \App\Models\PinjamRuanganModel();
        $activeLoan = $pinjamModel->where('ruangan_id', $id)
                                 ->whereIn('status', ['disetujui', 'dipinjam'])
                                 ->where('deleted_at', null)
                                 ->first();
        
        if ($activeLoan) {
            throw new \Exception('Ruangan tidak dapat diedit karena sedang dalam peminjaman aktif');
        }

        // DEBUG: Log semua data POST yang diterima
        log_message('debug', 'POST Data received: ' . json_encode($this->request->getPost()));
        
        $validation = \Config\Services::validation();
        $validation->setRules([
            'nama_ruangan' => 'required',
            'lokasi' => 'required',
            'kapasitas' => 'required|numeric'
            // HAPUS validasi is_active dulu untuk debug
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'error' => $validation->getErrors()
            ]);
        }

        // ===== HANDLING FORM EDIT YANG BERBEDA =====
        
        $fasilitasArray = $this->request->getPost('fasilitas');
        $keteranganText = $this->request->getPost('keterangan');
        
        $fasilitasGabungan = '';
        
        if (is_array($fasilitasArray)) {
            $fasilitasFromCheckbox = '';
            if (!empty($fasilitasArray)) {
                $fasilitasFromCheckbox = implode(', ', $fasilitasArray);
            }
            
            $keteranganClean = !empty($keteranganText) ? trim($keteranganText) : '';
            
            if (!empty($fasilitasFromCheckbox) && !empty($keteranganClean)) {
                $fasilitasGabungan = $fasilitasFromCheckbox . '. ' . $keteranganClean;
            } elseif (!empty($fasilitasFromCheckbox)) {
                $fasilitasGabungan = $fasilitasFromCheckbox;
            } elseif (!empty($keteranganClean)) {
                $fasilitasGabungan = $keteranganClean;
            }
        } else {
            $fasilitasGabungan = trim($fasilitasArray ?: '');
        }

        // Handle foto upload
        $paths = json_decode($ruangan['foto_ruangan'] ?: '[]', true);
        $files = $this->request->getFiles();
        
        if (isset($files['foto_ruangan'])) {
            foreach ($files['foto_ruangan'] as $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $newName = $file->getRandomName();
                    $file->move(WRITEPATH . '../public/uploads/ruangan', $newName);
                    $paths[] = $newName;
                }
            }
        }

        // Get lokasi dan validasi
        $lokasiRuangan = $this->request->getPost('lokasi');
        $allowedLokasi = [
            'Gedung Utama', 'Pusat Data dan Teknologi Informasi', 'Bina Marga',
            'Cipta Karya', 'Sumber Daya Air', 'Gedung G', 'Heritage', 'Auditorium'
        ];

        if (!in_array($lokasiRuangan, $allowedLokasi)) {
            throw new \Exception('Lokasi ruangan tidak valid');
        }

        // DEBUG: Cek is_active handling
        $isActive = $this->request->getPost('is_active');
        log_message('debug', 'is_active raw value: ' . var_export($isActive, true));
        log_message('debug', 'is_active type: ' . gettype($isActive));
        
        $isActiveValue = ($isActive === '1' || $isActive === 'on') ? true : false;
        log_message('debug', 'is_active converted to: ' . var_export($isActiveValue, true));

        // Prepare data untuk update
        $data = [
            'nama_ruangan' => $this->request->getPost('nama_ruangan'),
            'lokasi' => $lokasiRuangan,
            'kapasitas' => $this->request->getPost('kapasitas'),
            'fasilitas' => $fasilitasGabungan,
            'foto_ruangan' => json_encode($paths),
            'is_active' => $isActiveValue,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // DEBUG: Log data yang akan diupdate dan allowedFields
        log_message('debug', 'Update data: ' . json_encode($data));
        
        // DEBUG: Cek allowedFields
        $reflection = new \ReflectionClass($ruanganModel);
        $property = $reflection->getProperty('allowedFields');
        $property->setAccessible(true);
        $allowedFields = $property->getValue($ruanganModel);
        log_message('debug', 'Model allowedFields: ' . json_encode($allowedFields));

        // DEBUG: Coba update dengan error handling yang lebih detail
        $updateResult = $ruanganModel->update($id, $data);
        log_message('debug', 'Update result: ' . var_export($updateResult, true));
        
        if (!$updateResult) {
            $errors = $ruanganModel->errors();
            log_message('error', 'Model validation errors: ' . json_encode($errors));
            log_message('error', 'Last query: ' . $ruanganModel->db->getLastQuery());
            throw new \Exception('Gagal memperbarui data ruangan. Errors: ' . json_encode($errors));
        }

        // DEBUG: Verifikasi data setelah update
        $updatedData = $ruanganModel->find($id);
        log_message('debug', 'Data after update: ' . json_encode($updatedData));

        $statusMessage = $isActiveValue ? 'diaktifkan' : 'dinonaktifkan (maintenance)';
        log_message('info', "Ruangan ID {$id} berhasil diperbarui dan {$statusMessage} oleh user " . user_id());

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Data ruangan berhasil diperbarui. Status: ' . ($isActiveValue ? 'Aktif' : 'Non-aktif (Maintenance)'),
            'debug' => [
                'is_active_received' => $isActive,
                'is_active_converted' => $isActiveValue,
                'updated_data' => $updatedData
            ]
        ]);

    } catch (\Exception $e) {
        log_message('error', 'Error edit ruangan: ' . $e->getMessage());
        log_message('error', 'Stack trace: ' . $e->getTraceAsString());
        return $this->response->setJSON([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
}

    public function pinjam()
    {
        try {
            log_message('debug', 'Received POST data: ' . json_encode($this->request->getPost()));
            log_message('debug', 'Received FILES: ' . json_encode($this->request->getFiles()));

            $surat = $this->request->getFile('surat_permohonan');

            if (!$surat || !$surat->isValid()) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Surat permohonan wajib diunggah dalam format PDF'
                ]);
            }

            if ($this->check_file_with_virustotal($surat)) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'File terdeteksi tidak aman'
                ]);
            }

            $type = $surat->getClientMimeType();
            
            if ($type !== 'application/pdf') {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'File harus dalam format PDF'
                ]);
            }

            $size = $surat->getSize();
            // $minSize = 1024;
            $maxSize = 2 * 1024 * 1024;

            // if ($size < $minSize) {
            //     return $this->response->setJSON([
            //         'success' => false,
            //         'error' => 'Ukuran file minimal 1KB'
            //     ]);
            // }

            if ($size > $maxSize) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Ukuran file maksimal 2MB'
                ]);
            }
            
            $userId = user_id();
            $ruanganId = $this->request->getPost('ruangan_id');

            if (!$userId || !$ruanganId) {
                throw new \Exception('Data user atau ruangan tidak valid');
            }

            $ruanganModel = new RuanganModel();
            $ruangan = $ruanganModel->find($ruanganId);
            
            if (!$ruangan) {
                throw new \Exception('Ruangan tidak ditemukan');
            }

            $uploadPath = ROOTPATH . 'public/uploads/documents';
            if (!is_dir($uploadPath)) {
                if (!mkdir($uploadPath, 0777, true)) {
                    throw new \Exception('Gagal membuat direktori upload');
                }
            }

            $newName = $surat->getRandomName();
            
            if (!$surat->move($uploadPath, $newName)) {
                throw new \Exception('Gagal upload file: ' . $surat->getErrorString());
            }

            $db = \Config\Database::connect();
            $db->transStart();

            try {
                $peminjaman = [
                    'user_id' => $userId,
                    'ruangan_id' => $ruanganId,
                    'nama_penanggung_jawab' => $this->request->getPost('nama_penanggung_jawab'),
                    'unit_organisasi' => $this->request->getPost('unit_organisasi'),
                    'keperluan' => $this->request->getPost('keperluan'),
                    'tanggal' => $this->request->getPost('tanggal'),
                    'waktu_mulai' => $this->request->getPost('waktu_mulai'),
                    'waktu_selesai' => $this->request->getPost('waktu_selesai'),
                    'jumlah_peserta' => $this->request->getPost('jumlah_peserta'),
                    'surat_permohonan' => $newName,
                    'status' => 'pending'
                ];

                $pinjamModel = new PinjamRuanganModel();
                $pinjamModel->skipValidation(true);
                
                $inserted = $pinjamModel->insert($peminjaman);
                
                if (!$inserted) {
                    throw new \Exception('Gagal menyimpan data: ' . json_encode($pinjamModel->errors()));
                }

                $updateData = [
                    'id' => $ruanganId,
                    'status' => 'Menunggu Verifikasi',
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                
                if ($ruanganModel->save($updateData) === false) {
                    throw new \Exception('Gagal update status ruangan');
                }

                $emailData = [
                    'user_email' => user()->email,
                    'user_fullname' => user()->fullname,
                    'nama_ruangan' => $ruangan['nama_ruangan'],
                    'lokasi' => $ruangan['lokasi'],
                    'nama_penanggung_jawab' => $peminjaman['nama_penanggung_jawab'],
                    'unit_organisasi' => $peminjaman['unit_organisasi'],
                    'tanggal' => $peminjaman['tanggal'],
                    'waktu_mulai' => $peminjaman['waktu_mulai'],
                    'waktu_selesai' => $peminjaman['waktu_selesai'],
                    'keperluan' => $peminjaman['keperluan'],
                    'jumlah_peserta' => $peminjaman['jumlah_peserta'],
                    'surat_permohonan' => $newName
                ];
                
                helper('email');
                sendRuanganPeminjamanNotification($emailData, 'new');

                $db->transComplete();

                if ($db->transStatus() === false) {
                    throw new \Exception('Transaksi database gagal');
                }

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Peminjaman ruangan berhasil diajukan dan menunggu persetujuan'
                ]);

            } catch (\Exception $e) {
                $db->transRollback();
                if (file_exists($uploadPath . '/' . $newName)) {
                    unlink($uploadPath . '/' . $newName);
                }
                throw $e;
            }

        } catch (\Exception $e) {
            log_message('error', 'Error peminjaman ruangan: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    private function getGedungRole($lokasi)
    {
        $roleMap = [
            'Gedung Utama' => 'admin_gedungutama',
            'Pusat Data dan Teknologi Informasi' => 'admin_pusdatin',
            'Bina Marga' => 'admin_binamarga',
            'Cipta Karya' => 'admin_ciptakarya',
            'Sumber Daya Air' => 'admin_sda',
            'Gedung G' => 'admin_gedungg',
            'Heritage' => 'admin_heritage',
            'Auditorium' => 'admin_auditorium'
        ];

        return $roleMap[$lokasi] ?? null;
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
    
            if ($status === 'ditolak') {
                $dokumenTambahan = $this->request->getFile('dokumen_tambahan');
                if ($dokumenTambahan && $dokumenTambahan->isValid()) {
                    if ($this->check_file_with_virustotal($dokumenTambahan)) {
                        return $this->response->setJSON([
                            'success' => false,
                            'error' => 'File dokumen tambahan terdeteksi tidak aman'
                        ]);
                    }
                }
            }

            $pinjamModel = new PinjamRuanganModel();
            
            $pinjamData = $pinjamModel->select('pinjam_ruangan.*, ruangan.lokasi')
                                     ->join('ruangan', 'ruangan.id = pinjam_ruangan.ruangan_id')
                                     ->find($pinjam_id);
    
            if (!$pinjamData) {
                throw new \Exception('Data peminjaman tidak ditemukan');
            }
    
            $ruanganModel = new RuanganModel();
            $ruangan = $ruanganModel->find($pinjamData['ruangan_id']);
    
            if (!$ruangan) {
                throw new \Exception('Data ruangan tidak ditemukan');
            }
    
            $gedungRole = $this->getGedungRole($ruangan['lokasi']);
    
            if (!in_groups('admin') && !in_groups($gedungRole)) {
                throw new \Exception('Anda tidak memiliki akses untuk memverifikasi ruangan ini');
            }

            $db = \Config\Database::connect();
            $db->transStart();

            try {
                $updatePinjam = [
                    'id' => $pinjam_id,
                    'status' => $status,
                    'keterangan_status' => $keterangan,
                    'verified_at' => date('Y-m-d H:i:s'),
                    'verified_by' => user_id(),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                if (!$pinjamModel->save($updatePinjam)) {
                    throw new \Exception('Gagal mengupdate status peminjaman');
                }

                // $ruanganStatus = $status === 'disetujui' ? 'Digunakan' : 'Tersedia';
                // $updateRuangan = [
                //     'id' => $pinjamData['ruangan_id'],
                //     'status' => $ruanganStatus,
                //     'updated_at' => date('Y-m-d H:i:s')
                // ];

                if ($status === 'disetujui') {
                    $ruanganStatus = 'Digunakan';
                } else if ($status === 'ditolak') {
                    $ruanganStatus = 'Tersedia';
                }
    
                $updateRuangan = [
                    'id' => $pinjamData['ruangan_id'],
                    'status' => $ruanganStatus,
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                if (!$ruanganModel->save($updateRuangan)) {
                    throw new \Exception('Gagal mengupdate status ruangan');
                }

                $userModel = new \Myth\Auth\Models\UserModel();
                $peminjam = $userModel->find($pinjamData['user_id']);

                $emailData = [
                    'user_email' => $peminjam->email,
                    'user_fullname' => $peminjam->fullname,
                    'nama_ruangan' => $ruangan['nama_ruangan'],
                    'status' => $status,
                    'lokasi' => $pinjamData['lokasi'],
                    'keterangan' => $keterangan,
                    'tanggal' => $pinjamData['tanggal'],
                    'waktu_mulai' => $pinjamData['waktu_mulai'],
                    'waktu_selesai' => $pinjamData['waktu_selesai']
                ];

                helper('email');
                sendRuanganPeminjamanNotification($emailData, 'verified');

                $db->transComplete();

                if ($db->transStatus() === false) {
                    throw new \Exception('Transaksi database gagal');
                }

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Verifikasi peminjaman berhasil'
                ]);

            } catch (\Exception $e) {
                $db->transRollback();
                throw $e;
            }

        } catch (\Exception $e) {
            log_message('error', 'Error verifikasi peminjaman: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function checkExpiredBookings()
    {
        $pinjamModel = new PinjamRuanganModel();
        $ruanganModel = new RuanganModel();
        
        $currentDateTime = date('Y-m-d H:i:s');
        
        $totalActive = $pinjamModel->where('status', 'disetujui')->countAllResults();
        
        $expiredBookings = $pinjamModel->where('status', 'disetujui')
            ->where("CONCAT(tanggal, ' ', waktu_selesai) <", $currentDateTime)
            ->findAll();

        $expiredCount = count($expiredBookings);
        $expiredDetails = [];

        foreach ($expiredBookings as $booking) {
            $db = \Config\Database::connect();
            $db->transStart();

            try {
                $pinjamModel->update($booking['id'], [
                    'status' => 'selesai',
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

                $ruanganModel->update($booking['ruangan_id'], [
                    'status' => 'Tersedia',
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

                $db->transComplete();
                $updated = true;
                
            } catch (\Exception $e) {
                $db->transRollback();
                log_message('error', 'Error updating expired booking: ' . $e->getMessage());
                $updated = false;
            }

            $ruangan = $ruanganModel->find($booking['ruangan_id']);
            
            $expiredDetails[] = [
                'nama_ruangan' => $ruangan['nama_ruangan'],
                'nama_penanggung_jawab' => $booking['nama_penanggung_jawab'],
                'tanggal' => $booking['tanggal'],
                'waktu_selesai' => $booking['waktu_selesai'],
                'updated' => $updated
            ];
        }

        return [
            'totalActive' => $totalActive,
            'expiredCount' => $expiredCount,
            'expiredDetails' => $expiredDetails
        ];
    }


    public function delete($id)
{
    try {
        log_message('debug', 'Delete attempt by user ID: ' . user_id());
        
        $ruanganModel = new RuanganModel();
        $ruangan = $ruanganModel->find($id);
        
        if (!$ruangan) {
            log_message('debug', 'Ruangan not found with ID: ' . $id);
            throw new \Exception('Ruangan tidak ditemukan');
        }
        
        log_message('debug', 'Ruangan found - Name: ' . $ruangan['nama_ruangan'] . ', Status: ' . $ruangan['status'] . ', Lokasi: ' . $ruangan['lokasi']);
        
        // Cek status ruangan
        if ($ruangan['status'] !== 'Tersedia') {
            log_message('debug', 'Cannot delete - status not Tersedia');
            throw new \Exception('Ruangan tidak dapat dihapus karena sedang dalam peminjaman');
        }
        
        // Cek permission berdasarkan lokasi
        $gedungRole = $this->getGedungRole($ruangan['lokasi']);
        log_message('debug', 'Required role for this gedung: ' . $gedungRole);
        
        $isAdmin = in_groups('admin');
        $isGedungAdmin = in_groups($gedungRole);
        
        log_message('debug', 'Permission check - Is super admin: ' . ($isAdmin ? 'YES' : 'NO') . ', Is gedung admin: ' . ($isGedungAdmin ? 'YES' : 'NO'));
        
        if (!$isAdmin && !$isGedungAdmin) {
            log_message('debug', 'Access denied - user tidak memiliki permission');
            throw new \Exception('Anda tidak memiliki akses untuk menghapus ruangan ini');
        }

        // Hapus file foto jika ada
        if (!empty($ruangan['foto_ruangan'])) {
            $fotos = json_decode($ruangan['foto_ruangan'], true) ?? [];
            log_message('debug', 'Deleting photos: ' . json_encode($fotos));
            foreach ($fotos as $foto) {
                $path = ROOTPATH . 'public/uploads/ruangan/' . $foto;
                if (file_exists($path)) {
                    unlink($path);
                    log_message('debug', 'Deleted photo: ' . $foto);
                }
            }
        }

        // Soft delete ruangan
        $deleteResult = $ruanganModel->delete($id);
        log_message('debug', 'Delete result: ' . ($deleteResult ? 'SUCCESS' : 'FAILED'));
        
        if (!$deleteResult) {
            log_message('error', 'Failed to delete ruangan with ID: ' . $id);
            throw new \Exception('Gagal menghapus ruangan dari database');
        }

        log_message('debug', 'Ruangan successfully deleted with ID: ' . $id);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Ruangan berhasil dihapus'
        ]);

    } catch (\Exception $e) {
        log_message('error', 'Error delete ruangan: ' . $e->getMessage());
        log_message('error', 'Error file: ' . $e->getFile() . ' Line: ' . $e->getLine());
        
        return $this->response->setJSON([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
}

    public function uploadGambarUnit()
    {
        $file = $this->request->getFile('gambar');
        if ($file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(ROOTPATH . 'public/uploads/unit-images', $newName);
            return $this->response->setJSON([
                'success' => true,
                'filename' => $newName
            ]);
        }
        return $this->response->setJSON([
            'success' => false,
            'error' => 'Gagal mengunggah gambar'
        ]);
    }

    public function getDetail($id)
    {
        try {
            $model = new RuanganModel();
            $ruangan = $model->find($id);
            
            if (!$ruangan) {
                throw new \Exception('Data ruangan tidak ditemukan');
            }
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $ruangan
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    public function getBookingByGedung($gedungId)
{
    $userId = user_id();
    $model = new \App\Models\PinjamRuanganModel();

    $bookings = $model
        ->select('pinjam_ruangan.*, ruangan.nama_ruangan')
        ->join('ruangan', 'ruangan.id = pinjam_ruangan.ruangan_id')
        ->where('ruangan.gedung_id', $gedungId)
        ->where('pinjam_ruangan.user_id', $userId)
        ->whereIn('pinjam_ruangan.status', ['disetujui', 'dipinjam'])
        ->orderBy('pinjam_ruangan.waktu_mulai', 'ASC')
        ->findAll();

    return $this->response->setJSON(['data' => $bookings]);
}

public function getBookingByDate()
{
    // FORCE JSON response header
    $this->response->setContentType('application/json');
    $this->response->setHeader('Cache-Control', 'no-cache, no-store, must-revalidate');
    $this->response->setHeader('Pragma', 'no-cache');
    $this->response->setHeader('Expires', '0');
    
    try {
        // Support both GET and POST methods
        $ruanganId = $this->request->getGet('ruangan_id') ?? $this->request->getPost('ruangan_id');
        $tanggal = $this->request->getGet('tanggal') ?? $this->request->getPost('tanggal');
        
        // Debug log untuk melihat parameter yang diterima
        log_message('debug', "getBookingByDate called with ruangan_id: {$ruanganId}, tanggal: {$tanggal}");
        log_message('debug', "Request method: " . $this->request->getMethod());
        log_message('debug', "Request URI: " . $this->request->getUri());
        
        // Validation
        if (!$ruanganId || !$tanggal) {
            log_message('error', 'Missing parameters in getBookingByDate');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Parameter ruangan_id dan tanggal harus diisi',
                'debug_info' => [
                    'ruangan_id' => $ruanganId,
                    'tanggal' => $tanggal,
                    'method' => $this->request->getMethod()
                ]
            ]);
        }
        
        // Validate date format
        if (!$this->isValidDate($tanggal)) {
            log_message('error', "Invalid date format: {$tanggal}");
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Format tanggal tidak valid (harus YYYY-MM-DD)',
                'debug_info' => [
                    'received_date' => $tanggal,
                    'expected_format' => 'YYYY-MM-DD'
                ]
            ]);
        }
        
        $pinjamModel = new PinjamRuanganModel();
        
        // Enhanced query dengan error handling
        try {
            $bookings = $pinjamModel->select('
                    pinjam_ruangan.id,
                    pinjam_ruangan.waktu_mulai,
                    pinjam_ruangan.waktu_selesai,
                    pinjam_ruangan.keperluan,
                    pinjam_ruangan.nama_penanggung_jawab,
                    pinjam_ruangan.unit_organisasi,
                    pinjam_ruangan.status,
                    pinjam_ruangan.user_id,
                    pinjam_ruangan.tanggal,
                    pinjam_ruangan.jumlah_peserta,
                    ruangan.nama_ruangan
                ')
                ->join('ruangan', 'ruangan.id = pinjam_ruangan.ruangan_id', 'left')
                ->where('pinjam_ruangan.ruangan_id', $ruanganId)
                ->where('pinjam_ruangan.tanggal', $tanggal)
                ->whereIn('pinjam_ruangan.status', ['disetujui', 'pending', 'menunggu_verifikasi'])
                ->where('pinjam_ruangan.deleted_at', null)
                ->orderBy('pinjam_ruangan.waktu_mulai', 'ASC')
                ->findAll();
                
        } catch (\Exception $queryError) {
            log_message('error', 'Database query error: ' . $queryError->getMessage());
            throw $queryError;
        }
        
        // Debug log untuk melihat hasil query
        log_message('debug', 'Raw query result count: ' . count($bookings));
        
        // Process booking data
        $processedBookings = [];
        $currentUserId = user_id();
        
        foreach ($bookings as $booking) {
            // Clean time format (HH:MM only)
            $waktuMulai = strlen($booking['waktu_mulai']) > 5 ? 
                substr($booking['waktu_mulai'], 0, 5) : $booking['waktu_mulai'];
            $waktuSelesai = strlen($booking['waktu_selesai']) > 5 ? 
                substr($booking['waktu_selesai'], 0, 5) : $booking['waktu_selesai'];
            
            // Privacy protection untuk booking user lain
            if ($booking['user_id'] != $currentUserId) {
                $processedBooking = [
                    'id' => $booking['id'],
                    'waktu_mulai' => $waktuMulai,
                    'waktu_selesai' => $waktuSelesai,
                    'keperluan' => 'Booking Privat',
                    'nama_penanggung_jawab' => 'User Lain',
                    'unit_organisasi' => '***',
                    'status' => $booking['status'],
                    'user_id' => $booking['user_id'],
                    'tanggal' => $booking['tanggal'],
                    'jumlah_peserta' => null,
                    'nama_ruangan' => $booking['nama_ruangan'],
                    'is_own_booking' => false
                ];
            } else {
                $processedBooking = [
                    'id' => $booking['id'],
                    'waktu_mulai' => $waktuMulai,
                    'waktu_selesai' => $waktuSelesai,
                    'keperluan' => $booking['keperluan'],
                    'nama_penanggung_jawab' => $booking['nama_penanggung_jawab'],
                    'unit_organisasi' => $booking['unit_organisasi'],
                    'status' => $booking['status'],
                    'user_id' => $booking['user_id'],
                    'tanggal' => $booking['tanggal'],
                    'jumlah_peserta' => $booking['jumlah_peserta'],
                    'nama_ruangan' => $booking['nama_ruangan'],
                    'is_own_booking' => true
                ];
            }
            
            $processedBookings[] = $processedBooking;
            
            // Debug each booking
            log_message('debug', "Processed booking: {$waktuMulai}-{$waktuSelesai} Status: {$booking['status']}");
        }
        
        // Prepare successful response
        $response = [
            'success' => true,
            'data' => $processedBookings,
            'message' => 'Data booking berhasil diambil',
            'count' => count($processedBookings),
            'debug_info' => [
                'ruangan_id' => $ruanganId,
                'tanggal' => $tanggal,
                'total_bookings' => count($processedBookings),
                'query_executed' => true,
                'current_time' => date('Y-m-d H:i:s'),
                'current_user_id' => $currentUserId,
                'request_method' => $this->request->getMethod()
            ]
        ];
        
        log_message('debug', 'Sending JSON response with ' . count($processedBookings) . ' bookings');
        
        return $this->response->setJSON($response);
        
    } catch (\Exception $e) {
        log_message('error', 'Error getBookingByDate: ' . $e->getMessage());
        log_message('error', 'Stack trace: ' . $e->getTraceAsString());
        
        $errorResponse = [
            'success' => false,
            'message' => 'Terjadi kesalahan saat mengambil data booking',
            'error' => $e->getMessage(),
            'debug_info' => [
                'ruangan_id' => $ruanganId ?? 'null',
                'tanggal' => $tanggal ?? 'null',
                'error_line' => $e->getLine(),
                'error_file' => basename($e->getFile()),
                'request_method' => $this->request->getMethod(),
                'timestamp' => date('Y-m-d H:i:s')
            ]
        ];
        
        return $this->response->setJSON($errorResponse);
    }
}

public function debugBookingData()
{
    try {
        $ruanganId = $this->request->getGet('ruangan_id') ?? 3; // Default untuk Ruang 18
        $tanggal = $this->request->getGet('tanggal') ?? date('Y-m-d');
        
        $pinjamModel = new PinjamRuanganModel();
        
        // Test query langsung
        $db = \Config\Database::connect();
        $query = $db->query("
            SELECT 
                pinjam_ruangan.*,
                ruangan.nama_ruangan
            FROM pinjam_ruangan 
            JOIN ruangan ON ruangan.id = pinjam_ruangan.ruangan_id
            WHERE pinjam_ruangan.ruangan_id = ? 
            AND pinjam_ruangan.tanggal = ?
            AND pinjam_ruangan.deleted_at IS NULL
            ORDER BY pinjam_ruangan.waktu_mulai ASC
        ", [$ruanganId, $tanggal]);
        
        $allBookings = $query->getResultArray();
        
        $activeBookings = $pinjamModel->where('ruangan_id', $ruanganId)
            ->where('tanggal', $tanggal)
            ->whereIn('status', ['disetujui', 'pending'])
            ->where('deleted_at', null)
            ->findAll();
        
        return $this->response->setJSON([
            'debug' => true,
            'ruangan_id' => $ruanganId,
            'tanggal' => $tanggal,
            'all_bookings_count' => count($allBookings),
            'active_bookings_count' => count($activeBookings),
            'all_bookings' => $allBookings,
            'active_bookings' => $activeBookings,
            'query_executed' => true,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        
    } catch (\Exception $e) {
        return $this->response->setJSON([
            'debug' => true,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
}

public function checkTimeConflict($ruanganId, $tanggal, $waktuMulai, $waktuSelesai, $excludeId = null)
{
    $pinjamModel = new PinjamRuanganModel();
    
    $builder = $pinjamModel->builder();
    $builder->where('ruangan_id', $ruanganId)
            ->where('tanggal', $tanggal)
            ->whereIn('status', ['disetujui', 'pending'])
            ->where('deleted_at', null);
    
    // Exclude booking tertentu jika sedang edit
    if ($excludeId) {
        $builder->where('id !=', $excludeId);
    }
    
    // Cek konflik waktu dengan 3 kondisi:
    $builder->groupStart()
                // 1. Waktu mulai baru berada dalam range booking yang ada
                ->where('waktu_mulai <=', $waktuMulai)
                ->where('waktu_selesai >', $waktuMulai)
            ->groupEnd()
            ->orGroupStart()
                // 2. Waktu selesai baru berada dalam range booking yang ada
                ->where('waktu_mulai <', $waktuSelesai)
                ->where('waktu_selesai >=', $waktuSelesai)
                ->where('ruangan_id', $ruanganId)
                ->where('tanggal', $tanggal)
                ->whereIn('status', ['disetujui', 'pending'])
                ->where('deleted_at', null)
            ->groupEnd()
            ->orGroupStart()
                // 3. Booking baru menutupi booking yang ada
                ->where('waktu_mulai >=', $waktuMulai)
                ->where('waktu_selesai <=', $waktuSelesai)
                ->where('ruangan_id', $ruanganId)
                ->where('tanggal', $tanggal)
                ->whereIn('status', ['disetujui', 'pending'])
                ->where('deleted_at', null)
            ->groupEnd();
    
    if ($excludeId) {
        $builder->where('id !=', $excludeId);
    }
    
    $conflicts = $builder->get()->getResultArray();
    
    return !empty($conflicts) ? $conflicts[0] : null;
}
    
    /**
     * Method untuk validasi format tanggal
     */
    private function isValidDate($date, $format = 'Y-m-d')
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }
    
    /**
     * Method untuk cek availability ruangan
     * Endpoint: /user/ruangan/checkAvailability
     */
    public function checkAvailability()
    {
        try {
            $ruanganId = $this->request->getGet('ruangan_id');
            $tanggal = $this->request->getGet('tanggal');
            $waktuMulai = $this->request->getGet('waktu_mulai');
            $waktuSelesai = $this->request->getGet('waktu_selesai');
            $excludeId = $this->request->getGet('exclude_id'); // untuk edit booking
            
            // Validasi input
            if (!$ruanganId || !$tanggal || !$waktuMulai || !$waktuSelesai) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Semua parameter harus diisi'
                ]);
            }
            
            $pinjamModel = new PinjamRuanganModel();
            
            // Cek konflik dengan booking yang ada
            $builder = $pinjamModel->builder();
            $builder->where('ruangan_id', $ruanganId)
                    ->where('tanggal', $tanggal)
                    ->whereIn('status', ['disetujui', 'pending'])
                    ->where('deleted_at', null)
                    ->groupStart()
                        // Case 1: Waktu mulai baru di antara booking yang ada
                        ->where('waktu_mulai <=', $waktuMulai)
                        ->where('waktu_selesai >', $waktuMulai)
                    ->groupEnd()
                    ->orGroupStart()
                        // Case 2: Waktu selesai baru di antara booking yang ada
                        ->where('waktu_mulai <', $waktuSelesai)
                        ->where('waktu_selesai >=', $waktuSelesai)
                        ->where('ruangan_id', $ruanganId)
                        ->where('tanggal', $tanggal)
                        ->whereIn('status', ['disetujui', 'pending'])
                        ->where('deleted_at', null)
                    ->groupEnd()
                    ->orGroupStart()
                        // Case 3: Booking baru menutupi booking yang ada
                        ->where('waktu_mulai >=', $waktuMulai)
                        ->where('waktu_selesai <=', $waktuSelesai)
                        ->where('ruangan_id', $ruanganId)
                        ->where('tanggal', $tanggal)
                        ->whereIn('status', ['disetujui', 'pending'])
                        ->where('deleted_at', null)
                    ->groupEnd();
            
            // Exclude booking tertentu jika sedang edit
            if ($excludeId) {
                $builder->where('id !=', $excludeId);
            }
            
            $conflicts = $builder->get()->getResultArray();
            
            $available = empty($conflicts);
            
            $response = [
                'success' => true,
                'available' => $available,
                'message' => $available ? 'Ruangan tersedia' : 'Ruangan tidak tersedia pada waktu tersebut'
            ];
            
            if (!$available) {
                $response['conflicts'] = $conflicts;
            }
            
            return $this->response->setJSON($response);
            
        } catch (\Exception $e) {
            log_message('error', 'Error checkAvailability: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengecek ketersediaan',
                'error' => $e->getMessage()
            ]);
        }
    }
// public function getBookingSaya()
// {
//     if (!logged_in()) {
//         return $this->response->setJSON([
//             'success' => false,
//             'message' => 'User belum login'
//         ]);
//     }

//     $userId = user_id();
//     $model = new \App\Models\PinjamRuanganModel();

//     try {
//         $bookings = $model->select('pinjam_ruangan.*, ruangan.nama_ruangan')
//             ->join('ruangan', 'ruangan.id = pinjam_ruangan.ruangan_id')
//             ->where('pinjam_ruangan.user_id', $userId)
//             ->whereIn('pinjam_ruangan.status', ['disetujui', 'dipinjam', 'pending'])
//             ->orderBy('pinjam_ruangan.tanggal', 'DESC')
//             ->findAll();

//         // Gabungkan tanggal dengan waktu mulai/selesai
//         foreach ($bookings as &$b) {
//             $b['waktu_mulai'] = $b['tanggal'] . ' ' . $b['waktu_mulai'];
//             $b['waktu_selesai'] = $b['tanggal'] . ' ' . $b['waktu_selesai'];
//         }

//         return $this->response->setJSON([
//             'success' => true,
//             'data' => $bookings
//         ]);
//     } catch (\Exception $e) {
//         return $this->response->setJSON([
//             'success' => false,
//             'error' => 'Gagal memuat data booking'
//         ]);
//     }
// }
public function getBookingPublik()
{
    $model = new \App\Models\PinjamRuanganModel();

    try {
        $bookings = $model->select('pinjam_ruangan.*, ruangan.nama_ruangan')
            ->join('ruangan', 'ruangan.id = pinjam_ruangan.ruangan_id')
            ->whereIn('pinjam_ruangan.status', ['disetujui', 'dipinjam']) // hanya yang aktif
            ->orderBy('pinjam_ruangan.tanggal', 'ASC')
            ->orderBy('pinjam_ruangan.waktu_mulai', 'ASC')
            ->findAll();

        return $this->response->setJSON([
            'success' => true,
            'data' => $bookings
        ]);
    } catch (\Exception $e) {
        log_message('error', 'Error getBookingPublik: ' . $e->getMessage());
        return $this->response->setJSON([
            'success' => false,
            'error' => 'Gagal memuat data booking'
        ]);
    }
}
public function toggleActive($id)
{
    try {
        $ruanganModel = new RuanganModel();
        $ruangan = $ruanganModel->find($id);
        
        if (!$ruangan) {
            throw new \Exception('Ruangan tidak ditemukan');
        }

        $gedungRole = $this->getGedungRole($ruangan['lokasi']);
        if (!in_groups('admin') && !in_groups($gedungRole)) {
            throw new \Exception('Anda tidak memiliki akses untuk mengubah status ruangan');
        }

        // Toggle status
        $newStatus = !$ruangan['is_active'];
        
        if (!$ruanganModel->update($id, ['is_active' => $newStatus])) {
            throw new \Exception('Gagal mengubah status ruangan');
        }

        $statusText = $newStatus ? 'diaktifkan' : 'dinonaktifkan (maintenance)';
        
        return $this->response->setJSON([
            'success' => true,
            'message' => "Ruangan berhasil {$statusText}",
            'new_status' => $newStatus
        ]);

    } catch (\Exception $e) {
        return $this->response->setJSON([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
}



}