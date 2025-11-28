<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            position: relative;
            min-height: 120px;
        }
        
        .logo-container {
            position: absolute;
            left: 0;
            top: 0;
            width: 100px;
            height: 100px;
            border: 2px solid #0066CC;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
        }
        
        .logo-container img {
            max-width: 90px;
            max-height: 90px;
            width: auto;
            height: auto;
        }
        
        .logo-fallback {
            font-size: 8pt;
            color: #666;
            text-align: center;
            line-height: 1.1;
        }
        
        .header-text {
            margin-left: 120px;
            text-align: left;
        }
        
        .header-text h1 {
            font-size: 16pt;
            font-weight: bold;
            margin: 0;
            margin-bottom: 5px;
            text-align: center;
        }
        
        .header-text h2 {
            font-size: 14pt;
            font-weight: bold;
            margin: 0;
            margin-bottom: 10px;
            text-align: center;
        }
        
        .header-text .address {
            font-size: 10pt;
            text-align: center;
            margin-bottom: 10px;
        }
        
        .divider {
            border-top: 3px solid #000;
            margin: 20px 0;
        }
        
        .letter-title {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            text-decoration: underline;
            margin: 30px 0;
        }
        
        .letter-number {
            text-align: center;
            font-size: 12pt;
            margin-bottom: 30px;
        }
        
        .content {
            margin: 20px 0;
            text-align: justify;
        }
        
        .data-table {
            width: 100%;
            margin: 20px 0;
        }
        
        .data-table td {
            padding: 3px 0;
            vertical-align: top;
        }
        
        .data-table .label {
            width: 25%;
        }
        
        .data-table .colon {
            width: 3%;
            text-align: center;
        }
        
        .data-table .value {
            width: 72%;
        }
        
        .permission-title {
            text-align: center;
            font-weight: bold;
            font-size: 14pt;
            margin: 30px 0 20px 0;
        }
        
        .signature-section {
            margin-top: 50px;
        }
        
        .signature-table {
            width: 100%;
        }
        
        .signature-table td {
            vertical-align: top;
            padding: 10px;
            text-align: center;
        }
        
        .signature-space {
            height: 80px;
            border-bottom: 1px solid #000;
            margin: 10px 0;
            width: 200px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .debug-info {
            position: fixed;
            bottom: 10px;
            right: 10px;
            font-size: 8pt;
            color: #666;
            background: rgba(255,255,255,0.9);
            padding: 5px;
            border: 1px solid #ccc;
        }
        
        /* Media query for PDF rendering */
        @media print {
            .debug-info {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Header dengan logo -->
    <div class="header">
        <div class="logo-container">
            <?php if (isset($logo_data) && !empty($logo_data)): ?>
                <img src="<?= htmlspecialchars($logo_data) ?>" alt="Logo PUPR">
            <?php else: ?>
                <div class="logo-fallback">
                    <div style="font-weight: bold; color: #0066CC;">REPUBLIK</div>
                    <div style="font-weight: bold; color: #0066CC;">INDONESIA</div>
                    <div style="font-size: 10pt; font-weight: bold; color: #0066CC;">PUPR</div>
                    <div style="color: #999; font-size: 7pt;">NO LOGO</div>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="header-text">
            <h1>KEMENTERIAN PEKERJAAN UMUM</h1>
            <h2>SEKRETARIAT JENDERAL</h2>
            <div class="address">
                Jl. Pattimura Nomor 20, Selong, Kebayoran Baru, Jakarta Selatan, DKI Jakarta 12110, Telepon (021) 7392681
            </div>
        </div>
    </div>
    
    <div class="divider"></div>
    
    <!-- Judul Surat -->
    <div class="letter-title">
        SURAT IZIN PEMAKAIAN KENDARAAN DINAS
    </div>
    
    <div class="letter-number">
        NOMOR: <?= isset($nomor_surat) ? htmlspecialchars($nomor_surat) : '.............................' ?>
    </div>
    
    <!-- Content -->
    <div class="content">
        <p>Dalam rangka penggunaan Kendaraan Dinas pada Satuan Kerja Setjen Kementerian PUPR, dengan ini:</p>
        
        <table class="data-table">
            <tr>
                <td class="label">Nama</td>
                <td class="colon">:</td>
                <td class="value"><?= isset($nama) ? htmlspecialchars($nama) : 'TEST' ?></td>
            </tr>
            <tr>
                <td class="label">NIP</td>
                <td class="colon">:</td>
                <td class="value"><?= isset($nip) ? htmlspecialchars($nip) : '2198198' ?></td>
            </tr>
            <tr>
                <td class="label">Pangkat/Golongan</td>
                <td class="colon">:</td>
                <td class="value"><?= isset($pangkat) ? htmlspecialchars($pangkat) : 'IV A' ?></td>
            </tr>
            <tr>
                <td class="label">Jabatan</td>
                <td class="colon">:</td>
                <td class="value"><?= isset($jabatan) ? htmlspecialchars($jabatan) : 'Kepala Biro Kepegawaian, Organisasi, dan Tata Laksana' ?></td>
            </tr>
            <tr>
                <td class="label">Alamat Rumah</td>
                <td class="colon">:</td>
                <td class="value"><?= isset($alamat) ? htmlspecialchars($alamat) : 'Ja' ?></td>
            </tr>
            <tr>
                <td class="label">No. Telp Rumah/HP</td>
                <td class="colon">:</td>
                <td class="value"><?= isset($no_telp) ? htmlspecialchars($no_telp) : '2838320' ?></td>
            </tr>
            <tr>
                <td class="label">No. KTP</td>
                <td class="colon">:</td>
                <td class="value"><?= isset($no_ktp) ? htmlspecialchars($no_ktp) : '3988923' ?></td>
            </tr>
        </table>
        
        <div class="permission-title">
            DIIZINKAN
        </div>
        
        <p>untuk memakai dan menyimpan di rumah, 1 (satu) unit Kendaraan Dinas yaitu:</p>
        
        <ul>
            <li>Jenis kendaraan: <?= isset($jenis_kendaraan) ? htmlspecialchars($jenis_kendaraan) : 'Sedan' ?></li>
            <li>Merk/Type: <?= isset($merk_type) ? htmlspecialchars($merk_type) : 'Toyota Camry' ?></li>
            <li>Nomor Polisi: <?= isset($nomor_polisi) ? htmlspecialchars($nomor_polisi) : 'B 1234 ABC' ?></li>
            <li>Tahun: <?= isset($tahun) ? htmlspecialchars($tahun) : '2020' ?></li>
            <li>Nomor Rangka: <?= isset($nomor_rangka) ? htmlspecialchars($nomor_rangka) : 'JTDBR32E300123456' ?></li>
            <li>Nomor Mesin: <?= isset($nomor_mesin) ? htmlspecialchars($nomor_mesin) : '2AR-FE123456' ?></li>
            <li>Keperluan: <?= isset($keperluan) ? htmlspecialchars($keperluan) : 'Keperluan operasional kantor dan rapat dinas' ?></li>
        </ul>
        
        <p>Surat izin ini berlaku mulai tanggal <?= isset($tanggal_mulai) ? htmlspecialchars($tanggal_mulai) : '01 Desember 2024' ?> 
        sampai dengan tanggal <?= isset($tanggal_selesai) ? htmlspecialchars($tanggal_selesai) : '31 Desember 2024' ?>.</p>
        
        <p>Demikian surat izin ini dibuat untuk dipergunakan sebagaimana mestinya.</p>
    </div>
    
    <!-- Tanda tangan -->
    <div class="signature-section">
        <table class="signature-table">
            <tr>
                <td style="width: 50%;"></td>
                <td style="width: 50%;">
                    <p>Jakarta, <?= date('d F Y') ?></p>
                    <p style="margin-bottom: 0;">Sekretaris Jenderal</p>
                    <div class="signature-space"></div>
                    <p style="margin-top: 0; font-weight: bold;">Nama Pejabat</p>
                    <p>NIP. 196X1231 198X03 1 001</p>
                </td>
            </tr>
        </table>
    </div>
    
    <!-- Debug info (for development) -->
    <div class="debug-info">
        <?php if (isset($logo_method)): ?>
            Logo Method: <?= htmlspecialchars($logo_method) ?><br>
            Logo Found: <?= isset($logo_found) ? ($logo_found ? 'Yes' : 'No') : 'Unknown' ?><br>
            <?= isset($logo_message) ? htmlspecialchars($logo_message) : '' ?>
        <?php endif; ?>
    </div>
</body>
</html>