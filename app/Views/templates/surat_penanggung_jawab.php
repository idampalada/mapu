<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Penanggung Jawab KDF</title>

    <style>
        body {
            font-family: "Times New Roman", serif;
            font-size: 11pt;
            line-height: 1.13;
            margin: 12px 22px;
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

        .header-text h1 {
            margin: 0;
            font-size: 12pt;
            font-weight: bold;
        }

        .header-text h2 {
            margin: 0;
            font-size: 11pt;
            font-weight: bold;
        }

        .address { font-size: 9pt; margin-top: 2px; }

        .letter-title {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            text-decoration: underline;
            margin: 8px 0 5px 0;
        }

        .letter-number {
            text-align: center;
            font-weight: bold;
            margin-bottom: 16px;
        }

        table { width: 100%; border-collapse: collapse; }
        td { padding: 0; font-size: 11pt; }

.bertanggung-jawab {
    text-align: center;
    font-weight: bold;
    margin: 10px 0 8px 0;
}


        .ketentuan { margin-top: 5px; text-align: justify; }
        .ketentuan ol { margin-left: 18px; padding-left: 0; }
        .ketentuan li { margin: 1px 0; }

        .signature-date {
            text-align: right;
            margin-top: 10px;
            margin-bottom: 8px;
        }

        .footer {
            margin-top: 10px;
            font-size: 10pt;
            text-align: justify;
            line-height: 1.1;
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
<div class="letter-title">SURAT PENANGGUNG JAWAB KENDARAAN DINAS FUNGSIONAL</div>

<div class="letter-number">
    NOMOR: <?= $nomor_surat ?>
</div>

<!-- KONTEN UTAMA -->
<p>Dalam rangka penggunaan Kendaraan Dinas Fungsional pada Satuan Kerja <?= $unit_organisasi ?> Kementerian PUPR, dengan ini:</p>

<table>
    <tr><td width="150">Nama</td><td width="10">:</td><td><?= $nama_penanggung_jawab ?></td></tr>
    <tr><td>NIP / NRP</td><td>:</td><td><?= $nip_nrp ?></td></tr>
    <tr><td>Pangkat / Golongan</td><td>:</td><td><?= $pangkat_golongan ?></td></tr>
    <tr><td>Jabatan</td><td>:</td><td><?= $jabatan ?></td></tr>
    <tr><td>Alamat Rumah</td><td>:</td><td><?= $alamat_rumah ?></td></tr>
    <tr><td>No. Telp Rumah / HP</td><td>:</td><td><?= $no_hp ?></td></tr>
    <tr><td>No. KTP</td><td>:</td><td><?= $no_ktp ?></td></tr>
</table>

<div class="bertanggung-jawab">BERTANGGUNG JAWAB</div>

<p>Dengan ini bertanggungjawab terhadap 1 (satu) unit Kendaraan Dinas
Fungsional yaitu :</p>

<table>
    <tr><td width="150">Jenis Kendaraan</td><td width="10">:</td><td><?= $jenis_kendaraan ?></td></tr>
    <tr><td>Nomor Polisi</td><td>:</td><td><?= $no_polisi ?></td></tr>
    <tr><td>Merk / Type</td><td>:</td><td><?= $merk ?></td></tr>
    <tr><td>Warna</td><td>:</td><td><?= $warna ?></td></tr>
    <tr><td>Nomor Mesin</td><td>:</td><td><?= $nomor_mesin ?></td></tr>
    <tr><td>Nomor Rangka</td><td>:</td><td><?= $no_rangka ?></td></tr>
    <tr><td>Kode Barang/NUP</td><td>:</td><td><?= $kode_barang ?> / <?= $nup ?></td></tr>
    <tr><td>Tahun Pembuatan</td><td>:</td><td><?= $tahun_pembuatan ?></td></tr>
</table>

<!-- KETENTUAN -->
<div class="ketentuan">
    <p><strong>Dengan ketentuan</strong></p>
    <ol>
        <li>Bersedia merawat serta menjaga kendaraan tersebut dengan baik.</li>
        <li>Kendaraan Dinas hanya untuk keperluan dinas/tugas, dan tidak diperkenankan untuk keperluan pribadi/keluarga;</li>
        <li>Pemakaian Kendaraan Dinas berdasarkan Surat Jalan Kendaraan Dinas Fungsional yang ditandatangani penanggung jawab kendaraan dinas;</li>
        <li>Surat Penanggung Jawab ini berlaku selama masa perjanjian sewa.</li>
        <li>Setelah memasuki masa pensiun maka Surat Penanggung Jawab ini dinyatakan tidak berlaku lagi dan berkewajiban untuk mengembalikan kendaraan dinas.</li>
        <li>Setelah di mutasi / alih tugas ke satuan kerja lain maka Surat Penanggung Jawab ini dinyatakan tidak berlaku lagi dan berkewajiban untuk mengembalikan kendaraan dinas.</li>
    </ol>
</div>

<!-- TANDA TANGAN -->
<div class="signature-date">
    <?= $tempat_surat ?>, <?= date("d F Y", strtotime($tanggal_surat)) ?>
</div>

<table style="width:100%; margin-top:25px; font-size:11pt;">

    <tr>
        <td style="width:50%; text-align:center;">
            Penanggung Jawab<br>
            Kendaraan Dinas Fungsional
            <div style="height:100px;"></div>
            <span style="font-weight:bold; text-decoration:underline;">
                <?= $nama_penanggung_jawab_kendaraan ?>
            </span><br>
            NIP: <?= $nip_penanggung_jawab_kendaraan ?>
        </td>

        <td style="width:50%; text-align:center;">
            Kepala Satuan Kerja<br>
            Selaku Kuasa Pengguna Barang
            <div style="height:100px;"></div>
            <span style="font-weight:bold; text-decoration:underline;">
                <?= $nama_kepala_satuan_kerja ?>
            </span><br>
            NIP: <?= $nip_kepala_satuan_kerja ?>
        </td>
    </tr>
</table>

<!-- FOOTER -->
<div class="footer" style="margin-top:25px;">
    <strong>Dibuat dalam rangkap 4, yaitu:</strong><br>
    Lembar 1 untuk Satuan Kerja yang bersangkutan;<br>
    Lembar 2 untuk Pemakai Kendaraan Dinas Fungsional;<br>
    Lembar 3 untuk Bagian BMN Unit Organisasi terkait;<br>
    Lembar 4 untuk Biro Pengelolaan BMN Kementerian PUPR;
</div>

</body>
</html>
