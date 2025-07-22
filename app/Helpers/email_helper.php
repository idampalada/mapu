<?php

function countDays($startDate, $endDate)
{
    $start = new DateTime($startDate);
    $end = new DateTime($endDate);
    $interval = $start->diff($end);
    return $interval->days + 1;
}

function getSapaan()
{
    $hour = (int) date('H');

    if ($hour >= 0 && $hour < 10) {
        return 'pagi';
    } elseif ($hour >= 10 && $hour < 15) {
        return 'siang';
    } elseif ($hour >= 15 && $hour < 18) {
        return 'sore';
    } else {
        return 'malam';
    }
}

function getEmailTemplate($content) {
    return <<<HTML
    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
        $content
        
        <hr style="margin: 30px 0; border: none; border-top: 1px solid #eee;">
        
        <p style="color: #666; font-size: 12px;">Kementerian Pekerjaan Umumt<br>
        Sekretariat Jenderal<br>
        Biro Umumn</p>
    </div>
HTML;
}

function sendEmail($to, $subject, $message, $attachments = [])
{
    $email = \Config\Services::email();
    
    $email->setFrom('noreply@pu.go.id', 'Manajemen Aset PUPR');
    $email->setMailType('html');
    $email->setSubject($subject);
    $email->setMessage($message);
    
    if (is_array($to)) {
        $email->setTo($to[0]); 
        if (count($to) > 1) {
            for ($i = 1; $i < count($to); $i++) {
                $email->setCC($to[$i]);
            }
        }
    } else {
        $email->setTo($to);
    }

    if (!empty($attachments)) {
        if (is_array($attachments)) {
            foreach ($attachments as $attachment) {
                $filePath = ROOTPATH . 'public/uploads/documents/' . $attachment;
                if (file_exists($filePath)) {
                    $email->attach($filePath);
                }
            }
        } else {
            $filePath = ROOTPATH . 'public/uploads/documents/' . $attachments;
            if (file_exists($filePath)) {
                $email->attach($filePath);
            }
        }
    }

    try {
        if ($email->send()) {
            return true;
        } else {
            log_message('error', 'Email error: ' . $email->printDebugger(['headers']));
            return false;
        }
    } catch (\Exception $e) {
        log_message('error', 'Email error: ' . $e->getMessage());
        return false;
    }
}

function sendPeminjamanNotification($data, $type = 'new') {
    $adminEmail = 'idampalada@if.uai.ac.id';
    $userEmail = $data['user_email'];
    $sapaan = getSapaan();

    if ($type === 'new') {
        $subject = 'Pengajuan Peminjaman Kendaraan Baru';
        $content = "
            <p>Selamat {$sapaan},</p>
            
            <p>Peminjaman kendaraan <strong>" . ($data['merk'] ?? '-') . "</strong> dengan nomor polisi <strong>" . ($data['no_polisi'] ?? '-') . "</strong> telah diajukan.</p>
            
            <div style='background-color: #f8f9fa; padding: 15px; border-radius: 4px; margin: 20px 0;'>
                <strong>Detail Peminjaman:</strong>
                <ul style='margin: 10px 0; padding-left: 20px;'>
                    <li>Peminjam: {$data['user_fullname']}</li>
                    <li>Penanggung Jawab: {$data['nama_penanggung_jawab']}</li>
                    <li>NIP/NRP: {$data['nip_nrp']}</li>
                    <li>Jabatan: {$data['jabatan']}</li>
                    <li>Unit Organisasi: {$data['unit_organisasi']}</li>
                    <li>Tanggal Pinjam: " . date('d/m/Y', strtotime($data['tanggal_pinjam'])) . "</li>
                    <li>Tanggal Kembali: " . date('d/m/Y', strtotime($data['tanggal_kembali'])) . "</li>
                    <li>Durasi Peminjaman: " . countDays($data['tanggal_pinjam'], $data['tanggal_kembali']) . " hari</li>
                </ul>
            </div>

            <p style='text-align: center; margin: 30px 0;'>
                <a href='http://manajemenaset.pu.go.id/admin/dashboard' 
                   style='display: inline-block; padding: 12px 24px; background-color: #007bff; color: white; text-decoration: none; border-radius: 4px;'>
                    Verifikasi Sekarang
                </a>
            </p>

            <p><em>Terima Kasih.</em></p>
        ";

        $message = getEmailTemplate($content);
        $attachments = !empty($data['surat_permohonan']) ? [$data['surat_permohonan']] : [];
        sendEmail($adminEmail, $subject, $message, $attachments);

    } elseif ($type === 'verified') {
        $status = $data['status'];
        $subject = "Peminjaman Kendaraan " . ucfirst($status);

        if ($status === 'disetujui') {
            $content = "
                <p>Selamat {$sapaan}, {$data['user_fullname']}.</p>
                
                <p>Pengajuan peminjaman kendaraan <strong>" . ($data['merk'] ?? '-') . "</strong> dengan nomor polisi <strong>" . ($data['no_polisi'] ?? '-') . "</strong> telah <span style='color: #28a745;'><strong>DISETUJUI</strong></span>.</p>
                
                <div style='background-color: #f8f9fa; padding: 15px; border-radius: 4px; margin: 20px 0;'>
                    <strong>Detail Peminjaman:</strong>
                    <ul style='margin: 10px 0; padding-left: 20px;'>
                        <li>Penanggung Jawab: {$data['nama_penanggung_jawab']}</li>
                        <li>NIP/NRP: {$data['nip_nrp']}</li>
                        <li>Tanggal Pinjam: " . date('d/m/Y', strtotime($data['tanggal_pinjam'])) . "</li>
                        <li>Tanggal Kembali: " . date('d/m/Y', strtotime($data['tanggal_kembali'])) . "</li>
                        <li>Durasi Peminjaman: " . countDays($data['tanggal_pinjam'], $data['tanggal_kembali']) . " hari</li>
                    </ul>
                </div>

                <div style='background-color: #e8f4fe; padding: 15px; border-radius: 4px; margin: 20px 0;'>
                    <strong>Langkah Selanjutnya:</strong>
                    <ol style='margin: 10px 0; padding-left: 20px;'>
                        <li>Cetak dan bawa Surat Jalan Admin yang terlampir</li>
                        <li>Datang sesuai jadwal pengambilan yang telah ditentukan</li>
                        <li>Tunjukkan Surat Jalan Admin kepada petugas</li>
                        <li>Lakukan pemeriksaan kondisi kendaraan bersama petugas</li>
                    </ol>
                </div>

                <p>Jika ada pertanyaan lebih lanjut, mohon hubungi nomor dibawah ini:<p>
                <p>081578732756 - <strong>Domenico Adi Nugroho</strong></p>
            ";
            
            // $attachments = [];
            // if (!empty($data['surat_jalan_admin'])) $attachments[] = $data['surat_jalan_admin'];
            // if (!empty($data['surat_permohonan'])) $attachments[] = $data['surat_permohonan'];

            $attachments =[];
            if (!empty($data['surat_jalan_admin'])) {
                $attachments[] = $data['surat_jalan_admin'];
            }
            if (!empty($data['surat_permohonan'])) {
                $attachments[] = $data['surat_permohonan'];
            }

            $message = getEmailTemplate($content);
            sendEmail($userEmail, $subject, $message, $attachments);
        } else {
            $content = "
                <p>Selamat {$sapaan}, {$data['user_fullname']}.</p>
                
                <p>Mohon maaf, pengajuan peminjaman kendaraan <strong>" . ($data['merk'] ?? '-') . "</strong> dengan nomor polisi <strong>" . ($data['no_polisi'] ?? '-') . "</strong> <span style='color: #dc3545;'><strong>TIDAK DISETUJUI</strong></span>.</p>

                <div style='background-color: #f8f9fa; padding: 15px; border-radius: 4px; margin: 20px 0;'>
                    <strong>Detail Pengajuan:</strong>
                    <ul style='margin: 10px 0; padding-left: 20px;'>
                        <li>Penanggung Jawab: {$data['nama_penanggung_jawab']}</li>
                        <li>NIP/NRP: {$data['nip_nrp']}</li>
                        <li>Tanggal Pinjam: " . date('d/m/Y', strtotime($data['tanggal_pinjam'])) . "</li>
                        <li>Tanggal Kembali: " . date('d/m/Y', strtotime($data['tanggal_kembali'])) . "</li>
                    </ul>
                </div>

                <div style='background-color: #fff3cd; padding: 15px; border-radius: 4px; margin: 20px 0;'>
                    <strong>Alasan Penolakan:</strong>
                    <p style='margin: 10px 0;'>" . ($data['keterangan'] ?? '-') . "</p>
                </div>

                " . (!empty($data['dokumen_tambahan']) ? "<p><strong>Catatan:</strong> Terlampir dokumen tambahan terkait penolakan.</p>" : "") . "

                <p>Jika ada pertanyaan lebih lanjut, mohon hubungi nomor dibawah ini:<p>
                <p>081578732756 - <strong>Domenico Adi Nugroho</strong></p>
            ";
            // $attachments = !empty($data['dokumen_tambahan']) ? [$data['dokumen_tambahan']] : [];
        }

        $attachments =[];
        if (!empty($data['dokumen_tambahan'])) {
            $attachments[] = $data['dokumen_tambahan'];
        }
        if (!empty($data['surat_jalan_admin'])) {
            $attachments[] = $data['surat_jalan_admin'];
        }
        if (!empty($data['surat_permohonan'])) {
            $attachments[] = $data['surat_permohonan'];
        }

        $message = getEmailTemplate($content);
        sendEmail($userEmail, $subject, $message, $attachments);
    }
}

function sendPengembalianNotification($data, $type = 'new')
{
    $adminEmail = 'idampalada@if.uai.ac.id';
    $userEmail = $data['user_email'];
    $sapaan = getSapaan();

    if ($type === 'new') {
        $subject = 'Pengajuan Pengembalian Kendaraan';
        $content = "
            <p>Selamat {$sapaan},</p>
            
            <p>Pengembalian kendaraan <strong>" . ($data['merk'] ?? '-') . "</strong> dengan nomor polisi <strong>" . ($data['no_polisi'] ?? '-') . "</strong> telah diajukan.</p>
            
            <div style='background-color: #f8f9fa; padding: 15px; border-radius: 4px; margin: 20px 0;'>
                <strong>Detail Pengembalian:</strong>
                <ul style='margin: 10px 0; padding-left: 20px;'>
                    <li>Penanggung Jawab: {$data['nama_penanggung_jawab']}</li>
                    <li>NIP/NRP: {$data['nip_nrp']}</li>
                    <li>Tanggal Pinjam: " . date('d/m/Y', strtotime($data['tanggal_pinjam'])) . "</li>
                    <li>Tanggal Kembali: " . date('d/m/Y', strtotime($data['tanggal_kembali'])) . "</li>
                    <li>Durasi Penggunaan: " . countDays($data['tanggal_pinjam'], $data['tanggal_kembali']) . " hari</li>
                </ul>
            </div>
        
            <p style='text-align: center; margin: 30px 0;'>
                <a href='http://manajemenaset.pu.go.id/admin/dashboard' 
                style='display: inline-block; padding: 12px 24px; background-color: #007bff; color: white; text-decoration: none; border-radius: 4px;'>
                    Verifikasi Sekarang
                </a>
            </p>
        ";

        $attachments = [];
        if (!empty($data['surat_pengembalian'])) {
            $attachments[] = $data['surat_pengembalian'];
        }
        if (!empty($data['berita_acara_pengembalian'])) {
            $attachments[] = $data['berita_acara_pengembalian'];
        }

        $message = getEmailTemplate($content);
        sendEmail($adminEmail, $subject, $message, $attachments);

    } elseif ($type === 'verified') {
        $status = $data['status'];
        $subject = "Pengembalian Kendaraan " . ucfirst($status);
    
        if ($status === 'disetujui') {
            $content = "
                <p>Selamat {$sapaan}, {$data['user_fullname']}.</p>
                
                <p>Pengembalian kendaraan <strong>" . ($data['merk'] ?? '-') . "</strong> dengan nomor polisi <strong>" . ($data['no_polisi'] ?? '-') . "</strong> telah <span style='color: #28a745;'><strong>DISETUJUI</strong></span>.</p>
                
                <div style='background-color: #f8f9fa; padding: 15px; border-radius: 4px; margin: 20px 0;'>
                    <strong>Detail Pengembalian:</strong>
                    <ul style='margin: 10px 0; padding-left: 20px;'>
                        <li>Tanggal Pinjam: " . date('d/m/Y', strtotime($data['tanggal_pinjam'])) . "</li>
                        <li>Tanggal Kembali: " . date('d/m/Y', strtotime($data['tanggal_kembali'])) . "</li>
                        <li>Kondisi Kendaraan: {$data['kondisi_kembali']}</li>
                    </ul>
                </div>
                
                <p>Jika ada pertanyaan lebih lanjut, mohon hubungi nomor dibawah ini:<p>
                <p>081578732756 - <strong>Domenico Adi Nugroho</strong></p>
            ";
    
            $attachments = [];
            if (!empty($data['surat_pengembalian'])) {
                $attachments[] = $data['surat_pengembalian'];
            }
            if (!empty($data['berita_acara_pengembalian'])) {
                $attachments[] = $data['berita_acara_pengembalian'];
            }

            $message = getEmailTemplate($content);
            sendEmail($userEmail, $subject, $message, $attachments);
    
        } else {
            $content = "
                <p>Selamat {$sapaan}, {$data['user_fullname']}.</p>
                
                <p>Mohon maaf, pengembalian kendaraan <strong>" . ($data['merk'] ?? '-') . "</strong> dengan nomor polisi <strong>" . ($data['no_polisi'] ?? '-') . "</strong> <span style='color: #dc3545;'><strong>TIDAK DISETUJUI</strong></span>.</p>
    
                <div style='background-color: #f8f9fa; padding: 15px; border-radius: 4px; margin: 20px 0;'>
                    <strong>Detail Pengembalian:</strong>
                    <ul style='margin: 10px 0; padding-left: 20px;'>
                        <li>Tanggal Pinjam: " . date('d/m/Y', strtotime($data['tanggal_pinjam'])) . "</li>
                        <li>Tanggal Kembali: " . date('d/m/Y', strtotime($data['tanggal_kembali'])) . "</li>
                        <li>Kondisi Kendaraan: {$data['kondisi_kembali']}</li>
                    </ul>
                </div>
    
                <div style='background-color: #fff3cd; padding: 15px; border-radius: 4px; margin: 20px 0;'>
                    <strong>Alasan Penolakan:</strong>
                    <p style='margin: 10px 0;'>{$data['keterangan']}</p>
                </div>
    
                " . (!empty($data['dokumen_tambahan']) ? "<p><strong>Catatan:</strong> Terlampir dokumen tambahan terkait penolakan.</p>" : "") . "
    
                <p>Jika ada pertanyaan lebih lanjut, mohon hubungi nomor dibawah ini:<p>
                <p>081578732756 - <strong>Domenico Adi Nugroho</strong></p>
            ";
    
            $attachments = [];
            if (!empty($data['surat_pengembalian'])) {
                $attachments[] = $data['surat_pengembalian'];
            }
            if (!empty($data['berita_acara_pengembalian'])) {
                $attachments[] = $data['berita_acara_pengembalian'];
            }
            if (!empty($data['dokumen_tambahan'])) {
                $attachments[] = $data['dokumen_tambahan'];
            }
    
            $message = getEmailTemplate($content);
            sendEmail($userEmail, $subject, $message, $attachments);
        }
    }
}

function getAdminEmailByLocation($lokasi) {
    $db = \Config\Database::connect();
    
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

    $adminRole = $roleMap[$lokasi] ?? null;

    if ($adminRole) {
        $results = $db->table('users')
            ->select('users.email')
            ->join('auth_groups_users', 'auth_groups_users.user_id = users.id')
            ->join('auth_groups', 'auth_groups.id = auth_groups_users.group_id')
            ->where('auth_groups.name', $adminRole)
            ->where('users.active', 1)
            ->get()
            ->getResult();

        if (!empty($results)) {
            return array_map(function($row) {
                return $row->email;
            }, $results);
        }
    }

    return ['idampalada@if.uai.ac.id'];
}

function sendRuanganPeminjamanNotification($data, $type = 'new') {
    // $adminEmail = 'idampalada@if.uai.ac.id';
    $adminEmail = getAdminEmailByLocation($data['lokasi']);
    $userEmail = $data['user_email'];
    $sapaan = getSapaan();

    if ($type === 'new') {
        $subject = 'Pengajuan Peminjaman Ruangan Baru';
        $content = "
            <p>Selamat {$sapaan},</p>
            
            <p>Peminjaman ruangan <strong>" . ($data['nama_ruangan'] ?? '-') . "</strong> di <strong>{$data['lokasi']}</strong> telah diajukan.</p>
            
            <div style='background-color: #f8f9fa; padding: 15px; border-radius: 4px; margin: 20px 0;'>
                <strong>Detail Peminjaman Ruangan:</strong>
                <ul style='margin: 10px 0; padding-left: 20px;'>
                    <li>Peminjam: {$data['user_fullname']}</li>
                    <li>Penanggung Jawab: {$data['nama_penanggung_jawab']}</li>
                    <li>Unit Organisasi: {$data['unit_organisasi']}</li>
                    <li>Jumlah Peserta: {$data['jumlah_peserta']}</li>
                    <li>Tanggal: {$data['tanggal']}</li>
                    <li>Waktu mulai: " . date('H:i', strtotime($data['waktu_mulai'])) . "</li>
                    <li>Waktu selesai: " . date('H:i', strtotime($data['waktu_selesai'])) . "</li>
                    <li>Keperluan: {$data['keperluan']}</li>
                </ul>
            </div>

            <p style='text-align: center; margin: 30px 0;'>
                <a href='http://manajemenaset.pu.go.id/admin/dashboard' 
                   style='display: inline-block; padding: 12px 24px; background-color: #007bff; color: white; text-decoration: none; border-radius: 4px;'>
                    Verifikasi Sekarang
                </a>
            </p>

            <p><em>Terima Kasih.</em></p>
        ";

        $message = getEmailTemplate($content);
        $attachments = !empty($data['surat_permohonan']) ? [$data['surat_permohonan']] : [];
        sendEmail($adminEmail, $subject, $message, $attachments);

    } elseif ($type === 'verified') {
        $status = $data['status'];
        $subject = "Peminjaman Ruangan " . ucfirst($status);

        if ($status === 'disetujui') {
            $content = "
                <p>Selamat {$sapaan}, {$data['user_fullname']}.</p>
                
                <p>Pengajuan peminjaman ruangan <strong>" . ($data['nama_ruangan'] ?? '-') . "</strong> telah <span style='color: #28a745;'><strong>DISETUJUI</strong></span>.</p>
                
                <div style='background-color: #f8f9fa; padding: 15px; border-radius: 4px; margin: 20px 0;'>
                    <strong>Detail Peminjaman:</strong>
                    <ul style='margin: 10px 0; padding-left: 20px;'>
                        <li>Tanggal: " . date('d F Y', strtotime($data['tanggal'])) . "</li>
                        <li>Waktu Mulai: " . date('H:i', strtotime($data['waktu_mulai'])) . "</li>
                        <li>Waktu Selesai: " . date('H:i', strtotime($data['waktu_selesai'])) . "</li>
                    </ul>
                </div>

                <p>Jika ada pertanyaan lebih lanjut, mohon hubungi nomor dibawah ini:<p>
                <p>081578732756 - <strong>Domenico Adi Nugroho</strong></p>
            ";

            $message = getEmailTemplate($content);
            sendEmail($userEmail, $subject, $message);
        } else {
            $content = "
                <p>Selamat {$sapaan}, {$data['user_fullname']}.</p>
                
                <p>Mohon maaf, pengajuan peminjaman ruangan <strong>" . ($data['nama_ruangan'] ?? '-') . "</strong> <span style='color: #dc3545;'><strong>TIDAK DISETUJUI</strong></span>.</p>

                <div style='background-color: #f8f9fa; padding: 15px; border-radius: 4px; margin: 20px 0;'>
                    <strong>Detail Pengajuan:</strong>
                    <ul style='margin: 10px 0; padding-left: 20px;'>
                        <li>Tanggal: " . date('d F Y', strtotime($data['tanggal'])) . "</li>
                        <li>Waktu Mulai: " . date('H:i', strtotime($data['waktu_mulai'])) . "</li>
                        <li>Waktu Selesai: " . date('H:i', strtotime($data['waktu_selesai'])) . "</li>
                    </ul>
                </div>

                <p>Jika ada pertanyaan lebih lanjut, mohon hubungi nomor dibawah ini:<p>
                <p>081578732756 - <strong>Domenico Adi Nugroho</strong></p>
            ";
        }

        $message = getEmailTemplate($content);
        sendEmail($userEmail, $subject, $message);
    }
}
function sendBarangPeminjamanNotification($data, $type = 'new') {
    $adminEmail = 'idampalada@if.uai.ac.id'; // bisa disesuaikan nanti
    $userEmail = $data['user_email'];
    $sapaan = getSapaan();

    if ($type === 'new') {
        $subject = 'Pengajuan Peminjaman Barang Baru';
        $content = "
            <p>Selamat {$sapaan},</p>

            <p>Peminjaman barang <strong>" . ($data['nama_barang'] ?? '-') . "</strong> telah diajukan.</p>

            <div style='background-color: #f8f9fa; padding: 15px; border-radius: 4px; margin: 20px 0;'>
                <strong>Detail Peminjaman Barang:</strong>
                <ul style='margin: 10px 0; padding-left: 20px;'>
                    <li>Peminjam: {$data['user_fullname']}</li>
                    <li>Tanggal: {$data['tanggal']}</li>
                    <li>Waktu Mulai: {$data['waktu_mulai']}</li>
                    <li>Waktu Selesai: {$data['waktu_selesai']}</li>
                    <li>Keperluan: {$data['keperluan']}</li>
                </ul>
            </div>

            <p style='text-align: center; margin: 30px 0;'>
                <a href='http://manajemenaset.pu.go.id/admin/dashboard' 
                   style='display: inline-block; padding: 12px 24px; background-color: #007bff; color: white; text-decoration: none; border-radius: 4px;'>
                    Verifikasi Sekarang
                </a>
            </p>
        ";

        $attachments = !empty($data['surat_permohonan']) ? [$data['surat_permohonan']] : [];
        $message = getEmailTemplate($content);
        sendEmail($adminEmail, $subject, $message, $attachments);

    } elseif ($type === 'verified') {
        $status = $data['status'];
        $subject = "Peminjaman Barang " . ucfirst($status);

        if ($status === 'disetujui') {
            $content = "
                <p>Selamat {$sapaan}, {$data['user_fullname']}.</p>
                <p>Pengajuan peminjaman barang <strong>" . ($data['nama_barang'] ?? '-') . "</strong> telah <span style='color: #28a745;'>DISETUJUI</span>.</p>
            ";
        } else {
            $content = "
                <p>Selamat {$sapaan}, {$data['user_fullname']}.</p>
                <p>Mohon maaf, pengajuan peminjaman barang <strong>" . ($data['nama_barang'] ?? '-') . "</strong> <span style='color: #dc3545;'>DITOLAK</span>.</p>
                <p><strong>Alasan:</strong> {$data['keterangan']}</p>
            ";
        }

        $attachments = [];
        if (!empty($data['dokumen_tambahan'])) $attachments[] = $data['dokumen_tambahan'];
        $message = getEmailTemplate($content);
        sendEmail($userEmail, $subject, $message, $attachments);
    }
}

function sendBarangPengembalianNotification($data, $type = 'new') {
    $adminEmail = 'idampalada@if.uai.ac.id';
    $userEmail = $data['user_email'];
    $sapaan = getSapaan();

    if ($type === 'new') {
        $subject = 'Pengajuan Pengembalian Barang';
        $content = "
            <p>Selamat {$sapaan},</p>
            
            <p>Pengembalian barang <strong>" . ($data['nama_barang'] ?? '-') . "</strong> telah diajukan oleh <strong>{$data['user_fullname']}</strong>.</p>
            
            <div style='background-color: #f8f9fa; padding: 15px; border-radius: 4px; margin: 20px 0;'>
                <strong>Detail Pengembalian:</strong>
                <ul style='margin: 10px 0; padding-left: 20px;'>
                    <li>Tanggal Pinjam: " . (!empty($data['tanggal']) ? date('d/m/Y', strtotime($data['tanggal'])) : '-') . "</li>
                    <li>Waktu Selesai: " . ($data['waktu_selesai'] ?? '-') . "</li>
                    <li>Keperluan: " . ($data['keperluan'] ?? '-') . "</li>
                </ul>
            </div>

            <p style='text-align: center; margin: 30px 0;'>
                <a href='http://manajemenaset.pu.go.id/admin/dashboard' 
                style='display: inline-block; padding: 12px 24px; background-color: #007bff; color: white; text-decoration: none; border-radius: 4px;'>
                    Verifikasi Sekarang
                </a>
            </p>
        ";

        $message = getEmailTemplate($content);
        sendEmail($adminEmail, $subject, $message);

    } elseif ($type === 'verified') {
        $status = $data['status'];
        $subject = "Pengembalian Barang " . ucfirst($status);

        $statusText = $status === 'disetujui' ? 'DISETUJUI' : 'TIDAK DISETUJUI';
        $statusColor = $status === 'disetujui' ? '#28a745' : '#dc3545';

        $content = "
            <p>Selamat {$sapaan}, {$data['user_fullname']}.</p>
            
            <p>Pengembalian barang <strong>" . ($data['nama_barang'] ?? '-') . "</strong> telah <span style='color: {$statusColor};'><strong>{$statusText}</strong></span>.</p>
            
            <div style='background-color: #f8f9fa; padding: 15px; border-radius: 4px; margin: 20px 0;'>
                <strong>Detail Pengembalian:</strong>
                <ul style='margin: 10px 0; padding-left: 20px;'>
                    <li>Tanggal Pinjam: " . (!empty($data['tanggal']) ? date('d/m/Y', strtotime($data['tanggal'])) : '-') . "</li>
                    <li>Waktu Selesai: " . ($data['waktu_selesai'] ?? '-') . "</li>
                </ul>
            </div>";

        if ($status === 'ditolak' && !empty($data['keterangan'])) {
            $content .= "
                <div style='background-color: #fff3cd; padding: 15px; border-radius: 4px; margin: 20px 0;'>
                    <strong>Alasan Penolakan:</strong>
                    <p style='margin: 10px 0;'>{$data['keterangan']}</p>
                </div>";
        }

        $content .= "
            <p>Jika ada pertanyaan lebih lanjut, mohon hubungi nomor dibawah ini:</p>
            <p>081578732756 - <strong>Domenico Adi Nugroho</strong></p>
        ";

        $message = getEmailTemplate($content);
        sendEmail($userEmail, $subject, $message);
    }
}

