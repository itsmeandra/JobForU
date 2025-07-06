<?php
// virtual_file: admin_dashboard.php
// Deskripsi: Halaman dashboard untuk admin mengelola (CRUD) lowongan kerja.

session_start();
require_once "config.php";

// Proteksi halaman: hanya admin yang bisa akses
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'admin') {
    header("location: login.php");
    exit;
}

// -- LOGIC UNTUK CRUD --
$judul = $nama_perusahaan = $lokasi = $deskripsi = $gaji = "";
$judul_err = $nama_perusahaan_err = $lokasi_err = $deskripsi_err = "";
$form_title = "Tambah Lowongan Baru";
$job_id_to_edit = null;

// Ambil pesan sukses dari session jika ada
$success_message = '';
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']); // Hapus pesan setelah ditampilkan
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validasi input
    if (empty(trim($_POST["judul"]))) {
        $judul_err = "Judul tidak boleh kosong.";
    } else {
        $judul = trim($_POST["judul"]);
    }
    if (empty(trim($_POST["nama_perusahaan"]))) {
        $nama_perusahaan_err = "Nama perusahaan tidak boleh kosong.";
    } else {
        $nama_perusahaan = trim($_POST["nama_perusahaan"]);
    }
    if (empty(trim($_POST["lokasi"]))) {
        $lokasi_err = "Lokasi tidak boleh kosong.";
    } else {
        $lokasi = trim($_POST["lokasi"]);
    }
    if (empty(trim($_POST["deskripsi"]))) {
        $deskripsi_err = "Deskripsi tidak boleh kosong.";
    } else {
        $deskripsi = trim($_POST["deskripsi"]);
    }
    $gaji = trim($_POST["gaji"]);

    // Proses data jika tidak ada error validasi
    if (empty($judul_err) && empty($nama_perusahaan_err) && empty($lokasi_err) && empty($deskripsi_err)) {
        // Cek apakah ini proses UPDATE atau INSERT
        if (isset($_POST['job_id']) && !empty($_POST['job_id'])) {
            // Proses UPDATE
            $sql = "UPDATE jobs SET judul=?, nama_perusahaan=?, lokasi=?, deskripsi=?, gaji=? WHERE id=?";
            if ($stmt = mysqli_prepare($db, $sql)) {
                mysqli_stmt_bind_param($stmt, "sssssi", $judul, $nama_perusahaan, $lokasi, $deskripsi, $gaji, $_POST['job_id']);
                if (mysqli_stmt_execute($stmt)) {
                    $_SESSION['success_message'] = "Lowongan berhasil diperbarui!";
                    header("location: admin_dashboard.php");
                    exit();
                }
            }
        } else {
            // Proses INSERT
            $sql = "INSERT INTO jobs (judul, nama_perusahaan, lokasi, deskripsi, gaji) VALUES (?, ?, ?, ?, ?)";
            if ($stmt = mysqli_prepare($db, $sql)) {
                mysqli_stmt_bind_param($stmt, "sssss", $judul, $nama_perusahaan, $lokasi, $deskripsi, $gaji);
                if (mysqli_stmt_execute($stmt)) {
                    $_SESSION['success_message'] = "Lowongan baru berhasil diposting!";
                    header("location: admin_dashboard.php");
                    exit();
                }
            }
        }
    }
}

// Proses HAPUS data
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $sql = "DELETE FROM jobs WHERE id = ?";
    if ($stmt = mysqli_prepare($db, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $_GET['id']);
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success_message'] = "Lowongan berhasil dihapus.";
            header("location: admin_dashboard.php");
            exit();
        }
    }
}

// Proses untuk EDIT (mengambil data untuk ditampilkan di form)
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $sql = "SELECT * FROM jobs WHERE id = ?";
    if ($stmt = mysqli_prepare($db, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $_GET['id']);
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            if (mysqli_num_rows($result) == 1) {
                $job = mysqli_fetch_assoc($result);
                $judul = $job['judul'];
                $nama_perusahaan = $job['nama_perusahaan'];
                $lokasi = $job['lokasi'];
                $deskripsi = $job['deskripsi'];
                $gaji = $job['gaji'];
                $job_id_to_edit = $job['id'];
                $form_title = "Edit Lowongan Pekerjaan";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - JobForU</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
            /* bg-gray-100 */
        }
    </style>
</head>

<body class="antialiased">
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex-shrink-0">
                    <h1 class="text-2xl font-bold text-indigo-600">JobForU (Admin)</h1>
                </div>
                <div class="flex items-center">
                    <span class="text-gray-700 mr-4 hidden sm:block">Selamat datang, <b><?php echo htmlspecialchars($_SESSION["nama_lengkap"]); ?></b>!</span>
                    <a href="logout.php" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">

        <?php if (!empty($success_message)): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md shadow-sm mb-6" role="alert">
                <p class="font-bold">Sukses!</p>
                <p><?php echo htmlspecialchars($success_message); ?></p>
            </div>
        <?php endif; ?>

        <div class="bg-white p-6 sm:p-8 rounded-xl shadow-lg mb-8">
            <h2 class="text-2xl font-bold mb-6 text-gray-800"><?php echo $form_title; ?></h2>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <input type="hidden" name="job_id" value="<?php echo $job_id_to_edit; ?>">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="judul" class="block text-sm font-medium text-gray-700 mb-1">Judul Pekerjaan</label>
                        <input type="text" name="judul" id="judul" value="<?php echo htmlspecialchars($judul); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-xs mt-1"><?php echo $judul_err; ?></span>
                    </div>
                    <div>
                        <label for="nama_perusahaan" class="block text-sm font-medium text-gray-700 mb-1">Nama Perusahaan</label>
                        <input type="text" name="nama_perusahaan" id="nama_perusahaan" value="<?php echo htmlspecialchars($nama_perusahaan); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-xs mt-1"><?php echo $nama_perusahaan_err; ?></span>
                    </div>
                    <div>
                        <label for="lokasi" class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
                        <input type="text" name="lokasi" id="lokasi" value="<?php echo htmlspecialchars($lokasi); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="cth: Jakarta, Indonesia">
                        <span class="text-red-500 text-xs mt-1"><?php echo $lokasi_err; ?></span>
                    </div>
                    <div>
                        <label for="gaji" class="block text-sm font-medium text-gray-700 mb-1">Gaji (opsional)</label>
                        <input type="text" name="gaji" id="gaji" value="<?php echo htmlspecialchars($gaji); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="cth: Rp 5.000.000 - Rp 7.000.000">
                    </div>
                    <div class="md:col-span-2">
                        <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Pekerjaan</label>
                        <textarea name="deskripsi" id="deskripsi" rows="5" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"><?php echo htmlspecialchars($deskripsi); ?></textarea>
                        <span class="text-red-500 text-xs mt-1"><?php echo $deskripsi_err; ?></span>
                    </div>
                </div>
                <div class="mt-6 flex justify-end items-center">
                    <?php if ($job_id_to_edit): ?>
                        <a href="admin_dashboard.php" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-3">
                            Batal
                        </a>
                    <?php endif; ?>
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <?php echo ($job_id_to_edit) ? 'Simpan Perubahan' : 'Posting Lowongan'; ?>
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="p-4 border-b">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Daftar Lowongan Terposting</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Judul</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Perusahaan</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Lokasi</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Diposting</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php
                        $sql_jobs = "SELECT id, judul, nama_perusahaan, lokasi, posted_at FROM jobs ORDER BY posted_at DESC";
                        $result_jobs = mysqli_query($db, $sql_jobs);
                        if (mysqli_num_rows($result_jobs) > 0) {
                            while ($row = mysqli_fetch_assoc($result_jobs)) {
                                echo "<tr class='hover:bg-gray-50 transition-colors duration-200'>";
                                echo "<td class='px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900'>" . htmlspecialchars($row['judul']) . "</td>";
                                echo "<td class='px-6 py-4 whitespace-nowrap text-sm text-gray-600'>" . htmlspecialchars($row['nama_perusahaan']) . "</td>";
                                echo "<td class='px-6 py-4 whitespace-nowrap text-sm text-gray-600'>" . htmlspecialchars($row['lokasi']) . "</td>";
                                // Menggunakan objek DateTime untuk format yang lebih modern
                                echo "<td class='px-6 py-4 whitespace-nowrap text-sm text-gray-600'>" . (new DateTime($row['posted_at']))->format('d M Y') . "</td>";
                                echo "<td class='px-6 py-4 whitespace-nowrap text-right text-sm font-medium'>";
                                echo "<a href='admin_dashboard.php?action=edit&id=" . $row['id'] . "#' class='text-indigo-600 hover:text-indigo-900 mr-4 font-semibold'>Edit</a>";
                                echo "<a href='admin_dashboard.php?action=delete&id=" . $row['id'] . "' class='text-red-600 hover:text-red-900 mr-4 font-semibold' onclick='return confirm(\"Apakah Anda yakin ingin menghapus lowongan ini?\");'>Hapus</a>";
                                echo "<a href='view_job_applicants.php?job_id=" . $row['id'] . "' class='text-green-600 hover:text-indigo-900 mr-4 font-semibold'>Lihat Lamran</a>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center py-10 text-gray-500'>Belum ada lowongan yang diposting.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>

</html>