# Bantuin

**Bantuin** adalah platform marketplace jasa berbasis web. Pengguna yang **membutuhkan jasa** dapat memasang permintaan, penyedia jasa dapat **menawarkan layanannya**, lalu keduanya saling terhubung melalui **chat**. Setelah pekerjaan selesai, customer dapat memberi **ulasan** kepada penyedia jasa.

![PHP](https://img.shields.io/badge/PHP-%5E8.3-777BB4?logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?logo=laravel&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-4_(CDN)-06B6D4?logo=tailwindcss&logoColor=white)
![License](https://img.shields.io/badge/license-MIT-green)

---

## Daftar Isi

- [Fitur](#fitur)
- [Peran Pengguna](#peran-pengguna)
- [Teknologi](#teknologi)
- [Persyaratan](#persyaratan)
- [Instalasi](#instalasi)
- [Akun Demo](#akun-demo)
- [Menjalankan Aplikasi](#menjalankan-aplikasi)
- [Struktur Proyek](#struktur-proyek)
- [Daftar Route](#daftar-route)
- [REST API](#rest-api)
- [Cara Kerja Chat dan Notifikasi](#cara-kerja-chat-dan-notifikasi)
- [Pengujian](#pengujian)
- [Lisensi](#lisensi)

---

## Fitur

**Umum**
- Registrasi dan login dengan pemilihan peran, serta pembatasan percobaan login (`throttle:5,1`).
- Halaman publik **Butuh Jasa** dan **Menyediakan Jasa** dengan pencarian, filter kategori, dan filter mode kerja.
- Profil pengguna (publik dan privat), ubah profil, dan ganti password.
- Tombol **Keluar (logout)** tersedia di navbar dan di dashboard.

**Customer**
- Membuat, mengubah, dan menghapus permintaan jasa ("Butuh Jasa").
- Memulai chat dengan penyedia jasa, menandai percakapan **selesai**, dan memberi **ulasan** (rating dan komentar).

**Provider (Penyedia Jasa)**
- Membuat, mengubah, dan menghapus penawaran jasa ("Menyediakan Jasa").
- Membalas chat dari customer.

**Chat dan Notifikasi**
- Chat antar customer dan provider dengan pembaruan pesan otomatis (AJAX polling).
- Notifikasi pesan baru yang tersimpan di database, dengan fitur "tandai semua dibaca".

**Admin**
- Dashboard dengan ringkasan statistik dan data terbaru.
- Manajemen kategori (tambah dan ubah).
- **Kelola Jasa**: melihat seluruh permintaan dan penawaran jasa, lalu menghapusnya **satu per satu** (per jasa, bukan per kategori).

---

## Peran Pengguna

| Peran | Hak akses utama |
|---|---|
| `customer` | Membuat permintaan jasa, chat, menyelesaikan percakapan, memberi ulasan |
| `provider` | Membuat penawaran jasa, chat |
| `admin` | Dashboard admin, kelola kategori, hapus jasa per item |

Setelah login, pengguna diarahkan otomatis ke dashboard sesuai perannya melalui route `/dashboard`. Akses dibatasi dengan middleware `role:*`, **Policy** (`ServiceRequestPolicy`, `ServiceOfferPolicy`, `ConversationPolicy`), dan **Gate**.

---

## Teknologi

| Bagian | Teknologi |
|---|---|
| Backend | PHP ^8.3, Laravel 13 |
| Autentikasi API | Laravel Sanctum |
| Database | SQLite (default), dapat diganti MySQL/PostgreSQL |
| Frontend | Blade, Tailwind CSS 4 (via CDN), Google Fonts |
| Queue / Session / Cache | Driver `database` |
| Testing | PHPUnit 12 |
| Code style | Laravel Pint |

---

## Persyaratan

- PHP **8.3** atau lebih baru (ekstensi umum Laravel, termasuk `pdo_sqlite`)
- [Composer](https://getcomposer.org/)
- Koneksi internet (Tailwind CSS dan font dimuat dari CDN)

> Node.js dan `npm install` **tidak diperlukan**.

---

## Instalasi

```bash
# 1. Clone repositori
git clone <url-repositori-anda>.git
cd Bantuin_project

# 2. Instal dependensi PHP
composer install

# 3. Salin file environment lalu buat app key
cp .env.example .env
php artisan key:generate

# 4. Siapkan database SQLite (jika file belum ada)
touch database/database.sqlite

# 5. Jalankan migrasi dan seeder
php artisan migrate --seed
```

> Untuk MySQL, ubah bagian `DB_*` di `.env` (`DB_CONNECTION=mysql`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`), lalu jalankan ulang `php artisan migrate --seed`.

---

## Akun Demo

Seeder membuat tiga akun berikut (password semuanya `password`), beserta contoh kategori, jasa, percakapan, dan ulasan:

| Peran | Email | Password |
|---|---|---|
| Admin | `admin@bantuin.test` | `password` |
| Customer | `customer@bantuin.test` | `password` |
| Provider | `provider@bantuin.test` | `password` |

> Ganti password akun demo sebelum aplikasi dipakai di lingkungan produksi.

---

## Menjalankan Aplikasi

```bash
php artisan serve
```

Buka `http://127.0.0.1:8000` di browser.

Pastikan komputer terhubung ke internet agar Tailwind CSS dan font dapat dimuat dari CDN. Tanpa koneksi, tampilan tidak akan tampil dengan benar.

---

## Struktur Proyek

```
app/
├── Events/                  # MessageSent (ShouldBroadcast)
├── Http/
│   ├── Controllers/
│   │   ├── Admin/           # DashboardController, CategoryController, ServiceController
│   │   ├── Api/             # Controller REST API (Sanctum)
│   │   └── ...              # Auth, Conversation, Notification, Profile, Review, ServiceOffer, ServiceRequest
│   └── Middleware/          # RoleMiddleware
├── Models/                  # User, Category, ServiceRequest, ServiceOffer, Conversation, Message, Review
├── Notifications/           # NewMessage (channel database)
├── Policies/                # Otorisasi kepemilikan data
├── Providers/               # Registrasi Policy dan Gate
└── Services/                # MessageSender, ConversationStarter
database/
├── factories/
├── migrations/
└── seeders/
resources/views/
├── admin/                   # dashboard, categories, services
├── chat/                    # daftar chat dan ruang percakapan
├── notifications/
├── components/              # navbar, dll.
└── layouts/                 # layout utama (Tailwind via CDN)
routes/
├── web.php                  # route web
├── auth.php                 # login, register, logout
├── api.php                  # REST API
└── channels.php             # otorisasi channel broadcast
tests/Feature/               # pengujian fitur
```

---

## Daftar Route

### Publik

| Method | URL | Keterangan |
|---|---|---|
| GET | `/` | Beranda |
| GET | `/butuh-jasa`, `/butuh-jasa/{id}` | Daftar dan detail permintaan jasa |
| GET | `/menyediakan-jasa`, `/menyediakan-jasa/{id}` | Daftar dan detail penawaran jasa |
| GET | `/pengguna/{user}` | Profil publik |
| GET/POST | `/login`, `/register` | Autentikasi (khusus tamu) |

### Pengguna Login

| Method | URL | Keterangan |
|---|---|---|
| GET | `/dashboard` | Pengalihan dashboard sesuai peran |
| GET/POST/PUT | `/service-requests/...` | CRUD permintaan jasa (customer) |
| GET/POST/PUT | `/service-offers/...` | CRUD penawaran jasa (provider) |
| GET/POST | `/chat`, `/chat/{id}`, `/chat/{id}/pesan` | Chat dan kirim/ambil pesan |
| POST | `/chat/{id}/selesai`, `/chat/{id}/ulasan` | Selesaikan percakapan dan beri ulasan |
| GET/POST | `/notifikasi`, `/notifikasi/{id}`, `/notifikasi/baca-semua` | Notifikasi |
| GET/PUT | `/profil`, `/profil/password` | Profil dan ganti password |
| POST | `/logout` | Keluar |

### Admin (`role:admin`)

| Method | URL | Keterangan |
|---|---|---|
| GET | `/admin/dashboard` | Dashboard admin |
| GET/POST/PUT | `/admin/kategori` | Daftar, tambah, dan ubah kategori |
| GET | `/admin/jasa?tipe=butuh\|sedia` | Daftar jasa (dengan pencarian `q`) |
| DELETE | `/admin/jasa/butuh/{id}` | Hapus satu permintaan jasa |
| DELETE | `/admin/jasa/menyediakan/{id}` | Hapus satu penawaran jasa |

Daftar lengkap dapat dilihat dengan `php artisan route:list`.

---

## REST API

API menggunakan **Laravel Sanctum** (token Bearer). Seluruh endpoint berada di bawah prefix `/api`.

| Method | Endpoint | Auth | Keterangan |
|---|---|---|---|
| POST | `/api/login` | - | Login dan mendapatkan token |
| GET | `/api/service-requests`, `/api/service-requests/{id}` | - | Daftar dan detail permintaan jasa |
| GET | `/api/service-offers`, `/api/service-offers/{id}` | - | Daftar dan detail penawaran jasa |
| GET | `/api/me` | Sanctum | Data pengguna saat ini |
| POST | `/api/logout` | Sanctum | Logout dan cabut token |
| POST | `/api/service-requests` | Sanctum | Buat permintaan jasa |
| POST | `/api/service-offers` | Sanctum | Buat penawaran jasa |
| GET/POST | `/api/conversations` | Sanctum | Daftar dan mulai percakapan |
| GET | `/api/conversations/{id}` | Sanctum | Detail percakapan |
| GET/POST | `/api/conversations/{id}/messages` | Sanctum | Ambil dan kirim pesan |

Contoh penggunaan:

```bash
# Login
curl -X POST http://127.0.0.1:8000/api/login \
  -H "Accept: application/json" \
  -d "email=customer@bantuin.test" -d "password=password"

# Memakai token
curl http://127.0.0.1:8000/api/me \
  -H "Accept: application/json" \
  -H "Authorization: Bearer <TOKEN>"
```

---

## Cara Kerja Chat dan Notifikasi

**Chat.** Pesan dikirim lewat AJAX (`fetch` POST) dan diperbarui lewat **polling** setiap 3 detik ke `GET /chat/{id}/pesan?after={id_pesan_terakhir}`. Pengiriman pesan dibatasi 30 kali per menit (`throttle:30,1`), dan hanya peserta percakapan yang boleh membaca atau mengirim pesan (`ConversationPolicy`).

**Notifikasi.** Saat ada pesan baru, penerima mendapat notifikasi **Laravel Notifications** dengan channel `database` (class `NewMessage`). Notifikasi tersimpan di tabel `notifications` dan ditandai dibaca saat dibuka atau saat percakapan terkait dibuka. Satu percakapan hanya menghasilkan satu notifikasi belum-dibaca agar tidak membanjiri. Notifikasi dibaca saat halaman dimuat (belum real-time).

**Broadcasting.** Event `MessageSent` (`ShouldBroadcast`, `PrivateChannel('conversation.{id}')`) sudah disiapkan, tetapi `BROADCAST_CONNECTION` masih `log`. Untuk pembaruan real-time penuh tanpa polling, pasang [Laravel Reverb](https://laravel.com/docs/reverb) dan Laravel Echo, lalu ubah driver broadcast di `.env`.

---

## Pengujian

```bash
php artisan test
# atau
composer test
```

Menjalankan satu kelompok pengujian:

```bash
php artisan test --filter=AdminServiceDeleteTest
php artisan test --filter=ChatTest
```

Cakupan pengujian di `tests/Feature` meliputi autentikasi, otorisasi dan CRUD, listing publik, chat, notifikasi, profil, ulasan, REST API, serta fitur admin (dashboard, kategori, dan hapus jasa).

Merapikan kode dengan Pint:

```bash
./vendor/bin/pint
```

---

## Lisensi

Proyek ini dirilis di bawah lisensi [MIT](https://opensource.org/licenses/MIT), mengikuti lisensi kerangka Laravel.
