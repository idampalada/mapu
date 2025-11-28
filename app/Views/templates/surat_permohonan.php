<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Izin Pemakaian Kendaraan Dinas</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
            color: #000;
        }
        
        /* HEADER SECTION WITH LOGO */
        .header {
            text-align: center;
            margin-bottom: 30px;
            position: relative;
            min-height: 120px;
            border-bottom: 3px solid #000;
            padding-bottom: 20px;
        }
        
        /* LOGO CONTAINER - POSISI KIRI */
        .logo-container {
            position: absolute;
            left: 0;
            top: 0;
            width: 100px;
            height: 100px;
            border: 1px solid #ddd;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        
        .logo-container img {
            max-width: 90px;
            max-height: 90px;
            object-fit: contain;
        }
        
        /* FALLBACK LOGO STYLING */
        .logo-fallback {
            font-size: 8pt;
            color: #0066CC;
            text-align: center;
            line-height: 1.2;
            font-weight: bold;
            padding: 5px;
        }
        
        /* HEADER TEXT - MARGIN KIRI UNTUK LOGO */
        .header-text {
            margin-left: 120px;
            text-align: center;
        }
        
        .header-text h1 {
            font-size: 18pt;
            font-weight: bold;
            margin: 0 0 5px 0;
            color: #000;
            text-transform: uppercase;
        }
        
        .header-text h2 {
            font-size: 16pt;
            font-weight: bold;
            margin: 0 0 10px 0;
            color: #000;
            text-transform: uppercase;
        }
        
        .header-text .address {
            font-size: 11pt;
            margin: 10px 0;
            line-height: 1.3;
        }
        
        /* JUDUL SURAT */
        .letter-title {
            text-align: center;
            font-size: 16pt;
            font-weight: bold;
            text-decoration: underline;
            margin: 30px 0;
            text-transform: uppercase;
        }
        
        .letter-number {
            text-align: center;
            font-size: 12pt;
            margin-bottom: 30px;
            font-weight: bold;
        }
        
        /* CONTENT STYLING */
        .content {
            margin: 20px 0;
            text-align: justify;
        }
        
        .data-table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }
        
        .data-table td {
            padding: 5px 0;
            vertical-align: top;
            border: none;
        }
        
        .data-table .label {
            width: 25%;
            font-weight: normal;
        }
        
        .data-table .colon {
            width: 3%;
            text-align: center;
        }
        
        .data-table .value {
            width: 72%;
            font-weight: normal;
        }
        
        .permission-section {
            text-align: center;
            font-weight: bold;
            font-size: 14pt;
            margin: 30px 0;
            text-transform: uppercase;
        }
        
        .vehicle-details {
            margin: 20px 0;
        }
        
        .ketentuan {
            margin: 20px 0;
        }
        
        .ketentuan ol {
            padding-left: 20px;
        }
        
        .ketentuan li {
            margin: 8px 0;
            text-align: justify;
        }
        
        /* SIGNATURE SECTION - FIXED ALIGNMENT */
        .signature-section {
            margin-top: 50px;
            page-break-inside: avoid;
        }
        
        .signature-date {
            text-align: right;
            margin-bottom: 40px;
            font-weight: normal;
        }
        
        .signature-container {
            width: 100%;
            display: table;
            table-layout: fixed;
        }
        
        .signature-left {
            width: 50%;
            display: table-cell;
            text-align: center;
            vertical-align: top;
            padding-right: 20px;
        }
        
        .signature-right {
            width: 50%;
            display: table-cell;
            text-align: center;
            vertical-align: top;
            padding-left: 20px;
        }
        
        .signature-title {
            font-weight: normal;
            margin-bottom: 10px;
            line-height: 1.3;
        }
        
        .signature-space {
            height: 80px;
            margin: 15px 0;
        }
        
        .signature-name {
            text-decoration: underline;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .signature-nip {
            font-weight: normal;
            margin-top: 5px;
        }
        
        /* REMOVE DEBUG INFO COMPLETELY */
        .debug-info {
            display: none !important;
        }
    </style>
</head>
<body>
    <!-- HEADER DENGAN LOGO -->
    <div class="header">
        <!-- LOGO CONTAINER -->
        <div class="logo-container">
            <?php if (isset($logo_data) && !empty($logo_data)): ?>
                <img src="<?= htmlspecialchars($logo_data) ?>" alt="Logo PUPR" title="Logo Kementerian PUPR">
            <?php else: ?>
                <div class="logo-fallback">
                    <div>REPUBLIK</div>
                    <div>INDONESIA</div>
                    <div style="font-size: 10pt; margin-top: 5px;">PUPR</div>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- HEADER TEXT -->
        <div class="header-text">
            <h1>KEMENTERIAN PEKERJAAN UMUM</h1>
            <h2>SEKRETARIAT JENDERAL</h2>
            <div class="address">
                Jl. Pattimura Nomor 20, Selong, Kebayoran Baru, Jakarta Selatan, DKI Jakarta 12110<br>
                Telepon (021) 7392681
            </div>
        </div>
    </div>
    
    <!-- JUDUL SURAT -->
    <div class="letter-title">
        SURAT IZIN PEMAKAIAN KENDARAAN DINAS
    </div>
    
    <div class="letter-number">
        NOMOR: <?= isset($nomor_surat) ? htmlspecialchars($nomor_surat) : '.................' ?>
    </div>
    
    <!-- CONTENT -->
    <div class="content">
        <p>Dalam rangka penggunaan Kendaraan Dinas pada Satuan Kerja Setjen Kementerian PUPR, dengan ini:</p>
        
        <!-- DATA PEMINJAM -->
        <table class="data-table">
            <tr>
                <td class="label">Nama</td>
                <td class="colon">:</td>
                <td class="value"><strong><?= isset($nama_penanggung_jawab) ? htmlspecialchars($nama_penanggung_jawab) : 'Tidak diketahui' ?></strong></td>
            </tr>
            <tr>
                <td class="label">NIP</td>
                <td class="colon">:</td>
                <td class="value"><?= isset($nip_nrp) ? htmlspecialchars($nip_nrp) : 'Tidak diketahui' ?></td>
            </tr>
            <tr>
                <td class="label">Pangkat/Golongan</td>
                <td class="colon">:</td>
                <td class="value"><?= isset($pangkat_golongan) ? htmlspecialchars($pangkat_golongan) : 'Tidak diketahui' ?></td>
            </tr>
            <tr>
                <td class="label">Jabatan</td>
                <td class="colon">:</td>
                <td class="value"><?= isset($jabatan) ? htmlspecialchars($jabatan) : 'Tidak diketahui' ?></td>
            </tr>
            <tr>
                <td class="label">Unit Organisasi</td>
                <td class="colon">:</td>
                <td class="value"><?= isset($unit_organisasi) ? htmlspecialchars($unit_organisasi) : 'Tidak diketahui' ?></td>
            </tr>
            <tr>
                <td class="label">Alamat Rumah</td>
                <td class="colon">:</td>
                <td class="value"><?= isset($alamat_rumah) ? htmlspecialchars($alamat_rumah) : 'Tidak diketahui' ?></td>
            </tr>
            <tr>
                <td class="label">No. Telp/HP</td>
                <td class="colon">:</td>
                <td class="value"><?= isset($no_hp) ? htmlspecialchars($no_hp) : 'Tidak diketahui' ?></td>
            </tr>
            <tr>
                <td class="label">No. KTP</td>
                <td class="colon">:</td>
                <td class="value"><?= isset($no_ktp) ? htmlspecialchars($no_ktp) : 'Tidak diketahui' ?></td>
            </tr>
        </table>
        
        <!-- SECTION IZIN -->
        <div class="permission-section">
            DIIZINKAN
        </div>
        
        <p>untuk memakai dan menyimpan di rumah, 1 (satu) unit Kendaraan Dinas yaitu:</p>
        
        <!-- DATA KENDARAAN -->
        <table class="data-table">
            <tr>
                <td class="label">Jenis Kendaraan</td>
                <td class="colon">:</td>
                <td class="value"><?= isset($jenis_kendaraan) ? htmlspecialchars($jenis_kendaraan) : 'Kendaraan Dinas' ?></td>
            </tr>
            <tr>
                <td class="label">Merk/Type</td>
                <td class="colon">:</td>
                <td class="value"><?= isset($merk) ? htmlspecialchars($merk) : 'Tidak diketahui' ?></td>
            </tr>
            <tr>
                <td class="label">Nomor Polisi</td>
                <td class="colon">:</td>
                <td class="value"><?= isset($no_polisi) ? htmlspecialchars($no_polisi) : 'Tidak diketahui' ?> (plat merah)</td>
            </tr>
            <tr>
                <td class="label">Tahun Pembuatan</td>
                <td class="colon">:</td>
                <td class="value"><?= isset($tahun_pembuatan) ? htmlspecialchars($tahun_pembuatan) : 'Tidak diketahui' ?></td>
            </tr>
            <tr>
                <td class="label">Warna</td>
                <td class="colon">:</td>
                <td class="value"><?= isset($warna) ? htmlspecialchars($warna) : 'Tidak diketahui' ?></td>
            </tr>
            <tr>
                <td class="label">Nomor Rangka</td>
                <td class="colon">:</td>
                <td class="value"><?= isset($no_rangka) ? htmlspecialchars($no_rangka) : 'Tidak diketahui' ?></td>
            </tr>
            <tr>
                <td class="label">Nomor Mesin</td>
                <td class="colon">:</td>
                <td class="value"><?= isset($nomor_mesin) ? htmlspecialchars($nomor_mesin) : 'Tidak diketahui' ?></td>
            </tr>
            <tr>
                <td class="label">NUP</td>
                <td class="colon">:</td>
                <td class="value"><?= isset($nup) ? htmlspecialchars($nup) : 'Tidak diketahui' ?></td>
            </tr>
            <tr>
                <td class="label">Kode Barang</td>
                <td class="colon">:</td>
                <td class="value"><?= isset($kode_barang) ? htmlspecialchars($kode_barang) : 'Tidak diketahui' ?></td>
            </tr>
            <tr>
                <td class="label">Keperluan</td>
                <td class="colon">:</td>
                <td class="value"><?= isset($urusan_kedinasan) ? htmlspecialchars($urusan_kedinasan) : 'Keperluan dinas' ?></td>
            </tr>
        </table>
        
        <!-- MASA BERLAKU -->
        <p>Surat izin ini berlaku mulai tanggal <strong><?= isset($tanggal_pinjam) ? date('d F Y', strtotime($tanggal_pinjam)) : '[Tanggal Mulai]' ?></strong> 
        sampai dengan tanggal <strong><?= isset($tanggal_kembali) ? date('d F Y', strtotime($tanggal_kembali)) : '[Tanggal Selesai]' ?></strong>.</p>
        
        <!-- KETENTUAN -->
        <div class="ketentuan">
            <p><strong>Dengan ketentuan:</strong></p>
            <ol>
                <li>Izin bersifat sementara dan akan disesuaikan dengan kebutuhan dinas dan penugasan pejabat yang bersangkutan;</li>
                <li>Pemakai bertanggung jawab atas kehilangan dan bersedia dikenakan Tuntutan Ganti Rugi sesuai dengan ketentuan peraturan perundang-undangan;</li>
                <li>Kendaraan Dinas hanya untuk keperluan dinas/tugas, dan tidak diperkenankan digunakan untuk keperluan pribadi atau dipindahtangankan;</li>
                <li>Pemakai wajib mematuhi peraturan lalu lintas dan bertanggung jawab penuh atas segala risiko selama penggunaan kendaraan.</li>
            </ol>
        </div>
        
        <p>Demikian surat izin ini dibuat untuk dipergunakan sebagaimana mestinya.</p>
    </div>
    
    <!-- TANDA TANGAN - FIXED ALIGNMENT -->
    <div class="signature-section">
        <div class="signature-date">
            <?= isset($tempat_surat) ? htmlspecialchars($tempat_surat) : 'Jakarta' ?>, <?= isset($tanggal_surat) ? date('d F Y', strtotime($tanggal_surat)) : date('d F Y') ?>
        </div>
        
        <div class="signature-container">
            <div class="signature-left">
                <div class="signature-title">Pemakai Kendaraan Dinas</div>
                <div class="signature-space"></div>
                <div class="signature-name"><?= isset($nama_penanggung_jawab) ? htmlspecialchars($nama_penanggung_jawab) : '________________________' ?></div>
                <div class="signature-nip">NIP. <?= isset($nip_nrp) ? htmlspecialchars($nip_nrp) : '___________________' ?></div>
            </div>
            
            <div class="signature-right">
                <div class="signature-title">Sekretaris Jenderal<br>Selaku Kuasa Pengguna Barang</div>
                <div class="signature-space"></div>
                <div class="signature-name"><?= isset($nama_kepala_satuan_kerja) && !empty($nama_kepala_satuan_kerja) ? htmlspecialchars($nama_kepala_satuan_kerja) : '________________________' ?></div>
                <div class="signature-nip">NIP. <?= isset($nip_kepala_satuan_kerja) && !empty($nip_kepala_satuan_kerja) ? htmlspecialchars($nip_kepala_satuan_kerja) : '___________________' ?></div>
            </div>
        </div>
    </div>
</body>
</html>