<?php
// virtual_file: user_dashboard.php
// Deskripsi: Diperbarui agar user dapat melihat status lamaran mereka.
session_start();
require_once "config.php";
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Modifikasi query untuk mengambil status lamaran
$sql = "SELECT j.*, a.status AS application_status
        FROM jobs j
        LEFT JOIN (SELECT job_id, status FROM applications WHERE user_id = ?) a
        ON j.id = a.job_id
        ORDER BY j.posted_at DESC";

$jobs = [];
if ($stmt = mysqli_prepare($db, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $_SESSION['id']);
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $jobs[] = $row;
        }
    }
    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cari Lowongan - JobForU</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <nav class="navbar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex-shrink-0">
                    <h1 class="text-2xl font-bold text-indigo-600">JobForU</h1>
                </div>
                <div class="flex items-center"><span class="text-gray-700 mr-4">Halo, <b><?php echo htmlspecialchars($_SESSION["nama_lengkap"]); ?></b>!</span><a href="logout.php" class="btn btn-primary text-sm py-2 px-3">Logout</a></div>
            </div>
        </div>
    </nav>
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold leading-tight text-gray-900">Temukan Pekerjaan Impian Anda</h2>
            <p class="text-gray-600 mt-1">Ribuan kesempatan menanti Anda. Lamar sekarang!</p>
        </div>
    </header>
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8 fade-in">
        <?php if (isset($_GET['apply_status'])): ?><div class="mb-4 p-4 rounded-md <?php echo $_GET['apply_status'] == 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>"><?php if ($_GET['apply_status'] == 'success') echo "Anda berhasil melamar pekerjaan!";
                                                                                                                                                                                                    elseif ($_GET['apply_status'] == 'file_error') echo "Gagal mengunggah CV. Pastikan file adalah PDF/DOC/DOCX dan ukurannya di bawah 2MB.";
                                                                                                                                                                                                    elseif ($_GET['apply_status'] == 'exists') echo "Anda sudah pernah melamar pekerjaan ini.";
                                                                                                                                                                                                    else echo "Gagal melamar pekerjaan. Silakan coba lagi."; ?></div><?php endif; ?>
        <div class="space-y-6">
            <?php
            if (!empty($jobs)) {
                foreach ($jobs as $job) {
            ?>
                    <div class="card p-6">
                        <div class="flex flex-col md:flex-row justify-between">
                            <div>
                                <h3 class="text-xl font-bold text-indigo-700"><?php echo htmlspecialchars($job['judul']); ?></h3>
                                <p class="text-md font-semibold text-gray-800 mt-1"><?php echo htmlspecialchars($job['nama_perusahaan']); ?></p>
                                <p class="text-sm text-gray-500"><?php echo htmlspecialchars($job['lokasi']); ?></p>
                            </div>
                            <div class="mt-4 md:mt-0 md:text-right">
                                <p class="text-lg font-semibold text-green-600"><?php echo htmlspecialchars($job['gaji']); ?></p>
                                <p class="text-xs text-gray-400">Diposting: <?php echo date('d M Y', strtotime($job['posted_at'])); ?></p>
                            </div>
                        </div>
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <p class="text-gray-600 text-sm whitespace-pre-wrap"><?php echo nl2br(htmlspecialchars($job['deskripsi'])); ?></p>
                        </div>
                        <div class="mt-5 text-right">
                            <?php
                            // Logika untuk menampilkan tombol atau status
                            if ($job['application_status']) {
                                $status = $job['application_status'];
                                $status_text = '';
                                $status_class = '';
                                if ($status == 'pending') {
                                    $status_text = 'Pending';
                                    $status_class = 'status-pending';
                                } elseif ($status == 'accepted') {
                                    $status_text = 'Diterima';
                                    $status_class = 'status-accepted';
                                } elseif ($status == 'rejected') {
                                    $status_text = 'Ditolak';
                                    $status_class = 'status-rejected';
                                }
                                echo "<span class='status-badge $status_class'>$status_text</span>";
                            } else {
                                echo '<button data-job-id="' . $job['id'] . '" data-job-title="' . htmlspecialchars($job['judul']) . '" class="btn btn-primary apply-btn">Lamar Sekarang</button>';
                            }
                            ?>
                        </div>
                    </div>
            <?php }
            } else {
                echo "<div class='card text-center p-10'><p>Saat ini belum ada lowongan pekerjaan yang tersedia.</p></div>";
            } ?>
        </div>
    </main>
    <div id="applyModal" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-xl p-8 max-w-md w-full">
            <h3 class="text-2xl font-bold mb-2">Lamar Pekerjaan</h3>
            <p id="modalJobTitle" class="text-gray-600 mb-6"></p>
            <form action="apply_job.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="job_id" id="modalJobId">
                <div class="mb-4"><label for="cv" class="block text-sm font-medium text-gray-700 mb-2">Upload CV Anda</label><input type="file" name="cv" id="cv" required class="form-input file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    <p class="text-xs text-gray-500 mt-1">Tipe file yang diizinkan: PDF, DOC, DOCX. Maks 2MB.</p>
                </div>
                <div class="mt-6 flex justify-end"><button type="button" id="closeModal" class="btn btn-secondary mr-2">Batal</button><button type="submit" class="btn btn-primary">Kirim Lamaran</button></div>
            </form>
        </div>
    </div>
    <script>
        const applyModal = document.getElementById('applyModal');
        const closeModalBtn = document.getElementById('closeModal');
        const applyBtns = document.querySelectorAll('.apply-btn');
        const modalJobIdInput = document.getElementById('modalJobId');
        const modalJobTitle = document.getElementById('modalJobTitle');
        applyBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const jobId = btn.dataset.jobId;
                const jobTitle = btn.dataset.jobTitle;
                modalJobIdInput.value = jobId;
                modalJobTitle.textContent = jobTitle;
                applyModal.classList.remove('hidden');
            });
        });
        closeModalBtn.addEventListener('click', () => {
            applyModal.classList.add('hidden');
        });
        applyModal.addEventListener('click', (e) => {
            if (e.target === applyModal) {
                applyModal.classList.add('hidden');
            }
        });
    </script>
</body>

</html>