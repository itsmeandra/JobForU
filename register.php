<?php
// virtual_file: register.php
// Deskripsi: Halaman untuk user baru mendaftarkan akun.

session_start();
require_once "config.php";

$nama_lengkap = $email = $password = "";
$nama_lengkap_err = $email_err = $password_err = "";
$register_success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validasi nama lengkap
    if (empty(trim($_POST["nama_lengkap"]))) {
        $nama_lengkap_err = "Mohon masukkan nama lengkap Anda.";
    } else {
        $nama_lengkap = trim($_POST["nama_lengkap"]);
    }

    // Validasi email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Mohon masukkan email Anda.";
    } else {
        // Cek apakah email sudah terdaftar
        $sql = "SELECT id FROM users WHERE email = ?";
        if ($stmt = mysqli_prepare($db, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            $param_email = trim($_POST["email"]);
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $email_err = "Email ini sudah terdaftar.";
                } else {
                    $email = trim($_POST["email"]);
                }
            } else {
                echo "Oops! Terjadi kesalahan. Silakan coba lagi nanti.";
            }
            mysqli_stmt_close($stmt);
        }
    }

    // Validasi password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Mohon masukkan password Anda.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password minimal harus 6 karakter.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Jika tidak ada error, masukkan data ke database
    if (empty($nama_lengkap_err) && empty($email_err) && empty($password_err)) {
        $sql = "INSERT INTO users (nama_lengkap, email, password) VALUES (?, ?, ?)";
        if ($stmt = mysqli_prepare($db, $sql)) {
            mysqli_stmt_bind_param($stmt, "sss", $param_nama, $param_email, $param_password);

            $param_nama = $nama_lengkap;
            $param_email = $email;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Enkripsi password

            if (mysqli_stmt_execute($stmt)) {
                $register_success = "Registrasi berhasil! Silakan login.";
                header("refresh:2;url=login.php");
            } else {
                echo "Oops! Terjadi kesalahan. Silakan coba lagi nanti.";
            }
            mysqli_stmt_close($stmt);
        }
    }
    mysqli_close($db);
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - JobForU</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <div class="min-h-screen flex items-center justify-center p-4 fade-in">
        <div class="max-w-md w-full bg-white p-8 rounded-xl shadow-lg">
            <h2 class="text-3xl font-bold text-center text-gray-900 mb-2">Buat Akun Baru</h2>
            <p class="text-center text-gray-600 mb-8">Temukan pekerjaan impianmu bersama JobForU.</p>

            <?php if (!empty($register_success)): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline"><?php echo $register_success; ?></span>
                </div>
            <?php endif; ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" novalidate>
                <div class="mb-4">
                    <label for="nama_lengkap" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" id="nama_lengkap" class="form-input <?php echo (!empty($nama_lengkap_err)) ? 'border-red-500' : ''; ?>" value="<?php echo $nama_lengkap; ?>">
                    <span class="text-red-500 text-xs italic"><?php echo $nama_lengkap_err; ?></span>
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" id="email" class="form-input <?php echo (!empty($email_err)) ? 'border-red-500' : ''; ?>" value="<?php echo $email; ?>">
                    <span class="text-red-500 text-xs italic"><?php echo $email_err; ?></span>
                </div>
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input type="password" name="password" id="password" class="form-input <?php echo (!empty($password_err)) ? 'border-red-500' : ''; ?>">
                    <span class="text-red-500 text-xs italic"><?php echo $password_err; ?></span>
                </div>
                <div>
                    <button type="submit" class="btn btn-primary w-full">
                        Register
                    </button>
                </div>
            </form>
            <p class="text-center text-sm text-gray-600 mt-6">
                Sudah punya akun? <a href="login.php" class="font-medium text-indigo-600 hover:text-indigo-500">Login di sini</a>
            </p>
        </div>
    </div>

</body>

</html>