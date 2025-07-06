<?php
// virtual_file: index.php
// Deskripsi: File utama yang mengatur routing berdasarkan role user.

session_start();

// Cek apakah user sudah login atau belum
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Cek role user
if (isset($_SESSION["role"])) {
    if ($_SESSION["role"] == 'admin') {
        header("location: admin_dashboard.php");
    } else {
        header("location: user_dashboard.php");
    }
} else {
    header("location: user_dashboard.php");
}
exit;
?>
```

```php
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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

    if (empty($judul_err) && empty($nama_perusahaan_err) && empty($lokasi_err) && empty($deskripsi_err)) {
        if (isset($_POST['job_id']) && !empty($_POST['job_id'])) {
            $sql = "UPDATE jobs SET judul=?, nama_perusahaan=?, lokasi=?, deskripsi=?, gaji=? WHERE id=?";
            if ($stmt = mysqli_prepare($db, $sql)) {
                mysqli_stmt_bind_param($stmt, "sssssi", $judul, $nama_perusahaan, $lokasi, $deskripsi, $gaji, $_POST['job_id']);
                if (mysqli_stmt_execute($stmt)) {
                    header("location: admin_dashboard.php");
                    exit();
                }
            }
        } else {
            $sql = "INSERT INTO jobs (judul, nama_perusahaan, lokasi, deskripsi, gaji) VALUES (?, ?, ?, ?, ?)";
            if ($stmt = mysqli_prepare($db, $sql)) {
                mysqli_stmt_bind_param($stmt, "sssss", $judul, $nama_perusahaan, $lokasi, $deskripsi, $gaji);
                if (mysqli_stmt_execute($stmt)) {
                    header("location: admin_dashboard.php");
                    exit();
                }
            }
        }
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $sql = "DELETE FROM jobs WHERE id = ?";
    if ($stmt = mysqli_prepare($db, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $_GET['id']);
        if (mysqli_stmt_execute($stmt)) {
            header("location: admin_dashboard.php");
            exit();
        }
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $sql = "SELECT * FROM jobs WHERE id = ?";
    if ($stmt = mysqli_prepare($db, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $_GET['id']);
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
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
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - JobForU</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <nav class="navbar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex-shrink-0">
                    <h1 class="text-2xl font-bold text-indigo-600">JobForU (Admin)</h1>
                </div>
                <div class="flex items-center">
                    <span class="text-gray-700 mr-4">Selamat datang, <b><?php echo htmlspecialchars($_SESSION["nama_lengkap"]); ?></b>!</span>
                    <a href="logout.php" class="btn btn-danger text-sm py-2 px-3">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8 fade-in">
        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
            <h2 class="text-2xl font-bold mb-4 text-gray-800"><?php echo $form_title; ?></h2>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <input type="hidden" name="job_id" value="<?php echo $job_id_to_edit; ?>">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="judul" class="block text-sm font-medium text-gray-700">Judul Pekerjaan</label>
                        <input type="text" name="judul" id="judul" value="<?php echo htmlspecialchars($judul); ?>" class="form-input mt-1">
                        <span class="text-red-500 text-xs"><?php echo $judul_err; ?></span>
                    </div>
                    <div>
                        <label for="nama_perusahaan" class="block text-sm font-medium text-gray-700">Nama Perusahaan</label>
                        <input type="text" name="nama_perusahaan" id="nama_perusahaan" value="<?php echo htmlspecialchars($nama_perusahaan); ?>" class="form-input mt-1">
                        <span class="text-red-500 text-xs"><?php echo $nama_perusahaan_err; ?></span>
                    </div>
                    <div>
                        <label for="lokasi" class="block text-sm font-medium text-gray-700">Lokasi</label>
                        <input type="text" name="lokasi" id="lokasi" value="<?php echo htmlspecialchars($lokasi); ?>" class="form-input mt-1">
                        <span class="text-red-500 text-xs"><?php echo $lokasi_err; ?></span>
                    </div>
                    <div>
                        <label for="gaji" class="block text-sm font-medium text-gray-700">Gaji (opsional)</label>
                        <input type="text" name="gaji" id="gaji" value="<?php echo htmlspecialchars($gaji); ?>" class="form-input mt-1" placeholder="cth: Rp 5.000.000">
                    </div>
                    <div class="md:col-span-2">
                        <label for="deskripsi" class="block text-sm font-medium text-gray-700">Deskripsi Pekerjaan</label>
                        <textarea name="deskripsi" id="deskripsi" rows="5" class="form-input mt-1"><?php echo htmlspecialchars($deskripsi); ?></textarea>
                        <span class="text-red-500 text-xs"><?php echo $deskripsi_err; ?></span>
                    </div>
                </div>
                <div class="mt-6 flex justify-end">
                    <?php if ($job_id_to_edit): ?>
                        <a href="admin_dashboard.php" class="btn btn-secondary mr-2">Batal</a>
                    <?php endif; ?>
                    <button type="submit" class="btn btn-primary">
                        <?php echo ($job_id_to_edit) ? 'Simpan Perubahan' : 'Posting Lowongan'; ?>
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-x-auto">
            <div class="p-4 border-b">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Daftar Lowongan Terposting</h3>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Perusahaan</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php
                    $sql_jobs = "SELECT id, judul, nama_perusahaan, lokasi FROM jobs ORDER BY posted_at DESC";
                    $result_jobs = mysqli_query($db, $sql_jobs);
                    if (mysqli_num_rows($result_jobs) > 0) {
                        while ($row = mysqli_fetch_assoc($result_jobs)) {
                            echo "<tr>";
                            echo "<td class='px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900'>" . htmlspecialchars($row['judul']) . "</td>";
                            echo "<td class='px-6 py-4 whitespace-nowrap text-sm text-gray-600'>" . htmlspecialchars($row['nama_perusahaan']) . "</td>";
                            echo "<td class='px-6 py-4 whitespace-nowrap text-sm text-gray-600'>" . htmlspecialchars($row['lokasi']) . "</td>";
                            echo "<td class='px-6 py-4 whitespace-nowrap text-right text-sm font-medium'>";
                            echo "<a href='admin_dashboard.php?action=edit&id=" . $row['id'] . "' class='text-indigo-600 hover:text-indigo-900 mr-3'>Edit</a>";
                            echo "<a href='admin_dashboard.php?action=delete&id=" . $row['id'] . "' class='text-red-600 hover:text-red-900' onclick='return confirm(\"Yakin ingin menghapus?\");'>Hapus</a>";
                            echo "</td></tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4' class='text-center py-4'>Belum ada lowongan yang diposting.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>
</body>

</html>