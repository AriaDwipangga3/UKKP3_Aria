# dwipanggaa788@gmail.com
# github.com/AriaDwipangga3
dwipanggaa788@gmail.com
github.com/AriaDwipangga3

# login-app (Kedai Rasa)

Aplikasi Laravel sederhana: **login, register, dan CRUD akun dengan persetujuan (ACC) admin**.

## Alur

1. Customer mendaftar di `/register` -> akun berstatus **pending** (belum bisa login).
2. Admin membuka **Dashboard** lalu menekan **Terima** (atau **Tolak**) pada pendaftar.
3. Setelah di-ACC, customer bisa login di `/login`.

## Role

Hanya 2 role:

| Role     | Bisa apa                                                          |
|----------|-------------------------------------------------------------------|
| admin    | Dashboard: lihat pendaftar, Terima / Tolak / Hapus customer       |
| customer | Dashboard sendiri (hanya setelah di-ACC admin)                    |

Akun admin tidak bisa daftar lewat form; dibuat lewat seeder.

## Cara menjalankan

```bash
composer install
cp .env.example .env
php artisan key:generate
# buat database kosong bernama "serkom" di MySQL dulu, contoh:
#   mysql -u root -e "CREATE DATABASE serkom CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
# lalu sesuaikan DB_USERNAME / DB_PASSWORD di file .env
php artisan migrate --seed
php artisan serve
```

Buka http://localhost:8000. Login admin awal (dari `.env`):

- Email: `admin@example.com`
- Password: `admin12345`  **(segera ganti!)**

Menjalankan test: `php artisan test`

## Peta file

| File                                             | Fungsi                                              |
|--------------------------------------------------|-----------------------------------------------------|
| `routes/web.php`                                 | Daftar semua alamat halaman + middleware-nya        |
| `app/Http/Controllers/Auth/AuthController.php`   | Login, register, logout                             |
| `app/Http/Controllers/Admin/UserController.php`  | Aksi Terima / Tolak / Hapus customer                |
| `app/Http/Controllers/DashboardController.php`   | Dashboard admin (daftar + filter) / customer        |
| `app/Http/Middleware/EnsureUserHasRole.php`      | Batasi halaman per role                             |
| `app/Http/Middleware/EnsureUserIsApproved.php`   | Hanya akun yang sudah di-ACC boleh masuk            |
| `app/Models/User.php`                            | Model user (role, status, helper)                   |
| `database/migrations/2026_10_19_...`             | Kolom `role`, `status`, `approved_at`               |
| `database/seeders/DatabaseSeeder.php`            | Membuat akun admin awal                             |
| `resources/views/...`                            | Tampilan (auth, admin, customer, partials)          |
| `public/css/dashboard.css`, `public/js/dashboard.js` | Gaya & script tampilan                         |

