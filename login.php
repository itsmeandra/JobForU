<?php
// virtual_file: login.php
// Deskripsi: Halaman login dengan tampilan CSS yang ditingkatkan.

session_start();
// Jika sudah login, redirect ke halaman index
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: index.php");
    exit;
}
require_once "config.php";

$email = $password = "";
$email_err = $password_err = $login_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validasi email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Mohon masukkan email.";
    } else {
        $email = trim($_POST["email"]);
    }

    // Validasi password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Mohon masukkan password.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Jika tidak ada error validasi
    if (empty($email_err) && empty($password_err)) {
        $sql = "SELECT id, nama_lengkap, email, password, role FROM users WHERE email = ?";
        if ($stmt = mysqli_prepare($db, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            $param_email = $email;

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);

                if (mysqli_stmt_num_rows($stmt) == 1) {
                    mysqli_stmt_bind_result($stmt, $id, $nama_lengkap, $email, $hashed_password, $role);
                    if (mysqli_stmt_fetch($stmt)) {
                        if (password_verify($password, $hashed_password)) {
                            session_start();
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["nama_lengkap"] = $nama_lengkap;
                            $_SESSION["email"] = $email;
                            $_SESSION["role"] = $role;
                            header("location: index.php");
                        } else {
                            $login_err = "Email atau password salah.";
                        }
                    }
                } else {
                    $login_err = "Email atau password salah.";
                }
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
    <title>Login - JobForU</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <div class="flex min-h-screen">
        <div class="hidden lg:flex w-1/2 items-center justify-center bg-indigo-700 text-white p-12">
            <div class="text-center">
                <h1 class="text-5xl font-extrabold tracking-tight">JobForU</h1>
                <p class="mt-4 text-lg text-indigo-200">Temukan Karir Impian Anda. Lowongan pekerjaan menanti.</p>
            </div>
        </div>

        <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12 fade-in">
            <div class="w-full max-w-md">
                <div class="text-center lg:hidden mb-8">
                    <h1 class="text-4xl font-extrabold tracking-tight text-indigo-700">JobForU</h1>
                </div>

                <h2 class="text-3xl font-bold text-gray-800 mb-2">Selamat Datang Kembali!</h2>
                <p class="text-gray-600 mb-8">Login untuk melanjutkan.</p>

                <?php if (!empty($login_err)): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                        <p class="font-bold">Error</p>
                        <p><?php echo $login_err; ?></p>
                    </div>
                <?php endif; ?>

                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="mb-5">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M3 4a2 2 0 00-2 2v1.161l8.441 4.221a1.25 1.25 0 001.118 0L19 7.162V6a2 2 0 00-2-2H3z" />
                                    <path d="M19 8.839l-7.77 3.885a2.75 2.75 0 01-2.46 0L1 8.839V14a2 2 0 002 2h14a2 2 0 002-2V8.839z" />
                                </svg>
                            </span>
                            <input type="email" name="email" id="email" class="form-input pl-10 <?php echo (!empty($email_err)) ? 'border-red-500' : ''; ?>" value="<?php echo $email; ?>" placeholder="anda@email.com">
                        </div>
                        <span class="text-red-500 text-xs italic mt-1"><?php echo $email_err; ?></span>
                    </div>

                    <div class="mb-6">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd" />
                                </svg>
                            </span>
                            <input type="password" name="password" id="password" class="form-input pl-10 <?php echo (!empty($password_err)) ? 'border-red-500' : ''; ?>" placeholder="••••••••">
                        </div>
                        <span class="text-red-500 text-xs italic mt-1"><?php echo $password_err; ?></span>
                    </div>

                    <div>
                        <button type="submit" class="btn btn-primary w-full">
                            Login
                        </button>
                    </div>
                </form>

                <p class="text-center text-sm text-gray-600 mt-8">
                    Belum punya akun? <a href="register.php" class="font-medium text-indigo-600 hover:text-indigo-500 hover:underline">Register di sini</a>
                </p>
            </div>
        </div>

    </div>

</body>

</html>