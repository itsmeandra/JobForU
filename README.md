# Dokumentasi Proyek Website Lowongan Kerja "JobForU"

## 1. Pendahuluan

**JobForU** adalah aplikasi web portal lowongan pekerjaan yang dibangun menggunakan PHP native dan MySQL. Aplikasi ini dirancang untuk menjembatani pencari kerja dengan perusahaan yang membuka lowongan. Proyek ini memiliki dua peran pengguna utama: **Admin** yang bertugas mengelola data lowongan, dan **User** (pencari kerja) yang dapat melihat dan melamar lowongan yang tersedia.

Aplikasi ini memiliki antarmuka yang modern dan responsif berkat penggunaan **Tailwind CSS**, serta fungsionalitas backend yang mencakup sistem otentikasi, manajemen data (CRUD), dan fitur interaktif seperti upload file dan pembaruan status lamaran.

## 2. Fitur Utama

Aplikasi ini memiliki serangkaian fitur yang terbagi berdasarkan peran pengguna.

### Fitur untuk Admin
- **Dashboard Statistik**: Menampilkan ringkasan data penting seperti jumlah total lowongan, total lamaran masuk, dan jumlah pengguna terdaftar.
- **Manajemen Lowongan (CRUD)**:
    - **Create**: Menambahkan data lowongan pekerjaan baru melalui form interaktif.
    - **Read**: Melihat daftar semua lowongan yang telah diposting dalam format tabel yang rapi.
    - **Update**: Mengedit informasi lowongan pekerjaan yang sudah ada.
    - **Delete**: Menghapus data lowongan pekerjaan dari database.
- **Manajemen Pelamar**:
    - **Lihat Semua Pelamar**: Mengakses halaman terpusat yang menampilkan semua lamaran dari seluruh lowongan.
    - **Lihat Pelamar per Lowongan**: Mengakses halaman spesifik untuk melihat daftar pelamar pada satu lowongan tertentu.
    - **Unduh CV**: Mengunduh file CV yang diunggah oleh setiap pelamar.
    - **Terima/Tolak Lamaran**: Mengubah status lamaran pengguna menjadi "Diterima" atau "Ditolak".

### Fitur untuk User (Pencari Kerja)
- **Otentikasi**:
    - **Register**: Mendaftarkan akun baru sebagai pencari kerja.
    - **Login**: Masuk ke dalam sistem untuk mengakses fitur utama.
- **Dashboard Lowongan**: Menampilkan semua lowongan pekerjaan yang tersedia dalam format kartu (card) yang informatif dan menarik.
- **Proses Lamar Kerja**:
    - **Modal Interaktif**: Mengklik tombol "Lamar Sekarang" akan membuka pop-up (modal).
    - **Upload CV**: Pengguna wajib mengunggah CV (format PDF, DOC, DOCX) melalui modal sebelum mengirim lamaran.
- **Melihat Status Lamaran**: Pengguna dapat melihat status lamaran mereka secara langsung di dashboard untuk setiap pekerjaan yang telah dilamar (misalnya: "Pending", "Diterima", "Ditolak").

## 3. Struktur Database

Database yang digunakan adalah `jobforu_db` yang terdiri dari tiga tabel utama.

1.  **`users`**
    - Menyimpan data pengguna, baik admin maupun user biasa.
    - `id`, `nama_lengkap`, `email`, `password`, `role`, `created_at`

2.  **`jobs`**
    - Menyimpan semua data terkait lowongan pekerjaan yang diposting oleh admin.
    - `id`, `judul`, `nama_perusahaan`, `lokasi`, `deskripsi`, `gaji`, `posted_at`

3.  **`applications`**
    - Tabel penghubung antara `users` dan `jobs`.
    - `id`, `job_id`, `user_id`, `cv_path`, `status`, `applied_at`

## 4. Struktur File Proyek

- **`config.php`**: Konfigurasi koneksi database.
- **`register.php` / `login.php` / `logout.php`**: Logika otentikasi pengguna.
- **`index.php`**: Router sederhana setelah login.
- **`admin_dashboard.php`**: Halaman utama Admin untuk CRUD lowongan.
- **`view_applications.php`**: Halaman Admin untuk melihat semua lamaran.
- **`view_job_applicants.php`**: Halaman Admin untuk melihat pelamar per lowongan.
- **`update_status.php`**: Backend untuk proses terima/tolak lamaran.
- **`user_dashboard.php`**: Halaman utama User untuk melihat lowongan.
- **`apply_job.php`**: Backend untuk proses lamaran dan upload CV.
- **`style.css`**: Stylesheet kustom untuk desain.
- **`/uploads/cv/`**: Direktori penyimpanan file CV.

## 5. Instalasi dan Konfigurasi

1.  **Web Server**: Gunakan XAMPP atau sejenisnya.
2.  **Database**: Buat database `jobforu_db` dan import semua tabel. Jalankan `ALTER TABLE` untuk `applications` jika diperlukan.
3.  **Konfigurasi**: Sesuaikan detail database di `config.php`.
4.  **Folder Upload**: Buat folder `uploads/cv/` di root proyek dan pastikan folder tersebut *writable*.
5.  **Akun Admin**: Tambahkan user dengan `role` 'admin' secara manual di tabel `users`.
