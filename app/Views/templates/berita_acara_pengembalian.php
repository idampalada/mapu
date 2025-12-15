<!DOCTYPE html>
<html>
<head>
<title>BERITA ACARA PENGEMBALIAN</title>

<style>
    body {
        font-family: "Times New Roman", serif;
        font-size: 11pt;
        line-height: 1.2;
        margin: 12px 22px;
        color: #000;
    }

    p { margin: 3px 0; }

    /* --- KOP SURAT --- */
    .header {
        text-align: center;
        position: relative;
        min-height: 60px;
        border-bottom: 1px solid #000;
        padding-bottom: 4px;
        margin-bottom: 10px;
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
    .header-text h1 { margin: 0; font-size: 12pt; font-weight: bold; text-transform: uppercase; }
    .header-text h2 { margin: 0; font-size: 11pt; font-weight: bold; text-transform: uppercase; }
    .address { font-size: 9pt; margin-top: 3px; line-height: 1.1; }

    /* TITLE */
    .ba-title {
        text-align: center;
        font-weight: bold;
        text-decoration: underline;
        margin: 10px 0 3px;
        font-size: 12pt;
    }
    .ba-number {
        text-align: center;
        margin-bottom: 10px;
    }

    /* TABLES */
    table { width: 100%; border-collapse: collapse; }
    table.no-border td {
        padding: 2px 0;
        vertical-align: top;
        font-size: 11pt;
    }
    .detail-table td {
        border: 1px solid #000;
        padding: 4px;
        font-size: 10.5pt;
    }
    .detail-table th {
        background-color: #f0f0f0;
        border: 1px solid #000;
        padding: 5px;
    }

    /* SIGNATURE */
    .signature {
        width: 100%;
        margin-top: 25px;
    }
    .signature-left, .signature-right {
        width: 48%;
        float: left;
        text-align: center;
    }
    .signature-right { float: right; }
    .signature-name {
        margin-top: 50px;
        text-decoration: underline;
        font-weight: bold;
    }
    .clear { clear: both; }

    /* FOTO */
    .image-container {
        text-align: center;
        margin: 10px 0;
    }
    .image-container img {
        max-width: 70%;
        border: 1px solid #ccc;
    }
    .timestamp {
        font-size: 9pt;
        color: #555;
        margin-top: 4px;
    }

    /* OPTIMIZER */
    h3 { margin: 8px 0 4px; }
    .page-break {
    page-break-before: always;
}
</style>

</head>
<body>

<!-- KOP SURAT -->
<div class="header">
    <div class="logo-container">
    <?php if (!empty($logo_data)): ?>
        <img src="<?= $logo_data ?>" style="width:55px;" />
    <?php else: ?>
        <span style="color:red;">LOGO TIDAK ADA</span>
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
<div class="ba-title">BERITA ACARA SERAH TERIMA PENGEMBALIAN KENDARAAN DINAS</div>
<div class="ba-number">NOMOR : <?= $nomor_surat ?></div>

<!-- CONTENT -->
<div class="content">
<p>Pada hari ini <?= date('d F Y', strtotime($tanggal_pengembalian)) ?>, kami yang bertanda tangan di bawah ini:</p>

<table class="no-border">
    <tr><td width="32%">Nama</td><td width="3%">:</td><td><?= $nama_penanggung_jawab ?></td></tr>
    <tr><td>NIP / NRP</td><td>:</td><td><?= $nip_nrp ?></td></tr>
    <tr><td>Pangkat / Golongan</td><td>:</td><td><?= $pangkat_golongan ?></td></tr>
    <tr><td>Jabatan</td><td>:</td><td><?= $jabatan ?></td></tr>
</table>

<p><strong>PIHAK KESATU</strong></p>

<table class="no-border">
    <tr><td width="32%">Nama</td><td width="3%">:</td><td><?= $pihak_kedua_nama ?></td></tr>
    <tr><td>NIP</td><td>:</td><td><?= $pihak_kedua_nip ?></td></tr>
    <tr><td>Jabatan</td><td>:</td><td><?= $pihak_kedua_jabatan ?></td></tr>
</table>

<p><strong>PIHAK KEDUA</strong></p>

<p>Melakukan serah terima <strong>PENGEMBALIAN</strong> Kendaraan Dinas dengan penjelasan sebagai berikut :</p>

<h3>Pasal 1</h3>
<p>PIHAK KESATU menyerahkan kepada PIHAK KEDUA dan PIHAK KEDUA
menyatakan menerima dari PIHAK KESATU Kendaraan Dinas sebagai
berikut :</p>

<table class="detail-table">
    <tr><td>Nomor SIP / Penanggung Jawab</td><td><?= $nomor_sip ?></td></tr>
    <tr><td>Jenis Kendaraan</td><td><?= $kategori_id ?></td></tr>
    <tr><td>Nomor Polisi</td><td><?= $no_polisi ?></td></tr>
    <tr><td>Kode Barang / NUP</td><td><?= $kode_barang ?> / <?= $nup ?></td></tr>
    <tr><td>Tahun Pembuatan</td><td><?= $tahun_pembuatan ?></td></tr>
    <tr><td>Merk / Type</td><td><?= $merk ?></td></tr>
    <tr><td>Warna</td><td><?= $warna ?></td></tr>
    <tr><td>Nomor STNK</td><td><?= $no_stnk ?></td></tr>
    <tr><td>Nomor BPKB</td><td><?= $no_bpkb ?></td></tr>
    <tr><td>Nomor Mesin</td><td><?= $nomor_mesin ?></td></tr>
    <tr><td>Nomor Rangka</td><td><?= $no_rangka ?></td></tr>
    <tr><td>Kondisi Kendaraan</td><td><?= $kondisi_kembali ?></td></tr>
</table>

<h3>Pasal 2</h3>
<p>Penyerahan sebagaimana dimaksud dalam Pasal 1 berupa:</p>
<ol>
    <li>Kendaraan sebagaimana dimaksud dalam Pasal 1</li>
    <li>STNK kendaraan sebagaimana dimaksud dalam Pasal 1</li>
    <li>Kunci kendaraan sebagaimana dimaksud dalam Pasal 1</li>
    <li>Kelengkapan berupa ...,...,..., dan ...</li>
</ol>

<div class="page-break"></div> <!-- Tambahkan ini agar pindah halaman -->
<div class="image-container">
    <strong>Foto Kendaraan Saat Pengembalian:</strong><br>
    <?php if (!empty($foto_pengembalian)): ?>
        <img src="<?= 'data:image/jpeg;base64,' . base64_encode(@file_get_contents(ROOTPATH.'public/uploads/images/'.$foto_pengembalian)) ?>">
    <?php else: ?>
        <p>[Tidak ada foto]</p>
    <?php endif; ?>
    <div class="timestamp">Timestamp: <?= date('d/m/Y H:i:s') ?></div>
</div>

<!-- Bagian Keterlambatan - Hanya tampil jika terlambat -->
        <?php if (!empty($alasan_keterlambatan)): ?>
<div style="margin-top: 20px; border: 1px solid #000; padding: 10px;">
    <h3>KETERLAMBATAN PENGEMBALIAN</h3>
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="width: 30%; padding: 5px;">Jumlah Hari Terlambat</td>
            <td style="width: 70%; padding: 5px;">: <?= $daysLate ?? '-' ?> hari</td>
        </tr>
        <tr>
            <td style="padding: 5px;">Alasan Keterlambatan</td>
            <td style="padding: 5px;">: <?= $alasan_keterlambatan ?></td>
        </tr>
    </table>
    
    <!-- HAPUS SELURUH BAGIAN INI (DOKUMENTASI FOTO): -->
    <?php /* 
    <?php if (!empty($foto_keterlambatan)): ?>
    <div style="margin-top: 10px;">
        <p><strong>Dokumentasi Keterlambatan:</strong></p>
        <img src="<?= 'data:image/jpeg;base64,' . base64_encode(@file_get_contents(ROOTPATH . 'public/uploads/images/' . $foto_keterlambatan)) ?>" 
             alt="Dokumentasi Keterlambatan" style="max-width: 300px; height: auto;">
        <div style="margin-top: 5px; font-size: 10px; color: #666;">
            Timestamp: <?= date('d/m/Y H:i:s') ?>
        </div>
    </div>
    <?php endif; ?>
    */ ?>
</div>
<?php endif; ?>
        
        <p>Dengan adanya Serah Terima ini maka selanjutnya tanggung jawab Kendaraan Dinas tersebut beralih dari PIHAK KESATU kepada PIHAK KEDUA.</p>
<!-- SIGNATURE -->
<div class="signature">
    <div class="signature-left">
        <p>PIHAK KESATU</p>
        <div class="signature-name"><?= $nama_penanggung_jawab ?></div>
        <div>NIP/NRP. <?= $nip_nrp ?></div>
    </div>

    <div class="signature-right">
        <p>PIHAK KEDUA</p>
        <div class="signature-name"><?= $pihak_kedua_nama ?></div>
        <div>NIP. <?= $pihak_kedua_nip ?></div>
    </div>

    <div class="clear"></div>
</div>

</div>

</body>
</html>
