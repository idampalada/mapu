<!DOCTYPE html>
<html>
<head>
    <title>SURAT PENANGGUNG JAWAB KENDARAAN DINAS FUNGSIONAL</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            margin: 20px;
        }
        h1, h2, h3 {
            text-align: center;
            margin: 5px 0;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid black;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .content {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            margin: 10px 0;
        }
        table td {
            padding: 3px;
            vertical-align: top;
        }
        table td:first-child {
            width: 200px;
        }
        .ketentuan {
            margin-bottom: 20px;
        }
        .ketentuan ol {
            margin-top: 5px;
            padding-left: 25px;
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
            width: 100%;
        }
        .signature-left, .signature-right {
            width: 50%;
            text-align: center;
        }
        .signature-name {
            text-decoration: underline;
            margin-top: 60px;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            font-size: 10pt;
        }
        .footer p {
            margin: 3px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>SURAT PENANGGUNG JAWAB KENDARAAN DINAS FUNGSIONAL</h2>
        <h3>KEMENTERIAN PEKERJAAN UMUM DAN PERUMAHAN RAKYAT</h3>
        <p>NOMOR: <?= isset($nomor_surat) ? $nomor_surat : '................................................' ?></p>
    </div>
    
    <div class="content">
        <p>Dalam rangka penggunaan Kendaraan Dinas Fungsional pada Satuan Kerja <?= $unit_organisasi ?> Kementerian PUPR, dengan ini:</p>
        
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