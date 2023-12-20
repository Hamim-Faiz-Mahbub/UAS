<?php
session_start();

// Jika tidak bisa login maka balik ke login.php
// jika masuk ke halaman ini melalui url, maka langsung menuju halaman login
if (!isset($_SESSION['login'])) {
    header('location:login.php');
    exit;
}

// Memanggil atau membutuhkan file function.php
require 'function.php';

// Mengambil data dari nim dengan fungsi get
$nim = $_GET['nim'];

// Menginisialisasi objek ExtendedDatabase
$db = new ExtendedDatabase();

// Membuat objek SiswaManager dan DataManager
$siswaManager = new SiswaManager($db);
$dataManager = new DataManager($db);

// Menghapus data siswa berdasarkan nim
$result = $siswaManager->hapusSiswa($nim);

if ($result > 0) {
    echo "<script>
                alert('Data siswa berhasil dihapus!');
                document.location.href = 'index.php';
            </script>";
} else {
    // Jika fungsi hapusSiswa mengembalikan nilai kurang dari 0/data tidak terhapus, maka munculkan alert dibawah
    echo "<script>
            alert('Data siswa gagal dihapus!');
        </script>";
}
?>
