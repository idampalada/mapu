<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Jalan Kendaraan Dinas Fungsional</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            margin: 0;
            padding: 20px;
            color: #000;
        }

        /* HEADER SAME AS PERMOHONAN & PENANGGUNG JAWAB */
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
            text-transform: uppercase;
        }

        .header-text h2 {
            font-size: 16pt;
            font-weight: bold;
            margin: 0 0 10px 0;
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

        .content { margin-bottom: 20px; }
        table { width: 100%; }
        table td { padding: 4px 0; vertical-align: top; }
        .indent { padding-left: 20px; }

        .signature {
            width: 100%;
            margin-top: 40px;
        }

        .signature-left, .signature-right {
            width: 50%;
            float: left;
            text-align: center;
        }

        .signature-name {
            margin-top: 60px;
            text-decoration: underline;
            font-weight: bold;
        }

        .clear { clear: both; }
    </style>
</head>
<body>

<!-- HEADER / KOP SURAT -->
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

<!-- TITLE -->
<div class="letter-title">SURAT JALAN KENDARAAN DINAS FUNGSIONAL</div>

<div class="letter-number">
    NOMOR: <?= isset($nomor_surat) ? htmlspecialchars($nomor_surat) : '.................................................' ?>
</div>

<!-- CONTENT -->
<div class="content">
    <p>Dalam rangka penggunaan Kendaraan Dinas Fungsional pada Satuan Kerja <?= $unit_organisasi ?> Kementerian PUPR, dengan ini:</p>

    <table>
        <tr><td width="150">Nama</td> <td>: <?= $nama_penanggung_jawab ?></td></tr>
        <tr><td>NIP / NRP</td> <td>: <?= $nip_nrp ?></td></tr>
        <tr><td>Pangkat / Golongan</td> <td>: <?= $pangkat_golongan ?></td></tr>
        <tr><td>Jabatan</td> <td>: <?= $jabatan ?></td></tr>
    </table>

    <p style="text-align: center; font-weight: bold;">DIIZINKAN</p>

    <p>untuk memakai 1 (satu) unit kendaraan dinas fungsional dalam rangka melaksanakan tugas kedinasan:</p>
    <p class="indent"><?= $urusan_kedinasan ?></p>

    <p>
        mulai tanggal <?= date('d F Y', strtotime($tanggal_mulai)) ?> Jam <?= $jam_mulai ?>
        sampai dengan tanggal <?= date('d F Y', strtotime($tanggal_selesai)) ?> Jam <?= $jam_selesai ?>.
    </p>

    <p>Data Kendaraan Dinas Fungsional:</p>

    <table>
        <tr><td width="150">Kode Barang</td> <td>: <?= $kode_barang ?></td></tr>
        <tr><td>NUP</td> <td>: <?= $nup ?></td></tr>
        <tr><td>Nomor Polisi</td> <td>: <?= $no_polisi ?></td></tr>
        <tr><td>Merk / Type</td> <td>: <?= $merk ?></td></tr>
    </table>

    <p>Dengan ketentuan:</p>
    <ol>
        <li>Pemakai bertanggung jawab atas keamanan kendaraan selama pemakaian;</li>
        <li>Pemakai bertanggung jawab atas kehilangan dan bersedia dikenakan Tuntutan Ganti Rugi sesuai ketentuan peraturan perundang-undangan;</li>
        <li>Kendaraan Dinas Fungsional hanya untuk keperluan dinas/tugas, dan tidak diperkenankan untuk keperluan pribadi/keluarga;</li>
        <li>Pemakai bersedia mengembalikan kendaraan kepada Satuan Kerja selaku Kuasa Pengguna Barang.</li>
    </ol>
</div>

<p style="text-align: right; margin-top: 20px;">
    <?= $lokasi_terbit ?>, <?= date('d F Y', strtotime($tanggal_terbit)) ?>
</p>

<div class="signature">
    <div class="signature-left">
        <div>Pemakai Kendaraan</div>
        <div>Dinas Fungsional</div>
        <div class="signature-name"><?= $nama_penanggung_jawab ?></div>
        <div>NIP: <?= $nip_nrp ?></div>
    </div>

    <div class="signature-right">
        <div>Pemegang Surat</div>
        <div>Penanggung Jawab</div>
        <div class="signature-name"><?= $pemegang_surat ?></div>
        <div>NIP: <?= $nip_pemegang_surat ?></div>
    </div>
</div>

<div class="clear"></div>

</body>
</html>
