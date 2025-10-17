<!DOCTYPE html>
<html>
<head>
    <title>SURAT IZIN PEMAKAIAN KENDARAAN DINAS</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12pt;
            line-height: 1.5;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h2 {
            margin-bottom: 5px;
        }
        .content {
            margin-bottom: 20px;
        }
        table.data {
            width: 100%;
            border-collapse: collapse;
        }
        table.data td {
            padding: 5px;
            vertical-align: top;
        }
        table.data td:first-child {
            width: 200px;
        }
        .footer {
            margin-top: 40px;
            page-break-inside: avoid;
        }
        .signature-date {
            text-align: right;
            margin-bottom: 30px;
        }
        .signature-container {
            width: 100%;
            display: table;
        }
        .signature-left {
            width: 50%;
            display: table-cell;
            text-align: center;
            vertical-align: top;
        }
        .signature-right {
            width: 50%;
            display: table-cell;
            text-align: center;
            vertical-align: top;
        }
        .signature-name {
            text-decoration: underline;
            margin-top: 60px;
            font-weight: bold;
        }
        .clear {
            clear: both;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>SURAT IZIN PEMAKAIAN KENDARAAN DINAS</h2>
        <h3>KEMENTERIAN PEKERJAAN UMUM DAN PERUMAHAN RAKYAT</h3>
        <p>NOMOR: <?= isset($nomor_surat) ? $nomor_surat : '................................................' ?></p>
    </div>
    
    <div class="content">
        <p>Dalam rangka penggunaan Kendaraan Dinas pada Satuan Kerja 
        <?= $unit_organisasi ?> Kementerian PUPR, dengan ini:</p>
        
        <table class="data">
            <tr>
                <td>Nama</td>
                <td>: <?= $nama_penanggung_jawab ?></td>
            </tr>
            <tr>
                <td>NIP</td>
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
                <td>: <?= $no_hp ?></td>
            </tr>
            <tr>
                <td>No. KTP</td>
                <td>: <?= $no_ktp ?></td>
            </tr>
        </table>
        
        <p style="text-align: center; margin: 20px 0;">DIIZINKAN</p>
        
        <p>untuk memakai dan menyimpan di rumah, 1 (satu) unit Kendaraan Dinas yaitu:</p>
        
        <table class="data">
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
        
        <p>Dengan ketentuan:</p>
        <ol>
            <li>Izin bersifat sementara dan akan disesuaikan dengan kebutuhan dinas dan penugasan pejabat yang bersangkutan;</li>
            <li>Pemakai bertanggung jawab atas kehilangan bersedia dikenakan Tuntutan Ganti Rugi sesuai dengan ketentuan peraturan perundang-undangan;</li>
            <li>Kendaraan Dinas hanya untuk keperluan dinas/tugas, dan tidak diperkenankan digunakan untuk keperluan pribadi atau dipindahtangankan.</li>
        </ol>
    </div>
    
    <div class="footer">
        <!-- Tanggal di atas footer, di kanan -->
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
</body>
</html>