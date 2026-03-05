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
            'luas_ruangan' => 'permit_empty|numeric', // Tambahkan validasi untuk luas_ruangan
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
            'luas_ruangan' => $this->request->getPost('luas_ruangan') ?? null, // Tambahkan luas_ruangan
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
            'kapasitas' => 'required|numeric',
            'luas_ruangan' => 'permit_empty|numeric'  // TAMBAH VALIDASI LUAS RUANGAN
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

        // HANDLE LUAS RUANGAN - TAMBAHAN INI
        $luasRuangan = $this->request->getPost('luas_ruangan');
        $luasRuanganValue = !empty($luasRuangan) ? (float)$luasRuangan : null;
        log_message('debug', 'luas_ruangan value: ' . var_export($luasRuanganValue, true));

        // Prepare data untuk update
        $data = [
            'nama_ruangan' => $this->request->getPost('nama_ruangan'),
            'lokasi' => $lokasiRuangan,
            'kapasitas' => $this->request->getPost('kapasitas'),
            'luas_ruangan' => $luasRuanganValue,  // TAMBAH FIELD INI
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
                'luas_ruangan_received' => $luasRuangan,
                'luas_ruangan_converted' => $luasRuanganValue,
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
                    'nomor_hp_penanggung_jawab' => $this->request->getPost('nomor_hp_penanggung_jawab'),
                    'unit_organisasi' => $this->request->getPost('unit_organisasi'),
                    'unit_kerja' => $this->request->getPost('unit_kerja'),
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
        $excludeId = $this->request->getGet('exclude_id'); // untuk edit

        if (!$ruanganId || !$tanggal || !$waktuMulai || !$waktuSelesai) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Parameter tidak lengkap'
            ]);
        }

        // PERBAIKAN: Validasi format waktu
        if (!$this->isValidTimeFormat($waktuMulai) || !$this->isValidTimeFormat($waktuSelesai)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Format waktu tidak valid'
            ]);
        }

        // PERBAIKAN: Validasi waktu selesai harus lebih besar dari waktu mulai
        if (strtotime($waktuSelesai) <= strtotime($waktuMulai)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Waktu selesai harus lebih besar dari waktu mulai'
            ]);
        }

        // Model untuk booking langsung
        $bookingModel = new \App\Models\BookingRuanganModel();
        
        // Model untuk confirm/pinjam
        $pinjamModel = new \App\Models\PinjamRuanganModel();

        // PERBAIKAN: Cek konflik dengan booking langsung
        $bookingConflicts = $this->checkBookingConflicts($bookingModel, $ruanganId, $tanggal, $waktuMulai, $waktuSelesai, $excludeId, 'booking');

        // PERBAIKAN: Cek konflik dengan pinjam/confirm yang disetujui
        $pinjamConflicts = $this->checkBookingConflicts($pinjamModel, $ruanganId, $tanggal, $waktuMulai, $waktuSelesai, $excludeId, 'pinjam');

        $allConflicts = array_merge($bookingConflicts, $pinjamConflicts);
        $available = empty($allConflicts);

        $response = [
            'success' => true,
            'available' => $available,
            'message' => $available ? 'Ruangan tersedia' : 'Ruangan tidak tersedia pada waktu tersebut'
        ];

        if (!$available) {
            $response['conflicts'] = $allConflicts;
            $response['conflict_details'] = $this->formatConflictDetails($allConflicts);
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
try {

    $today = date('Y-m-d');


    $bookingModel = new \App\Models\BookingRuanganModel();
    $pinjamModel  = new \App\Models\PinjamRuanganModel();

    // 1️⃣ Booking langsung (TIDAK pakai deleted_at)
    $bookingLangsung = $bookingModel
        ->select('
            booking_ruangan.id,
            booking_ruangan.tanggal,
            booking_ruangan.waktu_mulai,
            booking_ruangan.waktu_selesai,
            booking_ruangan.status,
            booking_ruangan.keperluan,
            booking_ruangan.nama_penanggung_jawab,
            booking_ruangan.unit_organisasi,
            booking_ruangan.jumlah_peserta,
            ruangan.nama_ruangan
        ')
        ->join('ruangan', 'ruangan.id = booking_ruangan.ruangan_id')
        ->where('booking_ruangan.status', 'aktif')
        ->where('booking_ruangan.tanggal >=', $today)
        ->findAll();

    // 2️⃣ Pinjam / confirm (PAKAI soft delete)
    $confirm = $pinjamModel
        ->select('
            pinjam_ruangan.id,
            pinjam_ruangan.tanggal,
            pinjam_ruangan.waktu_mulai,
            pinjam_ruangan.waktu_selesai,
            pinjam_ruangan.status,
            pinjam_ruangan.keperluan,
            pinjam_ruangan.nama_penanggung_jawab,
            pinjam_ruangan.unit_organisasi,
            pinjam_ruangan.jumlah_peserta,
            ruangan.nama_ruangan
        ')
        ->join('ruangan', 'ruangan.id = pinjam_ruangan.ruangan_id')
        ->whereIn('pinjam_ruangan.status', ['disetujui', 'dipinjam', 'pending'])
        ->where('pinjam_ruangan.deleted_at', null)
        ->where('pinjam_ruangan.tanggal >=', $today)
        ->findAll();

    // 3️⃣ Gabungkan
    $allData = array_merge($bookingLangsung, $confirm);

    // 4️⃣ Sort tanggal + waktu
    usort($allData, function ($a, $b) {
        return strtotime($a['tanggal'].' '.$a['waktu_mulai'])
             - strtotime($b['tanggal'].' '.$b['waktu_mulai']);
    });

    return $this->response->setJSON([
        'success' => true,
        'data'    => $allData
    ]);

} catch (\Throwable $e) {
    return $this->response->setJSON([
        'success' => false,
        'message' => 'Gagal memuat data kalender',
        'error'   => $e->getMessage()
    ], 500);
}

}


/**
 * Check availability untuk booking langsung  
 */
public function checkBookingAvailability()
{
    $ruanganId = $this->request->getGet('ruangan_id');
    $tanggal = $this->request->getGet('tanggal');
    $waktuMulai = $this->request->getGet('waktu_mulai');
    $waktuSelesai = $this->request->getGet('waktu_selesai');

    if (!$ruanganId || !$tanggal || !$waktuMulai || !$waktuSelesai) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Parameter tidak lengkap'
        ]);
    }

    try {
        $bookingModel = new \App\Models\BookingRuanganModel();
        $conflict = $bookingModel->checkAvailability($ruanganId, $tanggal, $waktuMulai, $waktuSelesai);

        return $this->response->setJSON([
            'status' => 'success',
            'available' => !$conflict,
            'conflict' => $conflict
        ]);

    } catch (\Exception $e) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}

/**
 * Riwayat booking user
 */
public function myBookings()
{
    $bookingModel = new \App\Models\BookingRuanganModel();
    $bookings = $bookingModel->getUserBookings(user_id());

    $data = [
        'title' => 'Riwayat Booking Saya',
        'bookings' => $bookings
    ];

    return view('user/ruangan/my_bookings', $data);
}

/**
 * Cancel booking
 */
public function cancelBooking($bookingId)
{
    try {
        $bookingModel = new \App\Models\BookingRuanganModel();
        
        $result = $bookingModel->cancelBooking($bookingId, user_id());
        
        if ($result) {
            return redirect()->back()->with('success', 'Booking berhasil dibatalkan');
        } else {
            return redirect()->back()->with('error', 'Booking tidak dapat dibatalkan. Pastikan booking milik Anda dan belum melewati batas waktu pembatalan');
        }

    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
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
public function bookingLangsung()
{
    // Force JSON response untuk AJAX requests
    $this->response->setContentType('application/json');
    
    try {
        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'ruangan_id' => 'required|integer',
            'tanggal' => 'required|valid_date',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'required', 
            'keperluan' => 'required|min_length[5]',
            'nama_penanggung_jawab' => 'required|min_length[3]',
            'nomor_hp_penanggung_jawab' => 'required|regex_match[/^[0-9]{10,15}$/]',
            'unit_organisasi' => 'required|min_length[3]',
            'unit_kerja' => 'required|min_length[3]', 
            'jumlah_peserta' => 'required|integer|greater_than[0]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Validasi gagal',
                'validation_errors' => $validation->getErrors()
            ]);
        }

        // Model untuk booking langsung
        $bookingModel = new \App\Models\BookingRuanganModel();
        
        // Cek availability - simple check tanpa blocking
        $existingBooking = $bookingModel->checkAvailability(
    $this->request->getPost('ruangan_id'),
    $this->request->getPost('tanggal'),
    $this->request->getPost('waktu_mulai'),
    $this->request->getPost('waktu_selesai'),
    user_id()
);


        if ($existingBooking) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Ruangan sudah dibooking pada waktu tersebut'
            ]);
        }

        // Validasi tambahan - cek apakah user sudah login
        if (!user_id()) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'User belum login'
            ]);
        }

        $data = [
            'ruangan_id' => $this->request->getPost('ruangan_id'),
            'user_id' => user_id(),
            'tanggal' => $this->request->getPost('tanggal'), 
            'waktu_mulai' => $this->request->getPost('waktu_mulai'),
            'waktu_selesai' => $this->request->getPost('waktu_selesai'),
            'keperluan' => $this->request->getPost('keperluan'),
            'nama_penanggung_jawab' => $this->request->getPost('nama_penanggung_jawab'),
            'nomor_hp_penanggung_jawab' => $this->request->getPost('nomor_hp_penanggung_jawab'),
            'unit_organisasi' => $this->request->getPost('unit_organisasi'),
            'unit_kerja' => $this->request->getPost('unit_kerja'),
            'jumlah_peserta' => $this->request->getPost('jumlah_peserta'),
            'status' => 'aktif'
        ];

        log_message('debug', 'Attempting to insert booking data: ' . json_encode($data));

        $insertResult = $bookingModel->insert($data);
        
        if (!$insertResult) {
            $errors = $bookingModel->errors();
            log_message('error', 'Failed to insert booking: ' . json_encode($errors));
            
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Gagal menyimpan booking',
                'database_errors' => $errors
            ]);
        }

        log_message('info', 'Booking successful for user ' . user_id() . ' - Ruangan ID: ' . $data['ruangan_id']);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Booking ruangan berhasil! Ruangan langsung dapat digunakan.',
            'booking_id' => $insertResult
        ]);
        
    } catch (\Exception $e) {
        log_message('error', 'Error in bookingLangsung: ' . $e->getMessage());
        log_message('error', 'Stack trace: ' . $e->getTraceAsString());
        
        return $this->response->setJSON([
            'success' => false,
            'error' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
        ]);
    }
}
public function getUserLatestBookingData()
{
    try {
        $userId = user_id();
        if (!$userId) {
            log_message('error', 'getUserLatestBookingData: User not authenticated');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User not authenticated'
            ]);
        }

        // PENTING: Ambil ruangan_id dari request untuk filter
        $ruanganId = $this->request->getGet('ruangan_id');
        
        if (!$ruanganId) {
            log_message('error', 'getUserLatestBookingData: No ruangan_id provided');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Ruangan ID is required'
            ]);
        }

        // Convert to integer untuk memastikan type yang tepat
        $ruanganId = intval($ruanganId);
        
        log_message('debug', "getUserLatestBookingData: Looking for user {$userId} in ruangan {$ruanganId}");

        $db = \Config\Database::connect();
        
        // Query dengan filter ruangan_id yang SANGAT SPESIFIK
        $latestBooking = $db->table('booking_ruangan')
            ->select('nama_penanggung_jawab, nomor_hp_penanggung_jawab, unit_organisasi, unit_kerja, keperluan, jumlah_peserta, waktu_mulai, waktu_selesai, tanggal, created_at, ruangan_id')
            ->where('user_id', $userId)
            ->where('ruangan_id', $ruanganId) // FILTER RUANGAN YANG SAMA!
            ->where('status', 'aktif')
            ->orderBy('created_at', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        log_message('debug', "getUserLatestBookingData: Booking query result: " . json_encode($latestBooking));

        if ($latestBooking) {
            // DOUBLE CHECK ruangan_id untuk memastikan
            if (intval($latestBooking['ruangan_id']) === $ruanganId) {
                log_message('info', "Auto-fill SUCCESS: Found booking for user {$userId} at ruangan {$ruanganId}");
                
                return $this->response->setJSON([
                    'success' => true,
                    'data' => [
                        'nama_penanggung_jawab' => $latestBooking['nama_penanggung_jawab'],
                        'nomor_hp_penanggung_jawab' => $latestBooking['nomor_hp_penanggung_jawab'],
                        'unit_organisasi' => $latestBooking['unit_organisasi'],
                        'unit_kerja' => $latestBooking['unit_kerja'],
                        'keperluan' => $latestBooking['keperluan'],
                        'jumlah_peserta' => $latestBooking['jumlah_peserta'],
                        'waktu_mulai' => $latestBooking['waktu_mulai'],
                        'waktu_selesai' => $latestBooking['waktu_selesai'],
                        'tanggal' => $latestBooking['tanggal'],
                        'source_type' => 'booking',
                        'ruangan_id' => $latestBooking['ruangan_id'],
                        'note' => "Data diambil dari booking terakhir di ruangan {$ruanganId}",
                        'created_at' => $latestBooking['created_at']
                    ],
                    'message' => 'Data booking untuk ruangan ini ditemukan'
                ]);
            } else {
                log_message('error', "Auto-fill MISMATCH: Expected ruangan {$ruanganId}, got ruangan {$latestBooking['ruangan_id']}");
            }
        }

        // Fallback: Cari di tabel pinjam_ruangan untuk ruangan yang sama
        $latestPinjam = $db->table('pinjam_ruangan')
            ->select('nama_penanggung_jawab, nomor_hp_penanggung_jawab, unit_organisasi, unit_kerja, keperluan, jumlah_peserta, waktu_mulai, waktu_selesai, tanggal, created_at, ruangan_id')
            ->where('user_id', $userId)
            ->where('ruangan_id', $ruanganId) // FILTER RUANGAN YANG SAMA!
            ->where('deleted_at IS NULL')
            ->whereIn('status', ['disetujui', 'dipinjam'])
            ->orderBy('created_at', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        log_message('debug', "getUserLatestBookingData: Pinjam query result: " . json_encode($latestPinjam));

        if ($latestPinjam) {
            // DOUBLE CHECK ruangan_id untuk memastikan
            if (intval($latestPinjam['ruangan_id']) === $ruanganId) {
                log_message('info', "Auto-fill SUCCESS: Found pinjam for user {$userId} at ruangan {$ruanganId}");
                
                return $this->response->setJSON([
                    'success' => true,
                    'data' => [
                        'nama_penanggung_jawab' => $latestPinjam['nama_penanggung_jawab'],
                        'nomor_hp_penanggung_jawab' => $latestPinjam['nomor_hp_penanggung_jawab'],
                        'unit_organisasi' => $latestPinjam['unit_organisasi'],
                        'unit_kerja' => $latestPinjam['unit_kerja'],  
                        'keperluan' => $latestPinjam['keperluan'],
                        'jumlah_peserta' => $latestPinjam['jumlah_peserta'],
                        'waktu_mulai' => $latestPinjam['waktu_mulai'],
                        'waktu_selesai' => $latestPinjam['waktu_selesai'],
                        'tanggal' => $latestPinjam['tanggal'],
                        'source_type' => 'confirm',
                        'ruangan_id' => $latestPinjam['ruangan_id'],
                        'note' => "Data diambil dari confirm request terakhir di ruangan {$ruanganId}",
                        'created_at' => $latestPinjam['created_at']
                    ],
                    'message' => 'Data confirm request untuk ruangan ini ditemukan'
                ]);
            } else {
                log_message('error', "Auto-fill MISMATCH: Expected ruangan {$ruanganId}, got ruangan {$latestPinjam['ruangan_id']}");
            }
        }

        log_message('info', "Auto-fill NOT FOUND: No previous data for user {$userId} at ruangan {$ruanganId}");
        
        return $this->response->setJSON([
            'success' => false,
            'message' => "No previous booking data found for this room",
            'ruangan_id' => $ruanganId,
            'user_id' => $userId,
            'note' => "Tidak ada data booking/confirm sebelumnya untuk ruangan {$ruanganId}"
        ]);

    } catch (\Exception $e) {
        log_message('error', 'Error in getUserLatestBookingData: ' . $e->getMessage());
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Server error: ' . $e->getMessage(),
            'error_details' => [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]
        ]);
    }
}
public function getPinjamModal($ruanganId)
{
    if (!logged_in()) {
        return $this->response->setStatusCode(401)->setBody('Unauthorized');
    }

    // Get ruangan data
    $ruanganModel = new \App\Models\RuanganModel();
    $ruangan = $ruanganModel->find($ruanganId);
    
    if (!$ruangan) {
        return $this->response->setStatusCode(404)->setBody('Ruangan not found');
    }

    // Load modal view
    $data = [
        'ruangan' => $ruangan
    ];
    
    return view('user/ruangan/modal_pinjam', $data);
}
public function getDetailPeminjaman($pinjamId)
{
    try {
        $pinjamModel = new PinjamRuanganModel();
        
        $peminjaman = $pinjamModel->select('pinjam_ruangan.*, ruangan.nama_ruangan')
                                  ->join('ruangan', 'ruangan.id = pinjam_ruangan.ruangan_id')
                                  ->find($pinjamId);

        if (!$peminjaman) {
            throw new \Exception('Data peminjaman tidak ditemukan');
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $peminjaman
        ]);

    } catch (\Exception $e) {
        return $this->response->setJSON([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}

/**
 * Cek ketersediaan untuk waktu baru
 */
public function cekKetersediaan()
{
    try {
        $json = $this->request->getJSON();
        
        $pinjamId = $json->pinjam_id;
        $waktuMulai = $json->waktu_mulai;
        $waktuSelesai = $json->waktu_selesai;

        // Get peminjaman data
        $pinjamModel = new PinjamRuanganModel();
        $peminjaman = $pinjamModel->find($pinjamId);
        
        if (!$peminjaman) {
            throw new \Exception('Data peminjaman tidak ditemukan');
        }

        // Cek konflik (exclude current booking)
        $konflik = $this->checkTimeConflict(
            $peminjaman['ruangan_id'], 
            $peminjaman['tanggal'], 
            $waktuMulai, 
            $waktuSelesai, 
            $pinjamId  // Exclude current
        );

        if ($konflik) {
            return $this->response->setJSON([
                'success' => true,
                'available' => false,
                'message' => 'Waktu bentrok dengan: ' . $konflik['nama_penanggung_jawab'] . 
                           ' (' . $konflik['waktu_mulai'] . ' - ' . $konflik['waktu_selesai'] . ')'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'available' => true,
            'message' => 'Waktu tersedia'
        ]);

    } catch (\Exception $e) {
        return $this->response->setJSON([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}

/**
 * Ubah jam dan setujui peminjaman
 */
public function ubahJamSetujui()
{
    // Debug log
    log_message('info', 'ubahJamSetujui called');
    
    try {
        // PERBAIKAN: Get data dari POST dan JSON
        $input = $this->request->getJSON(true) ?: [];
        $postData = $this->request->getPost();
        
        // Prioritaskan POST data untuk form submission
        $pinjamId = $postData['pinjam_id'] ?? $input['pinjam_id'] ?? null;
        $waktuMulaiBaru = $postData['waktu_mulai'] ?? $input['waktu_mulai'] ?? null;
        $waktuSelesaiBaru = $postData['waktu_selesai'] ?? $input['waktu_selesai'] ?? null;
        $alasanUbah = $postData['alasan'] ?? $input['alasan'] ?? null;

        log_message('info', 'Received data: ' . json_encode([
            'pinjam_id' => $pinjamId,
            'waktu_mulai' => $waktuMulaiBaru,
            'waktu_selesai' => $waktuSelesaiBaru,
            'alasan' => $alasanUbah
        ]));

        // Validasi input
        if (!$pinjamId || !$waktuMulaiBaru || !$waktuSelesaiBaru || !$alasanUbah) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data tidak lengkap: ' . json_encode([
                    'pinjam_id' => !!$pinjamId,
                    'waktu_mulai' => !!$waktuMulaiBaru,
                    'waktu_selesai' => !!$waktuSelesaiBaru,
                    'alasan' => !!$alasanUbah
                ])
            ]);
        }

        $pinjamModel = new PinjamRuanganModel();
        $ruanganModel = new RuanganModel();
        
        // PERBAIKAN: Get current data dengan join
        $peminjaman = $pinjamModel->select('pinjam_ruangan.*, ruangan.lokasi, ruangan.nama_ruangan')
                                  ->join('ruangan', 'ruangan.id = pinjam_ruangan.ruangan_id')
                                  ->where('pinjam_ruangan.id', $pinjamId)
                                  ->first();

        if (!$peminjaman) {
            log_message('error', 'Peminjaman not found: ' . $pinjamId);
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data peminjaman tidak ditemukan'
            ]);
        }

        log_message('info', 'Current peminjaman: ' . json_encode($peminjaman));

        // PERBAIKAN: Check admin access
        $gedungRole = $this->getGedungRole($peminjaman['lokasi']);
        if (!in_groups('admin') && !in_groups($gedungRole)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk memverifikasi ruangan ini'
            ]);
        }

        // PERBAIKAN: Normalisasi format waktu (HH:MM)
        if (strlen($waktuMulaiBaru) > 5) {
            $waktuMulaiBaru = substr($waktuMulaiBaru, 0, 5);
        }
        if (strlen($waktuSelesaiBaru) > 5) {
            $waktuSelesaiBaru = substr($waktuSelesaiBaru, 0, 5);
        }

        // PERBAIKAN: Final conflict check dengan exclude current booking
        $konflik = $this->checkTimeConflictForUpdate(
            $peminjaman['ruangan_id'], 
            $peminjaman['tanggal'], 
            $waktuMulaiBaru, 
            $waktuSelesaiBaru, 
            $pinjamId
        );

        if ($konflik) {
            log_message('error', 'Time conflict detected: ' . json_encode($konflik));
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Waktu bentrok dengan peminjaman lain: ' . $konflik['nama_penanggung_jawab']
            ]);
        }

        // PERBAIKAN: Start transaction
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // PERBAIKAN: Update dengan query builder untuk debugging
            $updateData = [
                'waktu_mulai' => $waktuMulaiBaru,
                'waktu_selesai' => $waktuSelesaiBaru,
                'status' => 'disetujui',
                'keterangan_status' => "Jam diubah oleh admin. Alasan: " . $alasanUbah,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            log_message('info', 'Updating with data: ' . json_encode($updateData));
            
            $result = $pinjamModel->update($pinjamId, $updateData);

            if (!$result) {
                log_message('error', 'Update failed. Model errors: ' . json_encode($pinjamModel->errors()));
                throw new \Exception('Gagal mengupdate data: ' . json_encode($pinjamModel->errors()));
            }

            // PERBAIKAN: Verify update dengan fresh query
            $updatedData = $pinjamModel->find($pinjamId);
            log_message('info', 'After update: ' . json_encode($updatedData));

            $db->transComplete();

            if ($db->transStatus() === FALSE) {
                log_message('error', 'Transaction failed');
                throw new \Exception('Transaction failed');
            }

            // PERBAIKAN: Success response dengan data
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Jam berhasil diubah dan peminjaman disetujui',
                'data' => [
                    'id' => $pinjamId,
                    'waktu_lama' => $peminjaman['waktu_mulai'] . ' - ' . $peminjaman['waktu_selesai'],
                    'waktu_baru' => $waktuMulaiBaru . ' - ' . $waktuSelesaiBaru,
                    'ruangan' => $peminjaman['nama_ruangan'],
                    'updated_data' => $updatedData
                ]
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Transaction error: ' . $e->getMessage());
            $db->transRollback();
            throw $e;
        }

    } catch (\Exception $e) {
        log_message('error', 'ubahJamSetujui error: ' . $e->getMessage());
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
}

private function checkTimeConflictForUpdate($ruanganId, $tanggal, $waktuMulai, $waktuSelesai, $excludePinjamId = null)
{
    log_message('info', "Checking time conflict for update: ruangan={$ruanganId}, tanggal={$tanggal}, waktu={$waktuMulai}-{$waktuSelesai}, exclude={$excludePinjamId}");
    
    $pinjamModel = new PinjamRuanganModel();
    
    // PERBAIKAN: Query yang lebih eksplisit dengan debug
    $query = $pinjamModel->select('id, nama_penanggung_jawab, waktu_mulai, waktu_selesai, status')
                        ->where('ruangan_id', $ruanganId)
                        ->where('tanggal', $tanggal)
                        ->whereIn('status', ['pending', 'disetujui'])
                        ->where('deleted_at', null);
    
    // Exclude current booking jika ada
    if ($excludePinjamId) {
        $query = $query->where('id !=', $excludePinjamId);
    }
    
    $existingBookings = $query->findAll();
    
    log_message('info', 'Existing bookings: ' . json_encode($existingBookings));

    foreach ($existingBookings as $booking) {
        // PERBAIKAN: Normalisasi format waktu untuk perbandingan
        $existingStart = substr($booking['waktu_mulai'], 0, 5);
        $existingEnd = substr($booking['waktu_selesai'], 0, 5);
        $newStart = substr($waktuMulai, 0, 5);
        $newEnd = substr($waktuSelesai, 0, 5);
        
        log_message('info', "Comparing: new({$newStart}-{$newEnd}) vs existing({$existingStart}-{$existingEnd})");
        
        // Check overlap: (StartA < EndB) && (EndA > StartB)
        if (($newStart < $existingEnd) && ($newEnd > $existingStart)) {
            log_message('error', 'Time conflict found with booking ID: ' . $booking['id']);
            return $booking; // Return conflicting booking
        }
    }
    
    log_message('info', 'No time conflict found');
    return null; // No conflict
}

public function debugUbahJam()
{
    try {
        $pinjamId = $this->request->getGet('id') ?: 47; // Default dari screenshot
        
        $pinjamModel = new PinjamRuanganModel();
        $data = $pinjamModel->find($pinjamId);
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Debug data',
            'data' => $data,
            'post_data' => $this->request->getPost(),
            'json_data' => $this->request->getJSON(true)
        ]);
        
    } catch (\Exception $e) {
        return $this->response->setJSON([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}
public function getPeminjamanByRuangan($ruanganId = null)
{
    try {
        if (!$ruanganId) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Ruangan ID harus disediakan'
            ]);
        }

        // ✅ GUNAKAN MODEL YANG BENAR
        $pinjamModel = new \App\Models\PinjamRuanganModel();
        $ruanganModel = new \App\Models\RuanganModel();
        $userModel = new \Myth\Auth\Models\UserModel(); // <- INI YANG BENAR!

        // Get peminjaman data
        $peminjaman = $pinjamModel->where('ruangan_id', $ruanganId)
            ->where('tanggal >=', date('Y-m-d'))
            ->whereIn('status', ['pending', 'disetujui'])
            ->where('deleted_at', null)
            ->orderBy('tanggal', 'ASC')
            ->orderBy('waktu_mulai', 'ASC')
            ->findAll();

        // Format data untuk response
        $formattedData = [];
        foreach ($peminjaman as $item) {
            $user = $userModel->find($item['user_id']);
            
            $formattedData[] = [
                'id' => $item['id'],
                'nama_penanggung_jawab' => $item['nama_penanggung_jawab'],
                'nama_peminjam' => $user ? $user->fullname : 'User tidak ditemukan',
                'tanggal' => $item['tanggal'],
                'waktu_mulai' => $item['waktu_mulai'],
                'waktu_selesai' => $item['waktu_selesai'],
                'keperluan' => $item['keperluan'] ?? '',
                'status' => $item['status'],
                'created_at' => $item['created_at']
            ];
        }

        $ruangan = $ruanganModel->find($ruanganId);

        return $this->response->setJSON([
            'success' => true,
            'data' => $formattedData,
            'total' => count($formattedData),
            'ruangan_info' => [
                'id' => $ruangan['id'] ?? $ruanganId,
                'nama_ruangan' => $ruangan['nama_ruangan'] ?? 'Unknown',
                'lokasi' => $ruangan['lokasi'] ?? 'Unknown'
            ]
        ]);

    } catch (\Exception $e) {
        log_message('error', 'Error getPeminjamanByRuangan: ' . $e->getMessage());
        return $this->response->setJSON([
            'success' => false,
            'error' => 'Terjadi kesalahan server: ' . $e->getMessage()
        ]);
    }
}
private function checkBookingConflicts($model, $ruanganId, $tanggal, $waktuMulai, $waktuSelesai, $excludeId = null, $type = 'booking')
{
    $builder = $model->builder();

    $builder->where('ruangan_id', $ruanganId)
            ->where('tanggal', $tanggal);

    // ✅ deleted_at hanya untuk pinjam_ruangan
    if ($type === 'booking') {
        $builder->where('status', 'aktif');
    } else {
        $builder->where('deleted_at', null)
                ->whereIn('status', ['disetujui', 'pending']);
    }

    // Exclude jika edit
    if ($excludeId) {
        $builder->where('id !=', $excludeId);
    }

    // ✅ LOGIC OVERLAP YANG BENAR (cukup 1 rumus)
    $builder->groupStart()
        ->where('waktu_mulai <', $waktuSelesai)
        ->where('waktu_selesai >', $waktuMulai)
    ->groupEnd();

    $conflicts = $builder->get()->getResultArray();

    foreach ($conflicts as &$conflict) {
        $conflict['conflict_type'] = $type;
    }

    return $conflicts;
}

private function isValidTimeFormat($time)
{
    return preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $time);
}
private function formatConflictDetails($conflicts)
{
    $details = [];
    foreach ($conflicts as $conflict) {
        $details[] = [
            'type' => $conflict['conflict_type'],
            'time' => $conflict['waktu_mulai'] . ' - ' . $conflict['waktu_selesai'],
            'purpose' => $conflict['keperluan'] ?? 'Tidak disebutkan',
            'status' => $conflict['status']
        ];
    }
    return $details;
}
/**
 * UPDATED getDaftarBookingSaya: Gabungkan data dari booking_ruangan dan pinjam_ruangan
 * Ganti method getDaftarBookingSaya() dengan yang ini
 */
/**
 * FIXED getDaftarBookingSaya: Perbaiki Ambiguous Column References
 * Ganti method getDaftarBookingSaya() dengan yang ini
 */
public function getDaftarBookingSaya()
{
    // Set content type JSON
    $this->response->setContentType('application/json');
    
    try {
        // Pastikan user sudah login
        if (!logged_in()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User belum login'
            ]);
        }
        
        // Get user ID dan role yang sedang login
        $userId = user_id();
        $db = \Config\Database::connect();
        
        // ===== CHECK USER ROLE =====
        $isAdmin = in_groups('admin');
        $isAdminGedungUtama = in_groups('admin_gedungutama');
        $isAdminPusdatin = in_groups('admin_pusdatin');
        $isAdminBinaMarga = in_groups('admin_binamarga');
        $isAdminCiptaKarya = in_groups('admin_ciptakarya');
        $isAdminSDA = in_groups('admin_sda');
        $isAdminGedungG = in_groups('admin_gedungg');
        $isAdminHeritage = in_groups('admin_heritage');
        $isAdminAuditorium = in_groups('admin_auditorium');
        
        // ===== BUILD WHERE CONDITIONS =====
        $whereConditions = [];
        $queryParams = [];
        
        if ($isAdmin) {
            // Admin bisa lihat semua booking dari semua gedung
            $whereConditions[] = "br.status != 'selesai'";
        } elseif ($isAdminGedungUtama) {
            $whereConditions[] = "r.lokasi = 'Gedung Utama'";
            $whereConditions[] = "br.status != 'selesai'";
        } elseif ($isAdminPusdatin) {
            $whereConditions[] = "r.lokasi = 'Pusat Data dan Teknologi Informasi'";
            $whereConditions[] = "br.status != 'selesai'";
        } elseif ($isAdminBinaMarga) {
            $whereConditions[] = "r.lokasi = 'Bina Marga'";
            $whereConditions[] = "br.status != 'selesai'";
        } elseif ($isAdminCiptaKarya) {
            $whereConditions[] = "r.lokasi = 'Cipta Karya'";
            $whereConditions[] = "br.status != 'selesai'";
        } elseif ($isAdminSDA) {
            $whereConditions[] = "r.lokasi = 'Sumber Daya Air'";
            $whereConditions[] = "br.status != 'selesai'";
        } elseif ($isAdminGedungG) {
            $whereConditions[] = "r.lokasi = 'Gedung G'";
            $whereConditions[] = "br.status != 'selesai'";
        } elseif ($isAdminHeritage) {
            $whereConditions[] = "r.lokasi = 'Heritage'";
            $whereConditions[] = "br.status != 'selesai'";
        } elseif ($isAdminAuditorium) {
            $whereConditions[] = "r.lokasi = 'Auditorium'";
            $whereConditions[] = "br.status != 'selesai'";
        } else {
            // User biasa hanya bisa lihat booking milik sendiri
            $whereConditions[] = "br.user_id = ?";
            $whereConditions[] = "br.status != 'selesai'";
            $queryParams[] = $userId;
        }
        
        $whereClause = implode(' AND ', $whereConditions);
        
        // ===== QUERY 1: Data dari booking_ruangan (FIXED - tambah br. prefix) =====
        $bookingRuanganQuery = $db->query("
            SELECT 
                'booking' as source_table,
                br.id,
                br.ruangan_id,
                br.user_id,
                br.tanggal,
                br.waktu_mulai,
                br.waktu_selesai,
                br.nama_penanggung_jawab,
                br.nomor_hp_penanggung_jawab,
                br.keperluan,
                br.status,
                br.created_at,
                br.updated_at,
                r.nama_ruangan,
                r.lokasi,
                u.email,
                u.fullname,
                '' as surat_permohonan,
                br.unit_organisasi,
                br.jumlah_peserta
            FROM public.booking_ruangan br
            LEFT JOIN public.ruangan r ON r.id = br.ruangan_id  
            LEFT JOIN public.users u ON u.id = br.user_id
            WHERE {$whereClause}
        ", $queryParams);
        
        // ===== QUERY 2: Data dari pinjam_ruangan (FIXED - tambah pr. prefix) =====
        // Untuk pinjam_ruangan, perlu adjust WHERE clause karena kolom berbeda sedikit
        $pinjamWhereConditions = [];
        $pinjamQueryParams = [];
        
        if ($isAdmin) {
            $pinjamWhereConditions[] = "pr.status != 'selesai'";
        } elseif ($isAdminGedungUtama) {
            $pinjamWhereConditions[] = "r.lokasi = 'Gedung Utama'";
            $pinjamWhereConditions[] = "pr.status != 'selesai'";
        } elseif ($isAdminPusdatin) {
            $pinjamWhereConditions[] = "r.lokasi = 'Pusat Data dan Teknologi Informasi'";
            $pinjamWhereConditions[] = "pr.status != 'selesai'";
        } elseif ($isAdminBinaMarga) {
            $pinjamWhereConditions[] = "r.lokasi = 'Bina Marga'";
            $pinjamWhereConditions[] = "pr.status != 'selesai'";
        } elseif ($isAdminCiptaKarya) {
            $pinjamWhereConditions[] = "r.lokasi = 'Cipta Karya'";
            $pinjamWhereConditions[] = "pr.status != 'selesai'";
        } elseif ($isAdminSDA) {
            $pinjamWhereConditions[] = "r.lokasi = 'Sumber Daya Air'";
            $pinjamWhereConditions[] = "pr.status != 'selesai'";
        } elseif ($isAdminGedungG) {
            $pinjamWhereConditions[] = "r.lokasi = 'Gedung G'";
            $pinjamWhereConditions[] = "pr.status != 'selesai'";
        } elseif ($isAdminHeritage) {
            $pinjamWhereConditions[] = "r.lokasi = 'Heritage'";
            $pinjamWhereConditions[] = "pr.status != 'selesai'";
        } elseif ($isAdminAuditorium) {
            $pinjamWhereConditions[] = "r.lokasi = 'Auditorium'";
            $pinjamWhereConditions[] = "pr.status != 'selesai'";
        } else {
            // User biasa hanya bisa lihat booking milik sendiri
            $pinjamWhereConditions[] = "pr.user_id = ?";
            $pinjamWhereConditions[] = "pr.status != 'selesai'";
            $pinjamQueryParams[] = $userId;
        }
        
        $pinjamWhereClause = implode(' AND ', $pinjamWhereConditions);
        
        $pinjamRuanganQuery = $db->query("
            SELECT 
                'pinjam' as source_table,
                pr.id,
                pr.ruangan_id,
                pr.user_id,
                pr.tanggal,
                pr.waktu_mulai,
                pr.waktu_selesai,
                pr.nama_penanggung_jawab,
                pr.nomor_hp_penanggung_jawab,
                pr.keperluan,
                pr.status,
                pr.created_at,
                pr.updated_at,
                r.nama_ruangan,
                r.lokasi,
                u.email,
                u.fullname,
                pr.surat_permohonan,
                pr.unit_organisasi,
                pr.jumlah_peserta
            FROM public.pinjam_ruangan pr
            LEFT JOIN public.ruangan r ON r.id = pr.ruangan_id  
            LEFT JOIN public.users u ON u.id = pr.user_id
            WHERE {$pinjamWhereClause}
        ", $pinjamQueryParams);
        
        // ===== GABUNGKAN RESULTS =====
        $bookingData = $bookingRuanganQuery->getResultArray();
        $pinjamData = $pinjamRuanganQuery->getResultArray();
        $allBookings = array_merge($bookingData, $pinjamData);
        
        // Sort berdasarkan created_at terbaru
        usort($allBookings, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
        
        // Format data untuk response
        $formattedBookings = [];
        foreach ($allBookings as $booking) {
            $formattedBookings[] = [
                'id' => $booking['id'],
                'source_table' => $booking['source_table'],
                'ruangan_id' => $booking['ruangan_id'],
                'nama_ruangan' => $booking['nama_ruangan'] ?? 'Ruangan tidak ditemukan',
                'lokasi' => $booking['lokasi'] ?? '',
                'tanggal' => $booking['tanggal'],
                'waktu_mulai' => $booking['waktu_mulai'],
                'waktu_selesai' => $booking['waktu_selesai'],
                'nama_penanggung_jawab' => $booking['nama_penanggung_jawab'],
                'nomor_hp_penanggung_jawab' => $booking['nomor_hp_penanggung_jawab'],
                'keperluan' => $booking['keperluan'] ?? '',
                'status' => $booking['status'] ?? 'aktif',
                'user_id' => $booking['user_id'],
                'email' => $booking['email'] ?? '',
                'fullname' => $booking['fullname'] ?? '',
                'surat_permohonan' => $booking['surat_permohonan'] ?? '',
                'unit_organisasi' => $booking['unit_organisasi'] ?? '',
                'jumlah_peserta' => $booking['jumlah_peserta'] ?? 0,
                'created_at' => $booking['created_at'],
                'updated_at' => $booking['updated_at']
            ];
        }
        
        // ===== DYNAMIC TITLE =====
        $title = 'Daftar Booking Saya';
        if ($isAdmin) {
            $title = 'Semua Booking (Admin)';
        } elseif ($isAdminGedungUtama) {
            $title = 'Booking Gedung Utama';
        } elseif ($isAdminPusdatin) {
            $title = 'Booking Pusdatin';
        } elseif ($isAdminBinaMarga) {
            $title = 'Booking Bina Marga';
        } elseif ($isAdminCiptaKarya) {
            $title = 'Booking Cipta Karya';
        } elseif ($isAdminSDA) {
            $title = 'Booking SDA';
        } elseif ($isAdminGedungG) {
            $title = 'Booking Gedung G';
        } elseif ($isAdminHeritage) {
            $title = 'Booking Heritage';
        } elseif ($isAdminAuditorium) {
            $title = 'Booking Auditorium';
        }
        
        $userRole = 'user';
        if ($isAdmin) $userRole = 'admin';
        elseif ($isAdminGedungUtama) $userRole = 'admin_gedungutama';
        elseif ($isAdminPusdatin) $userRole = 'admin_pusdatin';
        elseif ($isAdminBinaMarga) $userRole = 'admin_binamarga';
        elseif ($isAdminCiptaKarya) $userRole = 'admin_ciptakarya';
        elseif ($isAdminSDA) $userRole = 'admin_sda';
        elseif ($isAdminGedungG) $userRole = 'admin_gedungg';
        elseif ($isAdminHeritage) $userRole = 'admin_heritage';
        elseif ($isAdminAuditorium) $userRole = 'admin_auditorium';
        
        return $this->response->setJSON([
            'success' => true,
            'bookings' => $formattedBookings,
            'total' => count($formattedBookings),
            'title' => $title,
            'user_role' => $userRole,
            'debug_info' => [
                'booking_ruangan_count' => count($bookingData),
                'pinjam_ruangan_count' => count($pinjamData),
                'total_combined' => count($allBookings)
            ],
            'message' => 'Data booking berhasil dimuat'
        ]);
        
    } catch (\Exception $e) {
        log_message('error', 'Error in getDaftarBookingSaya: ' . $e->getMessage());
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Terjadi kesalahan saat memuat data booking',
            'error' => $e->getMessage()
        ]);
    }
}

/**
 * Request confirm untuk booking
 * Endpoint: user/ruangan/requestConfirm
 */
public function requestConfirm()
{
    $this->response->setContentType('application/json');
    
    try {
        if (!$this->request->is('post')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Method tidak diizinkan']);
        }
        
        if (!logged_in()) {
            return $this->response->setJSON(['success' => false, 'message' => 'User belum login']);
        }
        
        // Validasi file upload
        $suratPermohonan = $this->request->getFile('surat_permohonan');
        if (!$suratPermohonan || !$suratPermohonan->isValid()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Surat permohonan wajib diunggah dalam format PDF']);
        }
        
        if ($suratPermohonan->getClientMimeType() !== 'application/pdf') {
            return $this->response->setJSON(['success' => false, 'message' => 'File harus dalam format PDF']);
        }
        
        if ($suratPermohonan->getSize() > 2 * 1024 * 1024) {
            return $this->response->setJSON(['success' => false, 'message' => 'Ukuran file maksimal 2MB']);
        }
        
        $bookingId = $this->request->getPost('booking_id');
        if (empty($bookingId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'ID booking tidak valid']);
        }
        
        $userId = user_id();
        $db = \Config\Database::connect();
        
        // Ambil data booking
        $bookingQuery = $db->query("
            SELECT br.*, r.nama_ruangan
            FROM public.booking_ruangan br
            LEFT JOIN public.ruangan r ON r.id = br.ruangan_id
            WHERE br.id = ? AND br.user_id = ?
        ", [$bookingId, $userId]);
        
        $booking = $bookingQuery->getRowArray();
        
        if (!$booking) {
            return $this->response->setJSON(['success' => false, 'message' => 'Booking tidak ditemukan']);
        }
        
        if ($booking['status'] !== 'aktif') {
            return $this->response->setJSON(['success' => false, 'message' => 'Hanya booking aktif yang dapat di-request confirm']);
        }
        
        // Upload file
        $uploadPath = ROOTPATH . 'public/uploads/documents';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }
        
        $newFileName = $suratPermohonan->getRandomName();
        if (!$suratPermohonan->move($uploadPath, $newFileName)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal upload file']);
        }
        
        // Transaction
        $db->transStart();
        
        try {
            // Insert ke pinjam_ruangan
           $insertResult = $db->query("
    INSERT INTO public.pinjam_ruangan (
        user_id,
        ruangan_id,
        nama_penanggung_jawab,
        unit_organisasi,
        unit_kerja,
        keperluan,
        tanggal,
        waktu_mulai,
        waktu_selesai,
        jumlah_peserta,
        surat_permohonan,
        nomor_hp_penanggung_jawab,
        status,
        created_at,
        updated_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
", [
    $booking['user_id'],
    $booking['ruangan_id'],
    $booking['nama_penanggung_jawab'],
    $booking['unit_organisasi'],
    $booking['unit_kerja'],
    $booking['keperluan'],
    $booking['tanggal'],
    $booking['waktu_mulai'],
    $booking['waktu_selesai'],
    $booking['jumlah_peserta'],
    $newFileName,
    $booking['nomor_hp_penanggung_jawab'],
    'pending'
]);

            
            if (!$insertResult) {
                throw new \Exception('Gagal menyimpan data ke tabel pinjam_ruangan');
            }
            
            // Delete dari booking_ruangan
            $deleteResult = $db->query("DELETE FROM public.booking_ruangan WHERE id = ?", [$bookingId]);
            if (!$deleteResult) {
                throw new \Exception('Gagal menghapus data dari tabel booking_ruangan');
            }
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                if (file_exists($uploadPath . '/' . $newFileName)) {
                    unlink($uploadPath . '/' . $newFileName);
                }
                throw new \Exception('Transaction gagal');
            }
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Request confirm berhasil dikirim. Data telah dipindahkan untuk approval admin.'
            ]);
            
        } catch (\Exception $e) {
            $db->transRollback();
            if (file_exists($uploadPath . '/' . $newFileName)) {
                unlink($uploadPath . '/' . $newFileName);
            }
            throw $e;
        }
        
    } catch (\Exception $e) {
        log_message('error', 'Error in requestConfirm: ' . $e->getMessage());
        return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()]);
    }
}
public function getBookingByMonth()
{
    $this->response->setContentType('application/json');

    try {
        $ruanganId = $this->request->getGet('ruangan_id');
        $year      = $this->request->getGet('year');
        $month     = $this->request->getGet('month');

        if (!$ruanganId || !$year || !$month) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Parameter tidak lengkap'
            ]);
        }

        $startDate = sprintf('%04d-%02d-01', $year, $month);
        $endDate   = date('Y-m-t', strtotime($startDate));

        $db = \Config\Database::connect();

        $query = $db->query("
            SELECT 
                tanggal,
                status,
                COUNT(*) as total
            FROM pinjam_ruangan
            WHERE ruangan_id = ?
              AND tanggal BETWEEN ? AND ?
              AND status IN ('disetujui', 'pending')
              AND deleted_at IS NULL
            GROUP BY tanggal, status
        ", [$ruanganId, $startDate, $endDate]);

        $result = $query->getResultArray();

        $calendarData = [];
        foreach ($result as $row) {
            $calendarData[$row['tanggal']][] = [
                'status' => $row['status'],
                'total'  => (int)$row['total']
            ];
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $calendarData
        ]);

    } catch (\Exception $e) {
        return $this->response->setJSON([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}

}