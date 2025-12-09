<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Izin Pemakaian Kendaraan Dinas</title>

    <style>
        body {
            font-family: "Times New Roman", serif;
            font-size: 11pt;
            line-height: 1.12;
            margin: 12px 22px;
            color: #000;
        }

        p { margin: 2px 0; }

        .header {
            text-align: center;
            position: relative;
            min-height: 55px;
            border-bottom: 1px solid #000;
            padding-bottom: 4px;
            margin-bottom: 7px;
        }

        .logo-container {
            position: absolute;
            left: 0;
            top: 0;
            width: 55px;
            height: 55px;
        }

        .logo-container img {
            max-width: 50px;
            max-height: 50px;
        }

        .header-text h1, .header-text h2 {
            margin: 0;
            font-weight: bold;
            text-transform: uppercase;
        }

        .header-text h1 { font-size: 12pt; }
        .header-text h2 { font-size: 11pt; }

        .address {
            font-size: 9pt;
            margin-top: 2px;
            line-height: 1.1;
        }

        .letter-title {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            text-decoration: underline;
            margin: 7px 0 3px 0;
        }

        .letter-number {
            text-align: center;
            font-size: 11pt;
            margin-bottom: 7px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11pt;
        }

        td {
            padding: 1px 0;
            vertical-align: top;
        }

        .permission-section {
            text-align: center;
            font-weight: bold;
            margin: 8px 0 5px 0;
            font-size: 12pt;
            text-transform: uppercase;
        }

        .ketentuan li {
            margin: 1px 0;
            text-align: justify;
        }

        /* SIGNATURE */
        .signature-section {
            margin-top: 10px;
        }

        .signature-date {
            text-align: right;
            margin-bottom: 5px;
            font-size: 11pt;
        }

        .signature-container {
            width: 100%;
            display: table;
            table-layout: fixed;
            margin-top: 10px;
        }

        .signature-left, .signature-right {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: top;
        }

        .signature-space {
            height: 50px; /* lebih pendek agar muat 1 halaman */
        }

        .signature-name {
            font-weight: bold;
            text-decoration: underline;
            margin-top: 4px;
        }
    </style>
</head>
<body>

<!-- HEADER -->
<div class="header">
    <div class="logo-container">
        <?php if (!empty($logo_data)): ?>
            <img src="<?= $logo_data ?>" alt="Logo">
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

<!-- JUDUL -->
<div class="letter-title">SURAT IZIN PEMAKAIAN KENDARAAN DINAS</div>

<div class="letter-number">
    NOMOR: <?= $nomor_surat ?? '..............' ?>
</div>

<!-- ISI -->
<p>Dalam rangka penggunaan Kendaraan Dinas pada Satuan Kerja Setjen Kementerian PUPR, dengan ini:</p>

<table>
    <tr><td width="150">Nama</td><td width="10">:</td><td><strong><?= $nama_penanggung_jawab ?? '' ?></strong></td></tr>
    <tr><td>NIP</td><td>:</td><td><?= $nip_nrp ?? '' ?></td></tr>
    <tr><td>Pangkat/Golongan</td><td>:</td><td><?= $pangkat_golongan ?? '' ?></td></tr>
    <tr><td>Jabatan</td><td>:</td><td><?= $jabatan ?? '' ?></td></tr>
    <tr><td>Unit Organisasi</td><td>:</td><td><?= $unit_organisasi ?? '' ?></td></tr>
    <tr><td>Alamat Rumah</td><td>:</td><td><?= $alamat_rumah ?? '' ?></td></tr>
    <tr><td>No. Telp/HP</td><td>:</td><td><?= $no_hp ?? '' ?></td></tr>
    <tr><td>No. KTP</td><td>:</td><td><?= $no_ktp ?? '' ?></td></tr>
</table>

<div class="permission-section">DIIZINKAN</div>

<p>untuk memakai dan menyimpan di rumah, 1 (satu) unit Kendaraan Dinas yaitu:</p>

<table>
    <tr><td width="150">Jenis Kendaraan</td><td width="10">:</td><td><?= $jenis_kendaraan ?? '' ?></td></tr>
    <tr><td>Merk/Type</td><td>:</td><td><?= $merk ?? '' ?></td></tr>
    <tr><td>Nomor Polisi</td><td>:</td><td><?= $no_polisi ?? '' ?> (plat merah)</td></tr>
    <tr><td>Tahun Pembuatan</td><td>:</td><td><?= $tahun_pembuatan ?? '' ?></td></tr>
    <tr><td>Warna</td><td>:</td><td><?= $warna ?? '' ?></td></tr>
    <tr><td>Nomor Rangka</td><td>:</td><td><?= $no_rangka ?? '' ?></td></tr>
    <tr><td>Nomor Mesin</td><td>:</td><td><?= $nomor_mesin ?? '' ?></td></tr>
    <tr><td>NUP</td><td>:</td><td><?= $nup ?? '' ?></td></tr>
    <tr><td>Kode Barang</td><td>:</td><td><?= $kode_barang ?? '' ?></td></tr>
    <tr><td>Keperluan</td><td>:</td><td><?= $urusan_kedinasan ?? '' ?></td></tr>
</table>

<p>
Surat izin ini berlaku mulai <strong><?= date("d F Y", strtotime($tanggal_pinjam ?? 'now')) ?></strong>
sampai <strong><?= date("d F Y", strtotime($tanggal_kembali ?? 'now')) ?></strong>.
</p>

<p><strong>Dengan ketentuan:</strong></p>
<ol class="ketentuan">
    <li>Izin bersifat sementara dan disesuaikan kebutuhan dinas;</li>
    <li>Pemakai bertanggung jawab atas kehilangan dan dikenakan TGR sesuai ketentuan;</li>
    <li>Kendaraan hanya untuk tugas dinas, tidak untuk keperluan pribadi;</li>
    <li>Pemakai wajib mematuhi peraturan lalu lintas dan bertanggung jawab atas risiko penggunaan kendaraan.</li>
</ol>

<p>Demikian surat izin ini dibuat untuk dipergunakan sebagaimana mestinya.</p>

<!-- TANDA TANGAN -->
<div class="signature-section">

    <div class="signature-date">
        <?= $tempat_surat ?? 'Jakarta' ?>,
        <?= date("d F Y", strtotime($tanggal_surat ?? 'now')) ?>
    </div>

    <div class="signature-container">
        <div class="signature-left">
            Pemakai Kendaraan Dinas
            <div class="signature-space"></div>
            <div class="signature-name"><?= $nama_penanggung_jawab ?? '' ?></div>
            NIP. <?= $nip_nrp ?? '' ?>
        </div>

        <div class="signature-right">
            Sekretaris Jenderal<br>Selaku Kuasa Pengguna Barang
            <div class="signature-space"></div>
            <div class="signature-name"><?= $nama_kepala_satuan_kerja ?? '' ?></div>
            NIP. <?= $nip_kepala_satuan_kerja ?? '' ?>
        </div>
    </div>

</div>

</body>
</html>
