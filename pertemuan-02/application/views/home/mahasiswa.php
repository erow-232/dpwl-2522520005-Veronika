<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= $title; ?></title>
</head>
<body>
    <h1>Data Profil Mahasiswa</h1>
    <ul>
        <li><strong>NIM:</strong> <?= $nim; ?></li>
        <li><strong>Nama:</strong> <?= $nama; ?></li>
        <li><strong>Kelas:</strong> <?= $kelas; ?></li>
    </ul>

    <!-- Gunakan site_url() untuk navigasi halaman sesuai petunjuk modul O.2 -->
    <a href="<?= site_url(); ?>">Kembali ke Beranda</a>
</body>
</html>