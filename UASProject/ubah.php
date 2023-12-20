<?php
session_start();
require 'function.php';

class FormHandler
{
    protected $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function handleFormSubmission($data)
    {
        $siswaManager = new SiswaManager($this->db);

        if (!isset($_SESSION['login'])) {
            header('location:login.php');
            exit;
        }

        $nim = $_GET['nim'];
        $siswa = $this->getSiswaByNim($nim);

        if (isset($data['ubah'])) {
            if ($siswaManager->ubahSiswa($data, $siswa) > 0) {
                echo "<script>
                        alert('Data Mahasiswa berhasil diubah!');
                        document.location.href = 'index.php';
                    </script>";
            } else {
                echo "<script>
                        alert('Data Mahasiswa gagal diubah!');
                    </script>";
            }
        }
    }

    protected function getSiswaByNim($nim)
    {
        $result = $this->db->getConnection()->query("SELECT * FROM siswa WHERE nim = $nim");

        if ($result) {
            return $result->fetch_assoc();
        }

        return null;
    }
}

$db = new ExtendedDatabase();
$formHandler = new FormHandler($db);

$formHandler->handleFormSubmission($_POST);
$nim = $_GET['nim'];
$siswa = query("SELECT * FROM siswa WHERE nim = $nim")[0];
?>
<!DOCTYPE html>
<html lang="en">

<head>
     <meta charset="UTF-8">
     <meta http-equiv="X-UA-Compatible" content="IE=edge">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">

     <!-- Bootstrap -->
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
     <!-- Bootstrap Icons -->
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.0/font/bootstrap-icons.css">
     <!-- Font Google -->
     <link rel="preconnect" href="https://fonts.gstatic.com">
     <link href="https://fonts.googleapis.com/css2?family=Righteous&display=swap" rel="stylesheet">
     <!-- animasi CSS Aos -->
     <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
     <!-- My CSS -->
     <link rel="stylesheet" href="css/style.css">

     <title>Update Data</title>
</head>

<body background="img/bg/bck.png">
     <!-- Navbar -->
     <nav class="navbar navbar-expand-lg navbar-dark bg-dark text-uppercase">
          <div class="container">
               <a class="navbar-brand" href="index.php">Admin Data Mahasiswa</a>
               <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
               </button>
               <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                         <li class="nav-item">
                              <a class="nav-link" aria-current="page" href="index.php">Home</a>
                         </li>
                         </li>
                         <li class="nav-item">
                              <a class="nav-link" href="logout.php">Logout</a>
                         </li>
                    </ul>
               </div>
          </div>
     </nav>
     <!-- Close Navbar -->

     <!-- Container -->
     <div class="container">
          <div class="row my-2 text-light">
               <div class="col-md">
                    <h3 class="fw-bold text-uppercase ubah_data"></h3>
               </div>
               <hr>
          </div>
          <div class="row my-2 text-light">
               <div class="col-md">
                    <form action="" method="post" enctype="multipart/form-data">
                         <input type="hidden" name="gambarLama" value="<?= $siswa['gambar']; ?>">
                         <div class="mb-3">
                              <label for="nim" class="form-label">NIM</label>
                              <input type="number" class="form-control w-50" id="nim" value="<?= $siswa['nim']; ?>"
                                   name="nim" readonly>
                         </div>
                         <div class="mb-3">
                              <label for="nama" class="form-label">Nama</label>
                              <input type="text" class="form-control w-50" id="nama" value="<?= $siswa['nama']; ?>"
                                   name="nama" autocomplete="off" required>
                         </div>
                         <div class="mb-3">
                              <label for="tmpt_Lahir" class="form-label">Tempat Lahir</label>
                              <input type="text" class="form-control w-50" id="tmpt_Lahir"
                                   value="<?= $siswa['tmpt_Lahir']; ?>" name="tmpt_Lahir" autocomplete="off" required>
                         </div>
                         <div class="mb-3">
                              <label for="tgl_Lahir" class="form-label">Tanggal Lahir</label>
                              <input type="date" class="form-control w-50" id="tgl_Lahir"
                                   value="<?= $siswa['tgl_Lahir']; ?>" name="tgl_Lahir" autocomplete="off" required>
                         </div>
                         <div class="mb-3">
                              <label>Jenis Kelamin</label>
                              <div class="form-check">
                                   <input class="form-check-input" type="radio" name="jekel" id="Laki - Laki"
                                        value="Laki - Laki" <?php if ($siswa['jekel'] == 'Laki - Laki') { ?> checked=''
                                        <?php } ?>>
                                   <label class="form-check-label" for="Laki - Laki">Laki - Laki</label>
                              </div>
                              <div class="form-check">
                                   <input class="form-check-input" type="radio" name="jekel" id="Perempuan"
                                        value="Perempuan" <?php if ($siswa['jekel'] == 'Perempuan') { ?> checked=''
                                        <?php } ?>>
                                   <label class="form-check-label" for="Perempuan">Perempuan</label>
                              </div>
                         </div>
                         <div class="mb-3">
                              <label for="jurusan" class="form-label">Jurusan</label>
                              <select class="form-select w-50" id="jurusan" name="jurusan">
                                   <option disabled selected value>--------------------------------------------Pilih
                                        Jurusan--------------------------------------------</option>
                                   <option value="Teknik Informatika" <?php if ($siswa['jurusan'] == 'Teknik Informatika') { ?>
                                        selected='' <?php } ?>>Teknik Informatika</option>
                                   <option value="Teknik Industri"
                                        <?php if ($siswa['jurusan'] == 'Teknik Industri') { ?> selected=''
                                        <?php } ?>>Industri</option>
                                   <option value="Multimedia" <?php if ($siswa['jurusan'] == 'Multimedia') { ?>
                                        selected='' <?php } ?>>Multimedia</option>
                                   <option value="Rekayasa Perangkat Lunak"
                                        <?php if ($siswa['jurusan'] == 'Rekayasa Perangkat Lunak') { ?> selected=''
                                        <?php } ?>>Rekayasa Perangkat Lunak</option>
                                   <option value="Geomatika" <?php if ($siswa['jurusan'] == 'Geomatika') { ?>
                                        selected='' <?php } ?>>Geomatika</option>
                                   <option value="Mesin" <?php if ($siswa['jurusan'] == 'Mesin') { ?> selected=''
                                        <?php } ?>>Mesin</option>
                              </select>
                         </div>
                         <div class="mb-3">
                              <label for="email" class="form-label">E-Mail</label>
                              <input type="email" class="form-control w-50" id="email" value="<?= $siswa['email']; ?>"
                                   name="email" autocomplete="off" required>
                         </div>
                         <div class="mb-3">
                              <label for="gambar" class="form-label">Gambar <i>(Saat ini)</i></label> <br>
                              <img src="img/<?= $siswa['gambar']; ?>" width="50%" style="margin-bottom: 10px;">
                              <input class="form-control form-control-sm w-50" id="gambar" name="gambar" type="file">
                         </div>
                         <div class="mb-3">
                              <label for="alamat" class="form-label">Alamat</label>
                              <textarea class="form-control w-50" id="alamat" rows="5" name="alamat"
                                   autocomplete="off"><?= $siswa['alamat']; ?></textarea>
                         </div>
                         <hr>
                         <a href="index.php" class="btn btn-secondary">Kembali</a>
                         <button type="submit" class="btn btn-warning" name="ubah">Ubah</button>
                    </form>
               </div>
          </div>
     </div>
     <!-- Close Container -->

     <!-- Footer -->
     <div class="container-fluid">
          <div class="row bg-dark text-white text-center">
               <!-- ... -->
          </div>
     </div>
     <!-- Close Footer -->

     <!-- Bootstrap -->
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js"
          integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous">
     </script>

     <!-- animasi  gsap-->
     <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.9.1/gsap.min.js"> </script>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.9.1/TextPlugin.min.js"></script>
     <script>
     gsap.registerPlugin(TextPlugin);
     gsap.to('.ubah_data', {
          duration: 2,
          delay: 1,
          color: 'black', // Change 'black' to the desired color
          text: '<i class="bi bi-horizontal-line"></i>Ubah Data Mahasiswa'
     })
     gsap.from('.navbar', {
          duration: 1,
          y: '-100%',
          opacity: 0,
          ease: 'bounce',
     })
     </script>
</body>

</html>
