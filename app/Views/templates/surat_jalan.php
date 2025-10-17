<!DOCTYPE html>
<html>
<head>
    <title>SURAT JALAN</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12pt;
            line-height: 1.5;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .title {
            font-weight: bold;
            text-decoration: underline;
            margin: 5px 0;
        }
        .content {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
        }
        table td {
            padding: 3px 0;
            vertical-align: top;
        }
        .indent {
            padding-left: 20px;
        }
        .signature {
            width: 100%;
            margin-top: 40px;
        }
        .signature-left {
            width: 50%;
            float: left;
            text-align: center;
        }
        .signature-right {
            width: 50%;
            float: right;
            text-align: center;
        }
        .signature-name {
            margin-top: 60px;
            text-decoration: underline;
            font-weight: bold;
        }
        .clear {
            clear: both;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">SURAT JALAN</div>
        <div class="title">SURAT JALAN KENDARAAN DINAS FUNGSIONAL</div>
        <div class="title">KEMENTERIAN PEKERJAAN UMUM DAN PERUMAHAN RAKYAT</div>
        <div>NOMOR : <?= $nomor_surat ?></div>
    </div>
    
    <div class="content">
        <p>Dalam rangka penggunaan Kendaraan Dinas Fungsional pada Satuan Kerja <?= $unit_organisasi ?> Kementerian PUPR, dengan ini:</p>
        
        <table>
            <tr>
                <td width="150">Nama</td>
                <td>: <?= $nama_penanggung_jawab ?></td>
            </tr>
            <tr>
                <td>NIP / NRP</td>
                <td>: <?= $nip_nrp ?></td>
            </tr>
            <tr>
                <td>Pangkat / Golongan</td>
                <td>: <?= $pangkat_golongan ?></td>
            </tr>
            <tr>
                <td>Jabatan</td>
                <td>: <?= $jabatan ?></td>
            </tr>
        </table>
        
        <p style="text-align: center; font-weight: bold;">DIIZINKAN</p>
        
        <p>untuk memakai 1 (satu) unit kendaraan dinas fungsional dalam rangka melaksanakan tugas kedinasan</p>
        <p class="indent"><?= $urusan_kedinasan ?></p>
        
        <p>mulai tanggal <?= date('d F Y', strtotime($tanggal_mulai)) ?> Jam <?= $jam_mulai ?> sampai dengan tanggal <?= date('d F Y', strtotime($tanggal_selesai)) ?> Jam <?= $jam_selesai ?>.</p>
        
        <p>Data Kendaraan Dinas Fungsional:</p>
        
        <table>
            <tr>
                <td width="150">Kode Barang</td>
                <td>: <?= $kode_barang ?></td>
            </tr>
            <tr>
                <td>NUP</td>
                <td>: <?= $nup ?></td>
            </tr>
            <tr>
                <td>Nomor Polisi</td>
                <td>: <?= $no_polisi ?></td>
            </tr>
            <tr>
                <td>Merk / Type</td>
                <td>: <?= $merk ?></td>
            </tr>
        </table>
        
        <p>Dengan ketentuan:</p>
        <ol>
            <li>Pemakai bertanggung jawab atas keamanan kendaraan selama pemakaian;</li>
            <li>Pemakai bertanggung jawab atas kehilangan, bersedia dikenakan Tuntutan Ganti Rugi sesuai dengan ketentuan peraturan perundang-undangan;</li>
            <li>Kendaraan Dinas Fungsional hanya untuk keperluan dinas/tugas, dan tidak diperkenankan untuk keperluan pribadi/keluarga;</li>
            <li>Pemakai bersedia mengembalikan Kendaraan Dinas kepada Satuan Kerja selaku Kuasa Pengguna Barang.</li>
        </ol>
    </div>
    
    <p style="text-align: right; margin-top: 20px;"><?= $lokasi_terbit ?>, <?= date('d F Y', strtotime($tanggal_terbit)) ?></p>
    
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