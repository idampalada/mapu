<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Akses Ditolak</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            text-align: center;
            padding: 60px;
        }
        .box {
            background-color: #fff;
            padding: 40px;
            display: inline-block;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        h1 {
            color: #dc3545;
            font-size: 32px;
        }
        p {
            color: #555;
            margin-top: 10px;
        }
        a {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="box">
        <h1>🚫 Akses Ditolak</h1>
        <p>Anda tidak memiliki izin untuk mengakses halaman ini.</p>
        <p><a href="<?= base_url('/') ?>">Kembali ke Beranda</a></p>
    </div>
</body>
</html>
