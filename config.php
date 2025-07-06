<?php
// virtual_file: config.php
// Deskripsi: File ini untuk konfigurasi koneksi ke database.
//
// Mohon sesuaikan nilai DB_SERVER, DB_USERNAME, DB_PASSWORD, dan DB_DATABASE
// dengan konfigurasi server database Anda.

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root'); // Ganti dengan username database Anda
define('DB_PASSWORD', ''); // Ganti dengan password database Anda
define('DB_DATABASE', 'loker_jobforu'); // Ganti dengan nama database Anda

// Membuat koneksi ke database
$db = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

// Cek koneksi
if ($db === false) {
    die("ERROR: Tidak dapat terhubung ke database. " . mysqli_connect_error());
}
