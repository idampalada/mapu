<!DOCTYPE html>
<html>
<head>
    <title>BERITA ACARA PENGEMBALIAN</title>
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
            text-transform: uppercase;
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
            border-collapse: collapse;
        }
        table.no-border td {
            padding: 3px 0;
            vertical-align: top;
        }
        .signature {
            width: 100%;
            margin-top: 40px;
        }
        .signature-left {
            width: 45%;
            float: left;
            text-align: center;
        }
        .signature-right {
            width: 45%;
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
        .page-break {
            page-break-before: always;
        }
        .image-container {
            text-align: center;
            margin: 20px 0;
        }
        .image-container img {
            max-width: 80%;
            height: auto;
            border: 1px solid #ccc;
        }
        .timestamp {
            font-size: 10pt;
            color: #666;
            text-align: right;
            margin-top: 5px;
        }
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        .detail-table th,
        .detail-table td {
            border: 1px solid #000;
            padding: 5px;
        }
        .detail-table th {
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">BERITA ACARA SERAH TERIMA PENGEMBALIAN KENDARAAN DINAS</div>
        <div>NOMOR : <?= $nomor_surat ?></div>
    </div>
    
    <div class="content">
        <p>Pada hari ini <?= date('d', strtotime($tanggal_pengembalian)) ?> tanggal <?= date('m', strtotime($tanggal_pengembalian)) ?> bulan <?= date('Y', strtotime($tanggal_pengembalian)) ?> tahun, kami yang bertanda tangan di bawah ini:</p>
        
        <table class="no-border">
            <tr>
                <td width="30%">Nama</td>
                <td width="2%">:</td>
                <td><?= $nama_penanggung_jawab ?></td>
            </tr>
            <tr>
                <td>NIP / NRP</td>
                <td>:</td>
                <td><?= $nip_nrp ?></td>
            </tr>
            <tr>
                <td>Pangkat / Golongan</td>
                <td>:</td>
                <td><?= $pangkat_golongan ?></td>
            </tr>
            <tr>
                <td>Jabatan</td>
                <td>:</td>
                <td><?= $jabatan ?></td>
            </tr>
            <tr>
                <td>Alamat Rumah</td>
                <td>:</td>
                <td><?= $alamat_rumah ?? '-' ?></td>
            </tr>
            <tr>
                <td>No. Telp Rumah / HP</td>
                <td>:</td>
                <td><?= $no_hp ?? '-' ?></td>
            </tr>
            <tr>
                <td>No. KTP</td>
                <td>:</td>
                <td><?= $no_ktp ?? '-' ?></td>
            </tr>
        </table>
        
        <p>Selanjutnya disebut <strong>PIHAK KESATU</strong></p>
        
        <table class="no-border">
            <tr>
                <td width="30%">Nama</td>
                <td width="2%">:</td>
                <td><?= $pihak_kedua_nama ?></td>
            </tr>
            <tr>
                <td>NIP</td>
                <td>:</td>
                <td><?= $pihak_kedua_nip ?></td>
            </tr>
            <tr>
                <td>Jabatan</td>
                <td>:</td>
                <td><?= $pihak_kedua_jabatan ?></td>
            </tr>
        </table>
        
        <p>Selanjutnya disebut <strong>PIHAK KEDUA</strong></p>
        
        <p>Melakukan serah terima <strong>PENGEMBALIAN</strong> Kendaraan Dinas dengan penjelasan sebagai berikut :</p>
    </div>
    
    <!-- Halaman 2 -->
    <div class="page-break"></div>
    
    <div class="content">
        <h3>Pasal 1</h3>
        <p>PIHAK KESATU menyerahkan kepada PIHAK KEDUA dan PIHAK KEDUA menyatakan menerima dari PIHAK KESATU Kendaraan Dinas sebagai berikut :</p>
        
        <table class="detail-table">
            <tr>
                <td width="40%">Nomor SIP / Surat Penanggung Jawab</td>
                <td><?= $nomor_sip ?></td>
            </tr>
            <tr>
                <td>Jenis Kendaraan</td>
                <td><?= $kategori_id ?></td>
            </tr>
            <tr>
                <td>Nomor Polisi</td>
                <td><?= $no_polisi ?></td>
            </tr>
            <tr>
                <td>Kode Barang / NUP</td>
                <td><?= $kode_barang ?> / <?= $nup ?? '-' ?></td>
            </tr>
            <tr>
                <td>Tahun Pembuatan</td>
                <td><?= $tahun_pembuatan ?? '-' ?></td>
            </tr>
            <tr>
                <td>Merk / Type</td>
                <td><?= $merk ?></td>
            </tr>
            <tr>
                <td>Warna</td>
                <td><?= $warna ?? '-' ?></td>
            </tr>
            <tr>
                <td>Nomor Mesin</td>
                <td><?= $nomor_mesin ?? '-' ?></td>
            </tr>
            <tr>
                <td>Nomor Rangka</td>
                <td><?= $nomor_rangka ?? '-' ?></td>
            </tr>
            <tr>
                <td>Kondisi Kendaraan</td>
                <td><?= $kondisi_kembali ?></td>
            </tr>
        </table>
        
        <h3>Pasal 2</h3>
        <p>Penyerahan sebagaimana dimaksud dalam Pasal 1 berupa:</p>
        <ol>
            <li>Kendaraan sebagaimana dimaksud dalam Pasal 1</li>
            <li>STNK kendaraan sebagaimana dimaksud dalam Pasal 1</li>
            <li>Kunci kendaraan sebagaimana dimaksud dalam Pasal 1</li>
            <li>Kelengkapan berupa ..., ..., dan ...</li>
        </ol>
        
        <div class="image-container">
            <p><strong>Foto Kendaraan Saat Pengembalian:</strong></p>
            <?php if (!empty($foto_pengembalian)): ?>
                <img src="<?= 'data:image/jpeg;base64,' . base64_encode(@file_get_contents(ROOTPATH . 'public/uploads/images/' . $foto_pengembalian)) ?>" alt="Foto Kendaraan">
            <?php else: ?>
                <p>Foto Kendaraan</p>
            <?php endif; ?>
            <div class="timestamp">Timestamp: <?= date('d/m/Y H:i:s') ?></div>
        </div>
        
        <p>Dengan adanya Serah Terima ini maka selanjutnya tanggung jawab Kendaraan Dinas tersebut beralih dari PIHAK KESATU kepada PIHAK KEDUA.</p>
        
        <p>Demikian Berita Acara Serah Terima ini dibuat dengan sebenarnya untuk dipergunakan sebagaimana mestinya.</p>
        
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