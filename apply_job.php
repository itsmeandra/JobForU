<?php
// virtual_file: apply_job.php
// Deskripsi: File ini dirombak total untuk menangani upload file CV.

session_start();
require_once "config.php";

// Proteksi: user harus login untuk melamar
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Cek jika request adalah POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Pastikan job_id ada dan tidak kosong
    if (isset($_POST['job_id']) && !empty(trim($_POST['job_id']))) {
        $job_id = trim($_POST['job_id']);
        $user_id = $_SESSION['id'];

        // Cek dulu apakah user sudah pernah melamar pekerjaan yang sama
        $sql_check = "SELECT id FROM applications WHERE job_id = ? AND user_id = ?";
        if ($stmt_check = mysqli_prepare($db, $sql_check)) {
            mysqli_stmt_bind_param($stmt_check, "ii", $job_id, $user_id);
            mysqli_stmt_execute($stmt_check);
            mysqli_stmt_store_result($stmt_check);
            if (mysqli_stmt_num_rows($stmt_check) > 0) {
                mysqli_stmt_close($stmt_check);
                header("location: user_dashboard.php?apply_status=exists");
                exit();
            }
            mysqli_stmt_close($stmt_check);
        }

        // --- Proses Upload File CV ---
        if (isset($_FILES["cv"]) && $_FILES["cv"]["error"] == 0) {
            $allowed_ext = ["pdf", "doc", "docx"];
            $upload_dir = "uploads/cv/";
            $file_name = basename($_FILES["cv"]["name"]);
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $file_size = $_FILES["cv"]["size"];
            $max_size = 2 * 1024 * 1024; // 2MB

            // Validasi ekstensi file
            if (!in_array($file_ext, $allowed_ext)) {
                header("location: user_dashboard.php?apply_status=file_error");
                exit();
            }

            // Validasi ukuran file
            if ($file_size > $max_size) {
                header("location: user_dashboard.php?apply_status=file_error");
                exit();
            }

            // Buat nama file unik untuk menghindari tumpang tindih
            $unique_file_name = "cv_" . $user_id . "_" . $job_id . "_" . time() . "." . $file_ext;
            $target_file = $upload_dir . $unique_file_name;

            // Pindahkan file ke folder uploads
            if (move_uploaded_file($_FILES["cv"]["tmp_name"], $target_file)) {

                // --- Insert data ke database ---
                $sql = "INSERT INTO applications (job_id, user_id, cv_path, status) VALUES (?, ?, ?, 'pending')";
                if ($stmt = mysqli_prepare($db, $sql)) {
                    mysqli_stmt_bind_param($stmt, "iis", $job_id, $user_id, $target_file);
                    if (mysqli_stmt_execute($stmt)) {
                        header("location: user_dashboard.php?apply_status=success");
                    } else {
                        header("location: user_dashboard.php?apply_status=failed");
                    }
                    mysqli_stmt_close($stmt);
                }
            } else {
                header("location: user_dashboard.php?apply_status=failed");
            }
        } else {
            // Error jika tidak ada file atau error saat upload
            header("location: user_dashboard.php?apply_status=file_error");
        }
    } else {
        header("location: user_dashboard.php?apply_status=failed");
    }
} else {
    // Jika bukan POST request, redirect
    header("location: user_dashboard.php");
}
mysqli_close($db);
exit;
