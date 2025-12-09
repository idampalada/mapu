<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Surat Jalan Kendaraan Dinas Fungsional</title>

<style>
    body {
        font-family: "Times New Roman", serif;
        font-size: 11pt;
        line-height: 1.15;
        margin: 12px 22px;
        color: #000;
    }

    p { margin: 2px 0; }

    /* HEADER */
    .header {
        text-align: center;
        position: relative;
        min-height: 60px;
        border-bottom: 1px solid #000;
        padding-bottom: 4px;
        margin-bottom: 6px;
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

    .header-text h1 {
        margin: 0;
        font-size: 12pt;
        font-weight: bold;
        text-transform: uppercase;
    }

    .header-text h2 {
        margin: 0;
        font-size: 11pt;
        font-weight: bold;
        text-transform: uppercase;
    }

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
        margin: 8px 0 3px 0;
    }

    .letter-number {
        text-align: center;
        font-size: 11pt;
        margin-bottom: 16px;
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

    .indent { padding-left: 20px; }

    ol li {
        margin: 1px 0;
        text-align: justify;
    }

    /* SIGNATURE */
    .signature-date {
        text-align: right;
        margin-top: 18px;
        margin-bottom: 25px;
    }

    .signature {
        width: 100%;
        margin-top: 8px;
        font-size: 11pt;
    }

    .signature-left,
    .signature-right {
        width: 50%;
        float: left;
        text-align: center;
    }

    .signature-name {
        margin-top: 90px;
        text-decoration: underline;
        font-weight: bold;
    }

    .clear { clear: both; }
    .before-ketentuan {
    margin-top: 10px;  /* Atur spasi sesuai kebutuhan */
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
            Jl. Pattimura Nomor 20, Kebayoran Baru, Jakarta Selatan 12110<br>
            Telepon (021) 7392681
        </div>
    </div>
</div>

<!-- TITLE -->
<div class="letter-title">SURAT JALAN KENDARAAN DINAS FUNGSIONAL</div>

<div class="letter-number">
    NOMOR: <?= $nomor_surat ?? '.......................' ?>
</div>

<!-- CONTENT -->
<p>Dalam rangka penggunaan Kendaraan Dinas Fungsional pada Satuan Kerja <?= $unit_organisasi ?> Kementerian PUPR, dengan ini:</p>

<table>
    <tr><td width="150">Nama</td> <td>: <?= $nama_penanggung_jawab ?></td></tr>
    <tr><td>NIP / NRP</td> <td>: <?= $nip_nrp ?></td></tr>
    <tr><td>Pangkat / Golongan</td> <td>: <?= $pangkat_golongan ?></td></tr>
    <tr><td>Jabatan</td> <td>: <?= $jabatan ?></td></tr>
</table>

<p style="text-align:center; font-weight:bold; margin:12px 0 10px 0;">DIIZINKAN</p>


<p>untuk memakai 1 (satu) unit kendaraan dinas fungsional dalam rangka melaksanakan tugas kedinasan:</p>
<p class="indent"><?= $urusan_kedinasan ?></p>

<p>
    mulai tanggal <?= date("d F Y", strtotime($tanggal_mulai)) ?> Jam <?= $jam_mulai ?>
    sampai tanggal <?= date("d F Y", strtotime($tanggal_selesai)) ?> Jam <?= $jam_selesai ?>.
</p>

<p>Data Kendaraan Dinas Fungsional:</p>

<table>
    <tr><td width="150">Kode Barang</td> <td>: <?= $kode_barang ?></td></tr>
    <tr><td>NUP</td> <td>: <?= $nup ?></td></tr>
    <tr><td>Nomor Polisi</td> <td>: <?= $no_polisi ?></td></tr>
    <tr><td>Merk / Type</td> <td>: <?= $merk ?></td></tr>
</table>

<p class="before-ketentuan"><strong>Dengan ketentuan:</strong></p>
<ol>
    <li>Pemakai bertanggung jawab atas keamanan kendaraan selama pemakaian;</li>
    <li>Pemakai bertanggung jawab atas kehilangan dan bersedia dikenakan Tuntutan Ganti Rugi sesuai dengan ketentuan perundang-undangan;</li>
    <li>Kendaraan Dinas Fungsional hanya untuk keperluan dinas/tugas, dan
        tidak dibenarkan untuk keperluan pribadi/keluarga; </li>
    <li>Pemakai bersedia mengembalikan Kendaraan Dinas kepada Satuan Kerja selaku Kuasa Pengguna Barang.</li>
</ol>

<!-- SIGNATURE -->
<div class="signature-date">
    <?= $lokasi_terbit ?>, <?= date("d F Y", strtotime($tanggal_terbit)) ?>
</div>

<div class="signature">

    <div class="signature-left">
        Pemakai Kendaraan<br>
        Dinas Fungsional
        <div class="signature-name"><?= $nama_penanggung_jawab ?></div>
        NIP: <?= $nip_nrp ?>
    </div>

    <div class="signature-right">
        Pemegang Surat<br>
        Penanggung Jawab
        <div class="signature-name"><?= $pemegang_surat ?></div>
        NIP: <?= $nip_pemegang_surat ?>
    </div>

</div>

<div class="clear"></div>

</body>
</html>
