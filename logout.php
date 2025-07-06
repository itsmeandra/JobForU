<?php
// virtual_file: logout.php
// Deskripsi: Menghancurkan session dan mengarahkan user ke halaman login dengan tampilan.
session_start();
$_SESSION = array();
session_destroy();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout - JobForU</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <meta http-equiv="refresh" content="2;url=login.php">
    <style>
        .spinner {
            border-top-color: var(--primary-color);
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>
    <div class="min-h-screen flex flex-col items-center justify-center">
        <div class="spinner w-12 h-12 border-4 border-gray-300 rounded-full"></div>
        <p class="mt-4 text-lg font-semibold text-gray-700">Anda sedang keluar...</p>
        <p class="text-sm text-gray-500">Anda akan diarahkan ke halaman login.</p>
    </div>
</body>

</html>