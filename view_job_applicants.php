<?php
// virtual_file: view_job_applicants.php
// Deskripsi: Diperbarui dengan tombol aksi Terima/Tolak dan desain minimalis.

session_start();
require_once "config.php";

// Proteksi halaman admin
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'admin') {
    header("location: login.php");
    exit;
}

// Validasi job_id dari URL
if (!isset($_GET['job_id']) || !is_numeric($_GET['job_id'])) {
    header("location: admin_dashboard.php");
    exit;
}
$job_id = $_GET['job_id'];

// Ambil detail pekerjaan
$job_sql = "SELECT judul, nama_perusahaan FROM jobs WHERE id = ?";
$job_title = "Data Pelamar";
$company_name = "";
if ($stmt_job = mysqli_prepare($db, $job_sql)) {
    mysqli_stmt_bind_param($stmt_job, "i", $job_id);
    if (mysqli_stmt_execute($stmt_job)) {
        $result_job = mysqli_stmt_get_result($stmt_job);
        if (mysqli_num_rows($result_job) == 1) {
            $job_details = mysqli_fetch_assoc($result_job);
            $job_title = $job_details['judul'];
            $company_name = $job_details['nama_perusahaan'];
        } else {
            header("location: admin_dashboard.php");
            exit;
        }
    }
    mysqli_stmt_close($stmt_job);
}

// Ambil data pelamar
$sql = "SELECT a.id, u.nama_lengkap, u.email, a.applied_at, a.cv_path, a.status FROM applications a JOIN users u ON a.user_id = u.id WHERE a.job_id = ? ORDER BY a.status ASC, a.applied_at DESC";
$applicants = [];
if ($stmt = mysqli_prepare($db, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $job_id);
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $applicants[] = $row;
        }
    }
    mysqli_stmt_close($stmt);
}

// Fungsi helper untuk badge status
function getStatusBadge($status)
{
    $status = strtolower($status);
    switch ($status) {
        case 'accepted':
            return 'bg-green-100 text-green-800';
        case 'rejected':
            return 'bg-red-100 text-red-800';
        default: // 'pending'
            return 'bg-yellow-100 text-yellow-800';
    }
}
?>
<!DOCTYPE html>
<html lang="id" class="bg-gray-50">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pelamar untuk <?php echo htmlspecialchars($job_title); ?> - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in {
            animation: fadeIn 0.5s ease-out forwards;
        }
    </style>
</head>

<body class="text-gray-800">

    <nav class="bg-white/60 backdrop-blur-lg sticky top-0 z-10 border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="admin_dashboard.php" class="text-xl font-bold text-indigo-600">JobForU (Admin)</a>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-600">Halo, <span class="font-medium"><?php echo htmlspecialchars($_SESSION["nama_lengkap"]); ?></span>!</span>
                    <a href="logout.php" class="text-sm bg-indigo-600 text-white font-medium py-2 px-4 rounded-lg hover:bg-indigo-700 transition-colors">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 fade-in">

        <header class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Daftar Pelamar</h1>
                    <p class="text-base text-gray-500 mt-1">Untuk lowongan: <span class="font-semibold text-gray-700"><?php echo htmlspecialchars($job_title); ?></span></p>
                </div>
                <a href="admin_dashboard.php" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium flex items-center">
                    <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Dashboard
                </a>
            </div>
        </header>

        <main>
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Pelamar</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal Lamar</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (!empty($applicants)): ?>
                                <?php foreach ($applicants as $app): ?>
                                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($app['nama_lengkap']); ?></div>
                                            <div class="text-sm text-gray-500"><?php echo htmlspecialchars($app['email']); ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            <?php echo date('d M Y, H:i', strtotime($app['applied_at'])); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo getStatusBadge($app['status']); ?>">
                                                <?php echo htmlspecialchars(ucfirst($app['status'])); ?>
                                            </span>
                                            <?php if ($app['status'] == 'pending'): ?>
                                                <form action="update_status.php" method="POST" class="inline-block">
                                                    <input type="hidden" name="application_id" value="<?php echo $app['id']; ?>">
                                                    <input type="hidden" name="job_id" value="<?php echo $job_id; ?>">
                                                    <input type="hidden" name="new_status" value="accepted">
                                                    <button type="submit" class="bg-green-100 text-green-700 font-medium text-xs py-2 px-3 rounded-lg hover:bg-green-200 transition-colors">Terima</button>
                                                </form>
                                                <form action="update_status.php" method="POST" class="inline-block">
                                                    <input type="hidden" name="application_id" value="<?php echo $app['id']; ?>">
                                                    <input type="hidden" name="job_id" value="<?php echo $job_id; ?>">
                                                    <input type="hidden" name="new_status" value="rejected">
                                                    <button type="submit" class="bg-red-100 text-red-700 font-medium text-xs py-2 px-3 rounded-lg hover:bg-red-200 transition-colors">Tolak</button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center space-x-2">

                                                <a href="<?php echo htmlspecialchars($app['cv_path']); ?>" target="_blank" class="bg-gray-100 text-gray-700 font-medium text-xs py-2 px-3 rounded-lg hover:bg-gray-200 transition-colors">Lihat CV</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-12 text-gray-400">
                                        <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p class="mt-2 text-sm font-medium">Belum ada pelamar untuk lowongan ini.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

</body>

</html>