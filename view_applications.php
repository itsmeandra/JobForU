<?php
// virtual_file: view_applications.php
// Deskripsi: Halaman BARU untuk admin melihat daftar pelamar dan mengunduh CV.

session_start();
require_once "config.php";
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'admin') {
    header("location: login.php");
    exit;
}

// Query untuk mengambil data lamaran dengan join ke tabel users dan jobs
$sql = "SELECT 
            a.id, 
            a.applied_at, 
            a.status,
            a.cv_path,
            u.nama_lengkap, 
            u.email,
            j.judul AS judul_pekerjaan
        FROM applications a
        JOIN users u ON a.user_id = u.id
        JOIN jobs j ON a.job_id = j.id
        ORDER BY a.applied_at DESC";

$applications_result = mysqli_query($db, $sql);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Lamaran - Admin JobForU</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <nav class="navbar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16"><a href="admin_dashboard.php" class="text-2xl font-bold text-indigo-600">JobForU (Admin)</a>
                <div class="flex items-center"><span class="text-gray-700 mr-4">Halo, <b><?php echo htmlspecialchars($_SESSION["nama_lengkap"]); ?></b>!</span><a href="logout.php" class="btn btn-danger text-sm py-2 px-3">Logout</a></div>
            </div>
        </div>
    </nav>
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold leading-tight text-gray-900">Daftar Lamaran Masuk</h2>
            <p class="text-gray-600 mt-1">Kelola semua lamaran yang dikirim oleh pengguna.</p>
        </div>
    </header>

    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8 fade-in">
        <div class="bg-white shadow-md rounded-lg overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pelamar</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pekerjaan Dilamar</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Lamar</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">CV</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php
                    if (mysqli_num_rows($applications_result) > 0) {
                        while ($app = mysqli_fetch_assoc($applications_result)) {
                            echo "<tr class='hover:bg-gray-50 transition'>";
                            echo "<td class='px-6 py-4 whitespace-nowrap'><div class='text-sm font-medium text-gray-900'>" . htmlspecialchars($app['nama_lengkap']) . "</div><div class='text-sm text-gray-500'>" . htmlspecialchars($app['email']) . "</div></td>";
                            echo "<td class='px-6 py-4 whitespace-nowrap text-sm text-gray-800'>" . htmlspecialchars($app['judul_pekerjaan']) . "</td>";
                            echo "<td class='px-6 py-4 whitespace-nowrap text-sm text-gray-600'>" . date('d M Y, H:i', strtotime($app['applied_at'])) . "</td>";
                            echo "<td class='px-6 py-4 whitespace-nowrap'><span class='px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800'>" . ucfirst($app['status']) . "</span></td>";
                            echo "<td class='px-6 py-4 whitespace-nowrap text-center text-sm font-medium'><a href='" . htmlspecialchars($app['cv_path']) . "' target='_blank' class='btn btn-primary py-1 px-3 text-sm'>Lihat CV</a></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' class='text-center py-10 text-gray-500'>Belum ada lamaran yang masuk.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>
</body>

</html>