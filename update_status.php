<?php
// virtual_file: update_status.php
// Deskripsi: File BARU untuk memproses perubahan status lamaran oleh admin.

session_start();
require_once "config.php";

// Proteksi: Hanya admin yang bisa mengakses
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'admin') {
    header("HTTP/1.1 403 Forbidden");
    exit("Akses ditolak.");
}

// Cek jika request adalah POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validasi data input
    if (isset($_POST['application_id']) && is_numeric($_POST['application_id']) && isset($_POST['new_status'])) {

        $application_id = $_POST['application_id'];
        $new_status = $_POST['new_status'];

        // Pastikan status yang dikirim valid (accepted atau rejected)
        if ($new_status === 'accepted' || $new_status === 'rejected') {

            // Siapkan statement untuk update
            $sql = "UPDATE applications SET status = ? WHERE id = ?";
            if ($stmt = mysqli_prepare($db, $sql)) {
                mysqli_stmt_bind_param($stmt, "si", $new_status, $application_id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        }
    }
}

// Redirect kembali ke halaman sebelumnya
$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'admin_dashboard.php';
header("location: " . $referer);
exit;
