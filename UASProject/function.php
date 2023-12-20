<?php

class Database
{
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $database = "Data_siswa";
    protected $koneksi;

    public function __construct()
    {
        $this->koneksi = new mysqli($this->host, $this->username, $this->password, $this->database);

        if ($this->koneksi->connect_error) {
            die("Connection failed: " . $this->koneksi->connect_error);
        }
    }

    public function getConnection()
    {
        return $this->koneksi;
    }
}

class ExtendedDatabase extends Database
{
    public function getConnection()
    {
        $customConnection = parent::getConnection();

        // Tambahkan logika kustom Anda di sini, jika ada

        return $customConnection;
    }
}

class SiswaManager
{
    protected $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function getSiswaByNim($nim)
    {
        $result = $this->db->getConnection()->query("SELECT * FROM siswa WHERE nim = $nim");

        if ($result) {
            return $result->fetch_assoc();
        }

        return null;
    }

    public function ubahSiswa($data, $siswa)
    {
        $nim = $data['nim'];
        $nama = htmlspecialchars($data['nama']);
        $tmpt_Lahir = htmlspecialchars($data['tmpt_Lahir']);
        $tgl_Lahir = $data['tgl_Lahir'];
        $jekel = $data['jekel'];
        $jurusan = $data['jurusan'];
        $email = htmlspecialchars($data['email']);
        $alamat = htmlspecialchars($data['alamat']);

        $gambarLama = $data['gambarLama'];

        if ($_FILES['gambar']['error'] === 4) {
            $gambar = $gambarLama;
        } else {
            $gambar = $this->upload();
        }

        $sql = "UPDATE siswa SET nama = '$nama', tmpt_Lahir = '$tmpt_Lahir', tgl_Lahir = '$tgl_Lahir', jekel = '$jekel', jurusan = '$jurusan', email = '$email', gambar = '$gambar', alamat = '$alamat' WHERE nim = $nim";

        mysqli_query($this->db->getConnection(), $sql);

        return mysqli_affected_rows($this->db->getConnection());
    }

    public function hapusSiswa($nim)
    {
        $sql = "DELETE FROM siswa WHERE nim = $nim";
        mysqli_query($this->db->getConnection(), $sql);

        return mysqli_affected_rows($this->db->getConnection());
    }

    protected function upload()
    {
        $namaFile = $_FILES['gambar']['name'];
        $ukuranFile = $_FILES['gambar']['size'];
        $error = $_FILES['gambar']['error'];
        $tmpName = $_FILES['gambar']['tmp_name'];

        if ($error === 4) {
            echo "<script>alert('Pilih gambar terlebih dahulu!');</script>";
            return false;
        }

        $extValid = ['jpg', 'jpeg', 'png'];
        $ext = explode('.', $namaFile);
        $ext = strtolower(end($ext));

        if (!in_array($ext, $extValid)) {
            echo "<script>alert('Yang anda upload bukanlah gambar!');</script>";
            return false;
        }

        if ($ukuranFile > 3000000) {
            echo "<script>alert('Ukuran gambar anda terlalu besar!');</script>";
            return false;
        }

        $namaFileBaru = uniqid();
        $namaFileBaru .= '.';
        $namaFileBaru .= $ext;

        move_uploaded_file($tmpName, 'img/' . $namaFileBaru);

        return $namaFileBaru;
    }
}

class DataManager
{
    protected $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function tambahData($data)
    {
        if ($this->tambah($data) > 0) {
            echo "<script>
                    alert('Data siswa berhasil ditambahkan!');
                    document.location.href = 'index.php';
                </script>";
        } else {
            echo "<script>
                    alert('Data siswa gagal ditambahkan!');
                </script>";
        }
    }

    protected function tambah($data)
    {
        $nim = htmlspecialchars($data['nim']);
        $nama = htmlspecialchars($data['nama']);
        $tmpt_Lahir = htmlspecialchars($data['tmpt_Lahir']);
        $tgl_Lahir = $data['tgl_Lahir'];
        $jekel = $data['jekel'];
        $jurusan = $data['jurusan'];
        $email = htmlspecialchars($data['email']);
        $gambar = $this->upload();
        $alamat = htmlspecialchars($data['alamat']);

        if (!$gambar) {
            return false;
        }

        $sql = "INSERT INTO siswa VALUES ('$nim','$nama','$tmpt_Lahir','$tgl_Lahir','$jekel','$jurusan','$email','$gambar','$alamat')";

        mysqli_query($this->db->getConnection(), $sql);

        return mysqli_affected_rows($this->db->getConnection());
    }

    protected function upload()
    {
        $namaFile = $_FILES['gambar']['name'];
        $ukuranFile = $_FILES['gambar']['size'];
        $error = $_FILES['gambar']['error'];
        $tmpName = $_FILES['gambar']['tmp_name'];

        if ($error === 4) {
            echo "<script>alert('Pilih gambar terlebih dahulu!');</script>";
            return false;
        }

        $extValid = ['jpg', 'jpeg', 'png'];
        $ext = explode('.', $namaFile);
        $ext = strtolower(end($ext));

        if (!in_array($ext, $extValid)) {
            echo "<script>alert('Yang anda upload bukanlah gambar!');</script>";
            return false;
        }

        if ($ukuranFile > 3000000) {
            echo "<script>alert('Ukuran gambar anda terlalu besar!');</script>";
            return false;
        }

        $namaFileBaru = uniqid();
        $namaFileBaru .= '.';
        $namaFileBaru .= $ext;

        move_uploaded_file($tmpName, 'img/' . $namaFileBaru);

        return $namaFileBaru;
    }
}

class IndexManager
{
    protected $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function getAllSiswa()
    {
        return query("SELECT * FROM siswa ORDER BY nim DESC");
    }
}

class LoginManager
{
    protected $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function login($username, $password, $remember)
    {
        // Mengambil data user berdasarkan username
        $result = mysqli_query($this->db->getConnection(), "SELECT * FROM user WHERE username = '$username'");
        $row = mysqli_fetch_assoc($result);

        // Jika username ditemukan
        if ($row) {
            // Verifikasi password menggunakan password_verify
            if (password_verify($password, $row['password'])) {
                $_SESSION['login'] = true;

                // Cek remember me
                if ($remember) {
                    // Buat cookie dan acak cookie
                    setcookie('id', $row['id'], time() + 60);
                    setcookie('key', hash('sha256', $row['username']), time() + 60);
                }

                header('location:index.php');
                exit;
            }
        }

        // Jika login gagal, tampilkan error
        return false;
    }
}

function query($query)
{
    $db = new ExtendedDatabase();
    $result = $db->getConnection()->query($query);

    $rows = [];

    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }

    return $rows;
}

function registrasi($data)
{
    $db = new ExtendedDatabase();
    $username = strtolower(stripslashes($data["username"]));
    $password = mysqli_real_escape_string($db->getConnection(), $data["password"]);
    $password2 = mysqli_real_escape_string($db->getConnection(), $data["password2"]);

    $result = mysqli_query($db->getConnection(), "SELECT username FROM user WHERE username = '$username'");

    if (mysqli_fetch_assoc($result)) {
        echo "<script>alert('username sudah terdaftar');</script>";
        return false;
    }

    if ($password !== $password2) {
        echo "<script>alert('konfirmasi password tidak sesuai');</script>";
        return false;
    }

    $password = password_hash($password, PASSWORD_DEFAULT);

    mysqli_query($db->getConnection(), "INSERT INTO user VALUES('', '$username', '$password')");

    return mysqli_affected_rows($db->getConnection());
}
?>
