<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Penanggung Jawab KDF</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            margin: 0;
            padding: 20px;
            color: #000;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            position: relative;
            min-height: 120px;
            border-bottom: 3px solid #000;
            padding-bottom: 20px;
        }

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

        .logo-fallback {
            font-size: 8pt;
            color: #0066CC;
            text-align: center;
            line-height: 1.2;
            font-weight: bold;
            padding: 5px;
        }

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

        .letter-title {
            text-align: center;
            font-size: 16pt;
            font-weight: bold;
            text-decoration: underline;
            margin: 30px 0 10px;
            text-transform: uppercase;
        }

        .letter-number {
            text-align: center;
            font-size: 12pt;
            margin-bottom: 30px;
            font-weight: bold;
        }

        .content {
            margin: 20px 0;
            text-align: justify;
        }

        table {
            width: 100%;
            margin: 15px 0;
            border-collapse: collapse;
        }

        table td {
            padding: 5px 0;
            vertical-align: top;
        }

        .signature-section {
            margin-top: 50px;
            page-break-inside: avoid;
        }

        .signature-date {
            text-align: right;
            margin-bottom: 30px;
        }

        .signature-container {
            display: flex;
            justify-content: space-between;
        }

        .signature-left, .signature-right {
            width: 45%;
            text-align: center;
        }

        .signature-name {
            margin-top: 60px;
            font-weight: bold;
            text-decoration: underline;
        }

        .signature-nip {
            margin-top: 5px;
        }
    </style>
</head>
<body>

<!-- Kop Surat -->
<div class="header">
    <div class="logo-container">
        <?php if (!empty($logo_data)): ?>
            <img src="<?= htmlspecialchars($logo_data) ?>" alt="Logo PUPR">
        <?php else: ?>
            <div class="logo-fallback">
                <div>REPUBLIK</div>
                <div>INDONESIA</div>
                <div style="font-size: 10pt; margin-top: 5px;">PUPR</div>
            </div>
        <?php endif; ?>
    </div>

    <div class="header-text">
        <h1>KEMENTERIAN PEKERJAAN UMUM</h1>
        <h2>SEKRETARIAT JENDERAL</h2>
        <div class="address">
            Jl. Pattimura Nomor 20, Selong, Kebayoran Baru, Jakarta Selatan, DKI Jakarta 12110<br>
            Telepon (021) 7392681
        </div>
    </div>
</div>

<!-- Judul Surat -->
<div class="letter-title">
    SURAT PENANGGUNG JAWAB KENDARAAN DINAS FUNGSIONAL
</div>

<div class="letter-number">
    NOMOR: <?= isset($nomor_surat) ? htmlspecialchars($nomor_surat) : '.................................................' ?>
</div>

<!-- Konten selanjutnya -->
<div class="content">
    <p>Dalam rangka penggunaan Kendaraan Dinas Fungsional pada Satuan Kerja <?= $unit_organisasi ?> Kementerian PUPR, dengan ini:</p>
</div>


        <table>
            <tr>
                <td>Nama</td>
                <td>: <?= $nama_penanggung_jawab ?></td>
            </tr>
            <tr>
                <td>NIP/NRP</td>
                <td>: <?= $nip_nrp ?></td>
            </tr>
            <tr>
                <td>Pangkat/Golongan</td>
                <td>: <?= $pangkat_golongan ?></td>
            </tr>
            <tr>
                <td>Jabatan</td>
                <td>: <?= $jabatan ?></td>
            </tr>
            <tr>
                <td>Alamat Rumah</td>
                <td>: <?= $alamat_rumah ?></td>
            </tr>
            <tr>
                <td>No. Telp Rumah/HP</td>
                <td>: <?= isset($no_hp) ? $no_hp : '-' ?></td>
            </tr>
            <tr>
                <td>No. KTP</td>
                <td>: <?= isset($no_ktp) ? $no_ktp : '-' ?></td>
            </tr>
        </table>
        
        <p style="text-align: center; font-weight: bold;">BERTANGGUNG JAWAB</p>
        
        <p>terhadap 1 (satu) unit Kendaraan Dinas Fungsional yaitu:</p>
        
        <table>
            <tr>
                <td>Jenis Kendaraan</td>
                <td>: <?= $jenis_kendaraan ?></td>
            </tr>
            <tr>
                <td>Nomor Polisi</td>
                <td>: <?= $no_polisi ?> (plat merah)</td>
            </tr>
            <tr>
                <td>Merk/Type</td>
                <td>: <?= $merk ?></td>
            </tr>
            <tr>
                <td>Warna</td>
                <td>: <?= $warna ?></td>
            </tr>
            <tr>
                <td>Nomor Mesin</td>
                <td>: <?= $nomor_mesin ?></td>
            </tr>
            <tr>
                <td>Nomor Rangka</td>
                <td>: <?= $no_rangka ?></td>
            </tr>
            <tr>
                <td>Kode Barang/NUP</td>
                <td>: <?= $kode_barang ?> / <?= $nup ?></td>
            </tr>
            <tr>
                <td>Tahun Pembuatan</td>
                <td>: <?= $tahun_pembuatan ?></td>
            </tr>
        </table>
        
        <div class="ketentuan">
            <p>Dengan ketentuan:</p>
            <ol>
                <li>Kendaraan Dinas hanya untuk keperluan dinas/tugas, dan tidak diperkenankan untuk keperluan pribadi/keluarga;</li>
                <li>Pemakaian Kendaraan Dinas berdasarkan Surat Jalan Kendaraan Dinas Fungsional yang ditandatangani penanggung jawab kendaraan dinas;</li>
                <li>Surat Penanggung Jawab ini berlaku selama 2 (dua) tahun sejak ditandatanganinya surat ini;</li>
                <li>Pemegang Surat Penanggung Jawab yang telah memasuki masa pensiun maka Surat Penanggung Jawab ini dinyatakan tidak berlaku lagi dan berkewajiban untuk mengembalikan kendaraan dinas;</li>
                <li>Pemegang Surat Penanggung Jawab yang di mutasi / alih tugas ke satuan kerja lain maka Surat Penanggung Jawab ini dinyatakan tidak berlaku lagi dan berkewajiban untuk mengembalikan kendaraan dinas.</li>
            </ol>
        </div>
    </div>
    
    <div class="signature-section">
        <div class="signature-date">
            <?= isset($tempat_surat) && isset($tanggal_surat) ? $tempat_surat . ', ' . date('d F Y', strtotime($tanggal_surat)) : '..............., ............................' ?>
        </div>
        
        <div class="signature-container">
    <div class="signature-left">
        <p>Penanggung Jawab<br>Kendaraan Dinas Fungsional</p>
        <p class="signature-name"><?= $nama_penanggung_jawab_kendaraan ?></p>
        <p>NIP: <?= $nip_penanggung_jawab_kendaraan ?></p>
    </div>
    <div class="signature-right">
        <p>Kepala Satuan Kerja<br>Selaku Kuasa Pengguna Barang</p>
        <p class="signature-name"><?= $nama_kepala_satuan_kerja ?></p>
        <p>NIP: <?= $nip_kepala_satuan_kerja ?></p>
    </div>
</div>
    </div>
    
    <div class="footer">
        <p>Dibuat dalam rangkap 4, yaitu:</p>
        <p>Lembar 1 untuk Satuan Kerja yang bersangkutan;</p>
        <p>Lembar 2 untuk Pemegang Kendaraan Dinas Fungsional;</p>
        <p>Lembar 3 untuk Bagian BMN Unit Oganisasi terkait;</p>
        <p>Lembar 4 untuk Biro Pengelolaan BMN Kementerian PUPR;</p>
    </div>
</body>
</html>