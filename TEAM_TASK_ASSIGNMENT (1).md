# TEAM_TASK_ASSIGNMENT.md — Pembagian Tugas Tim Project Bantuin

> **Fungsi:** menjelaskan *siapa mengerjakan apa*: tahap, folder, file Backend (PHP), file Frontend (Blade), migration/tabel/model, batas tanggung jawab, dan hubungan antaranggota.
>
> **Versi ini menggantikan versi sebelumnya (Anggota A–E per fitur).** Pembagian kini mengikuti instruksi terbaru: **5 peran = Backend 1, Backend 2, Backend 3, Frontend 1, Frontend 2** dengan Tahap 1–7 versi baru (Bagian 3).
>
> **Dokumen pasangan:** `PROJECT_PRD.md` (alur aplikasi, fitur, database, aturan akses). Catatan: PRD masih memakai label A–E pada kolom "Pemilik"; **petakan memakai Bagian 15** sampai PRD diperbarui.

---

## 1. Cara Membaca

| Tanda | Arti |
|---|---|
| Nama file **tanpa tanda** | Disebut di dokumentasi asli project. |
| **†** | **Nama saran** (diturunkan dari nama route/konvensi Laravel; dokumentasi hanya menyebut foldernya). Jika berkas serupa sudah ada dengan nama lain, pakai yang sudah ada. |
| **[SEPAKATI]** | Tidak dijelaskan di instruksi/dokumentasi; **putuskan bersama tim**, jangan diputuskan sepihak (Bagian 14). |
| **[PENETAPAN]** | Komponen yang tidak disebut di instruksi terbaru tetapi **ada di dokumentasi project**; pemiliknya ditetapkan di sini agar tidak ada file tanpa penanggung jawab. Dapat diubah tim. |

Nama anggota belum ada: **ganti label peran dengan nama anggota** pada Bagian 2.

---

## 2. Ringkasan Peran

| Peran | Nama (isi) | Kategori | Fokus | Tahap |
|---|---|---|---|---|
| **Backend 1** | _…_ | Database & Authentication | Fondasi dan autentikasi | 1, 2, 3 |
| **Backend 2** | _…_ | Core Features | Fitur marketplace: Butuh Jasa dan Menyediakan Jasa | 4, 5 |
| **Backend 3** | _…_ | Chat & Integration | Chat dan integrasi backend | 6, 7 |
| **Frontend 1** | _…_ | UI Marketplace | Beranda dan marketplace | mengikuti Tahap 4–5 |
| **Frontend 2** | _…_ | UI Account & Interaction | Akun dan interaksi | mengikuti Tahap 3, 4–5, 6 |

---

## 3. Tahap Pengerjaan dan Dependensi

| Tahap | Pekerjaan | Pemilik backend | Pemilik frontend | Syarat sebelum mulai |
|---|---|---|---|---|
| **Persiapan** | PHP 8.3+, Composer (tanpa Node.js), MySQL lokal, `.env` dari `.env.example`, storage link, baca dokumen | Semua | Semua | — |
| **1** | Database foundation: migration dan struktur tabel | Backend 1 | — (Frontend 1 boleh mulai layout/komponen statis) | Persiapan |
| **2** | Model dan relasi Eloquent | Backend 1 | — | Tahap 1 |
| **3** | Authentication dan role: registrasi, login, logout, middleware, hak akses | Backend 1 | Frontend 2 (login, registrasi) | Tahap 1–2 |
| **4** | Backend Butuh Jasa: CRUD permintaan, validasi, status, otorisasi | Backend 2 | Frontend 1 (daftar, detail), Frontend 2 (form) | Tahap 3 |
| **5** | Backend Menyediakan Jasa: CRUD penawaran, validasi, status, otorisasi | Backend 2 | Frontend 1 (daftar, detail), Frontend 2 (form) | Tahap 3 (paralel/menyusul Tahap 4) |
| **6** | Backend chat: percakapan, kirim pesan, pemeriksaan hak akses | Backend 3 | Frontend 2 (halaman chat) | Tahap 4 dan/atau 5 (butuh listing) |
| **7** | Integrasi backend: route, controller, middleware, alur antarfitur; pengujian integrasi dan penyempurnaan bersama | Backend 3 (koordinator) + semua | Semua | Tahap 1–6 di-merge |

**Aturan urutan:** Backend 2 baru mulai setelah **Tahap 3 di-merge** (butuh role, middleware, model). Backend 3 baru mulai setelah ada listing dari Tahap 4/5. **Frontend tidak menunggu backend selesai untuk memulai tampilan**: bangun halaman dengan data contoh (dummy) mengikuti kontrak data (Bagian 12), lalu sambungkan ke data nyata saat controller di-merge.

### 3.1 Serah-terima antaranggota

| Hasil yang diserahkan | Dari | Untuk | Dibutuhkan sebelum |
|---|---|---|---|
| Seluruh migration + `DatabaseSeeder` | Backend 1 | Semua | Tahap 2–7 |
| Model + relasi (7 model) | Backend 1 | Backend 2, 3 | Tahap 4 dan 6 |
| `RoleMiddleware` + alias `role`, `AuthController`, route auth | Backend 1 | Backend 2, 3; Frontend 2 | Tahap 4 |
| `layouts/app.blade.php`, navbar, footer, partial kartu/rating | Frontend 1 | Semua view lain | Halaman apa pun yang memakai layout |
| `partials/form-field`†, `partials/avatar`† | Frontend 2 | Frontend 1, semua form | Form apa pun |
| Controller + route + data view Butuh Jasa/Menyediakan Jasa | Backend 2 | Frontend 1, 2 | Penyambungan data nyata |
| `ConversationStarter`, `MessageSender`, endpoint chat | Backend 3 | Frontend 2; API | Tahap 6 |
| Tombol "mulai chat" pada detail listing | Frontend 1 | Backend 3 (route `chat.store`) | Tahap 6 |

---

## 4. Aturan Pemilik File (Hindari Tumpang Tindih)

1. **Controller, Model, Request, Policy, Middleware, Service, Migration, Route = Backend.** **File `.blade.php` = Frontend.** Backend tidak mengedit Blade; Frontend tidak mengedit PHP.
2. **Satu file, satu pemilik.** Pemilik file ada di tabel tiap peran. Perubahan oleh orang lain hanya lewat PR yang ditinjau pemilik.
3. **Model:** Backend 1 membuat semua model + relasi pada **Tahap 2**. Setelah di-merge, **kepemilikan berpindah**: `User`, `Category` tetap Backend 1; `ServiceRequest`, `ServiceOffer` → **Backend 2**; `Conversation`, `Message`, `Review` → **Backend 3**.
4. **Migration:** seluruh 16 migration dibuat Backend 1 (Tahap 1). Perubahan skema berikutnya = **migration baru** (dibuat pemilik fitur, ditinjau Backend 1); jangan mengedit migration lama.
5. **Folder `resources/views/requests/` dan `offers/` dipakai dua frontend, tetapi per berkas punya satu pemilik:** Frontend 1 = `index`, `show`; Frontend 2 = `create`, `edit`, `_form`.
6. **Route:** `routes/web.php` dimiliki **Backend 3 (koordinator)**; tiap backend hanya mengisi **blok miliknya** (`// === BACKEND 1 ===` dst.). `bootstrap/app.php` dimiliki **Backend 1** (alias `role`); Backend 3 mengajukan registrasi route API/channel.
7. **Stack tetap:** Laravel 13 (PHP ^8.3), Blade, MySQL. Tampilan **Tailwind CSS via Play CDN** di `layouts/app.blade.php`. **Dilarang:** Node.js, npm, Vite, framework backend JavaScript, dan berkas yang butuh proses build. JavaScript (mis. polling chat) = `<script>` biasa di Blade.
8. Branch terpisah per peran/tugas (mis. `feature/backend-auth`, `feature/frontend-home`); jangan commit `.env`, token, password nyata, upload pengguna; jangan `migrate:fresh` pada data penting.

---

## 5. Backend 1 — Fondasi dan Autentikasi

**Identitas:** Backend 1 — _isi nama_ · **Database & Authentication** · **Tahap 1, 2, 3**.

### 5.1 Tugas utama
- **Tahap 1:** migration dan struktur seluruh tabel; factory dan `DatabaseSeeder` (kategori contoh, akun demo admin/pelanggan/penyedia, listing, percakapan selesai, pesan, ulasan).
- **Tahap 2:** model dan relasi Eloquent untuk seluruh tabel domain.
- **Tahap 3:** registrasi, login, logout (session, regenerasi session); pendaftaran publik hanya `customer`/`provider` (admin ditolak); throttle daftar `10,1`, login `5,1`; `RoleMiddleware` (alias `role`, **403** jika tidak cocok); hak akses per role.
- Membantu menyepakati struktur data dan **kontrak integrasi** dengan anggota lain (dokumentasikan kolom/relasi di PR).
- **[PENETAPAN]** Profil (ubah data, avatar, kota, bio, ganti password, profil publik), data dashboard per role, dan kategori admin (tambah/hapus; hapus meng-*cascade* listing) — karena bergantung pada `users`/`categories`.

### 5.2 File Backend (PHP)

| File | Fungsi | Tahap |
|---|---|---|
| `database/migrations/` (16 file, tabel 5.4) | Skema seluruh tabel | 1 |
| `database/factories/UserFactory.php`†, `CategoryFactory.php`†, `ServiceRequestFactory.php`†, `ServiceOfferFactory.php`†, `ConversationFactory.php`†, `MessageFactory.php`†, `ReviewFactory.php`† | Data uji | 1 |
| `database/seeders/DatabaseSeeder.php` | Data demo | 1 |
| `app/Models/User.php`, `Category.php`, `ServiceRequest.php`, `ServiceOffer.php`, `Conversation.php`, `Message.php`, `Review.php` | Model + relasi (lalu kepemilikan berpindah, aturan 3) | 2 |
| `app/Http/Controllers/AuthController.php` | Register, login, logout | 3 |
| `app/Http/Middleware/RoleMiddleware.php` | Pemeriksa role | 3 |
| `bootstrap/app.php` | Registrasi alias middleware `role` | 3 |
| `app/Http/Controllers/ProfileController.php` | Profil publik, edit profil, avatar, password **[PENETAPAN]** | 3 |
| `app/Http/Controllers/DashboardController.php` | Data dashboard per role (admin: statistik pengguna, kebutuhan, penawaran, kategori) **[PENETAPAN]** | 3 |
| `app/Http/Controllers/Admin/CategoryController.php` | Lihat/tambah/hapus kategori **[PENETAPAN]** | 3 |
| `routes/web.php` — **blok Backend 1** | `/daftar`, `/masuk`, `POST /keluar`, `/dashboard`, `/profil` (GET, PUT), `/profil/password` (PUT), `/pengguna/{user}`, `/admin/kategori` (GET, POST, DELETE; `auth` + `role:admin`) | 3 |
| `tests/Feature/AuthTest.php`, `ProfileTest.php`, `AdminTest.php` | Test | 3 |

### 5.3 File Frontend (Blade)
**Tidak ada.** View yang dilayani Backend 1 dikerjakan **Frontend 2**: `auth/login`, `auth/register`, `dashboard`, `profile/edit`, `profile/show`, `admin/categories/index` (kontrak data: Bagian 12).

### 5.4 Migration, tabel, dan model

| Migration | Tabel / perubahan | Model |
|---|---|---|
| `0001_01_01_000000_create_users_table.php` | `users` (email unik), `password_reset_tokens`, `sessions` | `User` |
| `0001_01_01_000001_create_cache_table.php` | `cache`, `cache_locks` | — |
| `0001_01_01_000002_create_jobs_table.php` | `jobs`, `job_batches`, `failed_jobs` | — |
| `2026_01_01_000001_add_profile_fields_to_users_table.php` | `role` (default customer), `phone`, `bio` | — |
| `2026_01_01_000002_create_categories_table.php` | `categories` (slug unik) — **harus sebelum** listing | `Category` |
| `2026_01_01_000003_create_service_requests_table.php` | `service_requests` | `ServiceRequest` |
| `2026_01_01_000004_create_service_offers_table.php` | `service_offers` | `ServiceOffer` |
| `2026_01_01_000005_create_conversations_table.php` | `conversations` | `Conversation` |
| `2026_01_01_000006_create_messages_table.php` | `messages` | `Message` |
| `2026_02_01_000001_add_city_and_avatar_to_users_table.php` | `city`, `avatar` | — |
| `2026_02_01_000002_add_image_to_listings.php` | `image` pada `service_requests` dan `service_offers` | — |
| `2026_02_01_000003_add_completed_at_to_conversations_table.php` | `completed_at` | — |
| `2026_02_01_000004_create_reviews_table.php` | `reviews`; unik `(conversation_id, reviewer_id)` | `Review` |
| `2026_02_01_000005_create_notifications_table.php` | `notifications` (UUID, morph `notifiable`) | — |
| `2026_02_01_000006_add_performance_indexes.php` | Index status+`created_at` listing; `conversation_id`+`id` pada `messages` | — |
| `2026_10_29_035444_create_personal_access_tokens_table.php` | Token Sanctum (morph `tokenable`) | — |

**Relasi:** `users` 1–N `service_requests`, `service_offers`; `categories` 1–N keduanya (FK cascade); `conversations` N–1 `users` (`customer_id`, `provider_id`); `conversations.service_request_id` / `service_offer_id` nullable (`nullOnDelete`; **tanpa constraint XOR**); `messages` N–1 `conversations`; `reviews` ke percakapan, reviewer, reviewee. Skema lengkap: `PROJECT_PRD.md` Bagian 8.

### 5.5 Batas tanggung jawab

| Boleh diubah | Tidak boleh diubah sembarangan / koordinasikan |
|---|---|
| File pada 5.2 dan 5.4 | Setelah Tahap 2: `ServiceRequest`/`ServiceOffer` (milik Backend 2) dan `Conversation`/`Message`/`Review` (milik Backend 3) |
| | Kolom `users` / perilaku role → beri tahu **semua** (berdampak lintas modul) |
| | Semua `.blade.php` → milik Frontend |
| | Controller listing/chat → milik Backend 2/3 |

### 5.6 Hubungan dengan anggota lain
- **Backend 2/3:** memberi model, relasi, role middleware; menerima permintaan perubahan skema (migration baru).
- **Frontend 2:** memberi route + data untuk auth/profil/dashboard/kategori.
- **Backend 3:** menerima koordinasi `routes/web.php`; mengajukan registrasi route via `bootstrap/app.php`.

### 5.7 Urutan pengerjaan
1. Migration (urut timestamp; `categories` sebelum listing) → jalankan dari DB kosong.
2. Factory + `DatabaseSeeder`.
3. Tujuh model + relasi → merge (kepemilikan model berpindah).
4. `RoleMiddleware` + alias; `AuthController` + route; `AuthTest`.
5. `ProfileController`, `DashboardController`, `Admin\CategoryController` + test.

---

## 6. Backend 2 — Fitur Marketplace

**Identitas:** Backend 2 — _isi nama_ · **Core Features** · **Tahap 4 dan 5** · Mulai setelah **Tahap 3 di-merge**.

### 6.1 Tugas utama
- **Tahap 4 — Butuh Jasa:** CRUD permintaan/kebutuhan, validasi, status (`open`, `in_progress`, `closed`), otorisasi (ubah: pemilik; hapus: pemilik atau admin). Daftar publik hanya `open`; pencarian kata kunci, filter kategori, pagination; deadline opsional; gambar.
- **Tahap 5 — Menyediakan Jasa:** CRUD penawaran, validasi, status (`active`, `inactive`), otorisasi (sama). Daftar publik hanya `active`; rating agregat dari `reviews` (rating final setelah Backend 3 menyelesaikan ulasan).
- Menyiapkan controller dan **data yang dibutuhkan halaman frontend**.
- **[PENETAPAN]** Beranda (hingga 3 kebutuhan `open` + 3 penawaran `active` terbaru) dan trait unggah gambar `HandlesImageUploads`.

### 6.2 File Backend (PHP)

| File | Fungsi |
|---|---|
| `app/Http/Controllers/ServiceRequestController.php` | CRUD kebutuhan |
| `app/Http/Controllers/ServiceOfferController.php` | CRUD penawaran |
| `app/Http/Requests/ServiceRequestRequest.php`, `ServiceOfferRequest.php` | Validasi (dipakai web **dan API**) |
| `app/Policies/ServiceRequestPolicy.php`, `ServiceOfferPolicy.php` | Pemilik/admin |
| `app/Models/ServiceRequest.php`, `ServiceOffer.php` | Hak milik setelah Tahap 2: filter kata kunci/kategori, label status, URL gambar, query rating |
| `HandlesImageUploads.php` (trait; saran `app/Http/Controllers/Concerns/`†) | Simpan/ganti/hapus gambar di disk `public` **[SEPAKATI lokasi]** |
| `app/Http/Controllers/HomeController.php` | Beranda **[PENETAPAN]** |
| `routes/web.php` — **blok Backend 2** | `GET /`; `GET /butuh-jasa`, `/{serviceRequest}`; `GET, POST /butuh-jasa/buat`; `GET, PUT /butuh-jasa/{serviceRequest}/ubah` (`role:customer`); `DELETE /butuh-jasa/{serviceRequest}` (`role:customer,admin`); `GET /menyediakan-jasa`, `/{serviceOffer}`; `GET, POST /menyediakan-jasa/buat`; `GET, PUT …/ubah` (`role:provider`); `DELETE …` (`role:provider,admin`) |
| `tests/Feature/ListingTest.php`, `BantuinTest.php` | Test listing |

### 6.3 File Frontend (Blade)
**Tidak ada.** View dikerjakan Frontend 1 (`home`, `requests/index|show`, `offers/index|show`) dan Frontend 2 (`requests/create|edit|_form`, `offers/create|edit|_form`).

### 6.4 Migration, tabel, dan model
Migration dibuat Backend 1. Tabel tanggung jawab: **`service_requests`** (pemilik FK `users` cascade, kategori FK `categories` cascade, judul, deskripsi, anggaran, lokasi, deadline, `image`, status) dan **`service_offers`** (pemilik, kategori, judul, deskripsi, harga, lokasi, `image`, status). Model: `ServiceRequest`, `ServiceOffer`. Nama kolom pemilik/kategori mengikuti migration yang ada (saran `user_id`†, `category_id`†). Relasi: `conversations.service_request_id` / `service_offer_id` (`nullOnDelete`).

### 6.5 Batas tanggung jawab

| Boleh diubah | Tidak boleh diubah sembarangan / koordinasikan |
|---|---|
| File pada 6.2 | Skema/migration → minta Backend 1 (migration baru) |
| | `ServiceRequestRequest`/`ServiceOfferRequest`: perubahan field → beri tahu Backend 3 (API), Frontend 2 (form), Backend 1 (factory/seeder) |
| | Status `closed` kebutuhan **diubah oleh aksi selesai Backend 3** lewat Eloquent; Backend 3 tidak mengedit model ini — sediakan metode bila diminta |
| | Aksi selesai **tidak mengubah status penawaran** |
| | `.blade.php` → Frontend; `RoleMiddleware`/Auth → Backend 1 |

### 6.6 Hubungan dengan anggota lain
- **Backend 1:** menerima model, middleware, role.
- **Backend 3:** memberi tabel/model listing (sumber percakapan); menerima rating dari `reviews`.
- **Frontend 1/2:** memberi route name dan data view (kontrak Bagian 12).

### 6.7 Urutan pengerjaan
1. Tunggu Tahap 3 di-merge. 2. `HandlesImageUploads`. 3. Tahap 4: request + policy + controller + route Butuh Jasa + test. 4. Tahap 5: request + policy + controller + route Menyediakan Jasa + test. 5. `HomeController`. 6. Sambungkan rating ke `reviews` setelah Backend 3 selesai. 7. Serahkan route name/data ke Frontend.

---

## 7. Backend 3 — Chat dan Integrasi

**Identitas:** Backend 3 — _isi nama_ · **Chat & Integration** · **Tahap 6 dan 7** · Mulai setelah ada listing (Tahap 4/5).

### 7.1 Tugas utama
- **Tahap 6 — Chat:** mulai percakapan (penyedia dari kebutuhan `open`; pelanggan dari penawaran `active`; `firstOrCreate`, tanpa percakapan ganda; admin tidak boleh memulai chat); kirim pesan (throttle `30,1`); polling `?after={id}`; pemeriksaan hak akses (hanya peserta).
- Bagian chat yang melekat: notifikasi database `NewMessage`; aksi **selesai** oleh pelanggan (`completed_at`, kebutuhan → `closed`); **ulasan** satu kali oleh pelanggan (rating 1–5, setelah selesai); event `MessageSent` + channel privat `conversation.{id}`.
- **Tahap 7 — Integrasi:** menyelaraskan route, controller, middleware, dan alur antarfitur; **pengujian integrasi dan penyempurnaan bersama** seluruh anggota.
- **[PENETAPAN]** REST API Sanctum (login token, `me`, logout, browse/create listing, percakapan dan pesan) karena memakai service chat; QA akhir dan pembaruan dokumentasi.

### 7.2 File Backend (PHP)

| File | Fungsi |
|---|---|
| `app/Http/Controllers/ConversationController.php` | Inbox, mulai/buka chat, polling, kirim, selesai |
| `app/Http/Controllers/ReviewController.php` | Ulasan pelanggan |
| `app/Http/Controllers/NotificationController.php` | Daftar (maks. 30), baca satu/semua |
| `app/Models/Conversation.php`, `Message.php`, `Review.php` | Hak milik setelah Tahap 2 (peserta, lawan bicara, `completed_at`, dll.) |
| `app/Services/ConversationStarter.php` | Cek role, buat/ambil percakapan (web **dan API**) |
| `app/Services/MessageSender.php` | Simpan pesan, sentuh conversation, notifikasi, coba broadcast (web **dan API**) |
| `app/Events/MessageSent.php`, `app/Notifications/NewMessage.php` | Event dan notifikasi |
| `routes/channels.php` | Otorisasi channel privat |
| `routes/web.php` — **koordinator + blok Backend 3** | `GET, POST /chat`, `GET /chat/{conversation}`, `GET, POST /chat/{conversation}/pesan`, `POST …/selesai`, `POST …/ulasan`, `GET /notifikasi`, `GET /notifikasi/{id}`, `POST /notifikasi/baca-semua` |
| `routes/api.php` | Route API (`/api/login`, listing, `me`, `logout`, `conversations`, `messages`) **[PENETAPAN]** |
| `app/Http/Controllers/Api/AuthController.php`, `Api/ServiceRequestController.php`, `Api/ServiceOfferController.php`, `Api/ConversationController.php` | API JSON **[PENETAPAN]** |
| `app/Http/Resources/ServiceRequestResource.php`, `ServiceOfferResource.php`, `ConversationResource.php`†, `MessageResource.php`† | Bentuk JSON |
| `composer.json`, `.env.example` | Hanya lewat PR koordinasi |
| `tests/Feature/ChatTest.php`†, `ReviewTest.php`†, `ApiTest.php` | Test **[SEPAKATI nama chat/ulasan]** |

### 7.3 File Frontend (Blade)
**Tidak ada.** Halaman chat, notifikasi, dan form ulasan dikerjakan **Frontend 2**; API tidak punya Blade (JSON).

### 7.4 Migration, tabel, dan model
Migration dibuat Backend 1. Tabel tanggung jawab: **`conversations`** (`customer_id`, `provider_id`, `service_request_id`/`service_offer_id` nullable, `completed_at`), **`messages`** (pengirim, body, `read_at`), **`reviews`** (percakapan, reviewer, reviewee, rating 1–5, komentar; unik `(conversation_id, reviewer_id)`), **`notifications`**, **`personal_access_tokens`**. Model: `Conversation`, `Message`, `Review`. Catatan: tidak ada constraint XOR antar kolom listing; dijaga `ConversationStarter`.

### 7.5 Batas tanggung jawab

| Boleh diubah | Tidak boleh diubah sembarangan / koordinasikan |
|---|---|
| File pada 7.2 | Kontrak `ConversationStarter`/`MessageSender` harus tetap kompatibel web + API |
| | Mengubah status kebutuhan menjadi `closed`: lewat Eloquent **tanpa mengedit `ServiceRequest.php`** (Backend 2) |
| | Query rating → Backend 2; `users` → Backend 1 |
| | Konfigurasi Reverb **[SEPAKATI]**: paket `laravel/reverb` belum ada di `composer.json`; tanpa npm tidak ada klien Echo — polling tetap jalur default |
| | Logika domain Backend 1/2 tidak diubah saat integrasi tanpa pemiliknya |

### 7.6 Hubungan dengan anggota lain
- **Backend 2:** menerima model listing; memberi `reviews` (rating).
- **Backend 1:** menerima model; menerima role middleware.
- **Frontend 2:** memberi route/data chat dan kontrak JSON polling; **Frontend 1:** menerima tombol mulai chat yang memanggil `chat.store`.
- **Semua:** koordinator `routes/web.php` dan pengujian integrasi Tahap 7.

### 7.7 Urutan pengerjaan
1. Tunggu listing (Tahap 4/5). 2. `ConversationStarter` + `MessageSender`. 3. `ConversationController` + route chat + polling JSON. 4. Notifikasi (`NewMessage`, `NotificationController`). 5. Selesai + `ReviewController`. 6. `MessageSent` + `routes/channels.php`. 7. **Tahap 7:** rapikan `routes/web.php`, middleware, alur antarfitur; API + `ApiTest`; jalankan seluruh `php artisan test`; catat kegagalan lama (mis. `AdminTest::test_non_admin_cannot_open_admin_pages` gagal pada analisis awal); perbarui dokumen.

---

## 8. Frontend 1 — Beranda dan Marketplace

**Identitas:** Frontend 1 — _isi nama_ · **UI Marketplace** · Semua pekerjaan memakai **Blade + Tailwind Play CDN**.

### 8.1 Tugas utama
- Beranda publik / landing page.
- Daftar Butuh Jasa dan daftar Menyediakan Jasa (kartu, pencarian, filter kategori, pagination).
- Detail permintaan dan detail penawaran jasa (aksi ubah/hapus untuk pemilik; tombol mulai chat sesuai role/status).
- Navbar (menu per role; link **Kategori** hanya admin; Keluar), footer, dan komponen kartu jasa.
- Tampilan **responsif** untuk desktop dan perangkat mobile.
- **[PENETAPAN]** Halaman error kustom 403, 404, 419, 429, 500 dan partial rating.

### 8.2 File Frontend (Blade)

| File | Fungsi |
|---|---|
| `resources/views/layouts/app.blade.php` | Layout utama; **tag Tailwind Play CDN**; navbar; footer; flash message |
| `resources/views/partials/navbar.blade.php`†, `partials/footer.blade.php`† | Navbar dan footer (dipisah bila layout terlalu panjang) |
| `resources/views/home.blade.php`† | Beranda/landing |
| `resources/views/requests/index.blade.php`† | Daftar Butuh Jasa + pencarian/filter + pagination |
| `resources/views/requests/show.blade.php`† | Detail permintaan; tombol mulai chat untuk penyedia bila `open` |
| `resources/views/offers/index.blade.php`† | Daftar Menyediakan Jasa |
| `resources/views/offers/show.blade.php`† | Detail penawaran; profil penyedia, rating; tombol mulai chat untuk pelanggan bila `active` |
| `resources/views/partials/request-card.blade.php`†, `partials/offer-card.blade.php`† | Komponen kartu |
| `resources/views/partials/request-filter.blade.php`†, `partials/offer-filter.blade.php`† | Pencarian dan filter kategori |
| `resources/views/partials/rating.blade.php`† | Rating/bintang (dipakai juga Frontend 2) |
| `resources/views/errors/403.blade.php`†, `404`†, `419`†, `429`†, `500`† (`.blade.php`) | Halaman error |

### 8.3 File Backend dan migration
**Tidak ada.** Data dari Backend 2 (listing, beranda) dan Backend 1 (role untuk navbar). Tabel/model tanggung jawab: tidak ada (hanya menampilkan).

### 8.4 Batas tanggung jawab

| Boleh diubah | Tidak boleh diubah sembarangan / koordinasikan |
|---|---|
| File pada 8.2 | Semua file PHP (controller, model, route) → backend; minta perubahan data lewat PR/issue |
| | `layouts/app.blade.php`: perubahan oleh Frontend 2 hanya lewat PR yang ditinjau Frontend 1 |
| | `requests/create|edit|_form`, `offers/create|edit|_form` → Frontend 2 |
| | Dilarang `@vite`, npm, atau berkas yang butuh build |

### 8.5 Hubungan dan dependensi
- **Frontend 2:** memberi layout, kartu, rating; menerima `partials/form-field`†, `partials/avatar`†.
- **Backend 2:** menerima data view listing/beranda (kontrak Bagian 12). **Backend 3:** tombol mulai chat memanggil route `chat.store`.
- **Urutan:** (1) layout + navbar + footer → merge lebih dulu (dipakai semua); (2) komponen kartu/filter/rating; (3) beranda, daftar, detail dengan data contoh; (4) sambungkan data nyata setelah Backend 2 merge; (5) responsif mobile; (6) halaman error.

---

## 9. Frontend 2 — Akun dan Interaksi

**Identitas:** Frontend 2 — _isi nama_ · **UI Account & Interaction** · **Blade + Tailwind Play CDN**.

### 9.1 Tugas utama
- Login dan registrasi.
- Dashboard pengguna (isi berbeda per role: pelanggan, penyedia, admin).
- Form pembuatan (dan ubah) Butuh Jasa; form pembuatan (dan ubah) Menyediakan Jasa.
- Halaman chat (inbox, percakapan, kirim pesan, polling, tombol selesai, form ulasan).
- Halaman profil dan edit profil pengguna (profil publik).
- **[PENETAPAN]** Komponen field form dan avatar; halaman notifikasi; halaman admin kategori.

### 9.2 File Frontend (Blade)

| File | Fungsi |
|---|---|
| `resources/views/partials/form-field.blade.php`† | Komponen field form (error validasi, nilai lama) — **dipakai semua form** |
| `resources/views/partials/avatar.blade.php`† | Avatar |
| `resources/views/auth/login.blade.php`†, `auth/register.blade.php`† | Login (`/masuk`), registrasi (`/daftar`: nama, email, password, role, HP opsional) |
| `resources/views/dashboard.blade.php` | Dashboard; bagian per role boleh dipisah: `partials/dashboard/customer.blade.php`†, `provider.blade.php`†, `admin.blade.php`† |
| `resources/views/requests/create.blade.php`†, `edit.blade.php`†, `_form.blade.php`† | Form Butuh Jasa (kategori, judul, deskripsi, anggaran, lokasi, deadline opsional, gambar; status saat ubah) |
| `resources/views/offers/create.blade.php`†, `edit.blade.php`†, `_form.blade.php`† | Form Menyediakan Jasa (kategori, judul, deskripsi, harga, lokasi, gambar; status saat ubah) |
| `resources/views/chat/index.blade.php`† | Inbox |
| `resources/views/chat/show.blade.php`† | Pesan, form kirim, tombol selesai (pelanggan), **skrip polling 3 detik (`<script>` biasa)** |
| `resources/views/chat/_review-form.blade.php`† | Form ulasan (setelah selesai, hanya pelanggan) |
| `resources/views/profile/edit.blade.php`†, `profile/show.blade.php`† | Edit profil/avatar/password; profil publik (penawaran aktif + ulasan) |
| `resources/views/notifications/index.blade.php`† | Notifikasi **[PENETAPAN]** |
| `resources/views/admin/categories/index.blade.php`† | Daftar kategori + form tambah + hapus **[PENETAPAN]** |

### 9.3 File Backend dan migration
**Tidak ada.** Data dari Backend 1 (auth, dashboard, profil, kategori), Backend 2 (form listing), Backend 3 (chat, notifikasi).

### 9.4 Batas tanggung jawab

| Boleh diubah | Tidak boleh diubah sembarangan / koordinasikan |
|---|---|
| File pada 9.2 | Semua file PHP → backend |
| | `layouts/app.blade.php`, `home`, `requests/index|show`, `offers/index|show`, kartu → Frontend 1 |
| | Format JSON polling chat dan nama field form → sepakati dengan Backend 3 / Backend 2 (**[SEPAKATI]**) |
| | Form wajib `@csrf`; ubah `PUT`, hapus `DELETE` via `@method` sesuai route |

### 9.5 Hubungan dan dependensi
- **Frontend 1:** menerima layout dan partial kartu/rating; memberi `form-field`†, `avatar`†.
- **Backend 1:** auth, dashboard, profil, kategori. **Backend 2:** validasi dan data form listing. **Backend 3:** endpoint chat, notifikasi, ulasan.
- **Urutan:** (1) `form-field`, `avatar` (dipakai Frontend 1 juga); (2) login/registrasi (Tahap 3); (3) dashboard + profil; (4) form Butuh Jasa dan Menyediakan Jasa (Tahap 4–5); (5) halaman chat + ulasan + notifikasi (Tahap 6); (6) admin kategori. Bangun dengan data contoh lebih dulu, sambungkan setelah controller di-merge.

---

## 10. Peta Kepemilikan Folder (Cari Cepat)

| Folder / file | Pemilik |
|---|---|
| `database/migrations/`, `factories/`, `seeders/` | Backend 1 |
| `app/Models/User.php`, `Category.php` | Backend 1 |
| `app/Models/ServiceRequest.php`, `ServiceOffer.php` | Backend 2 (setelah Tahap 2) |
| `app/Models/Conversation.php`, `Message.php`, `Review.php` | Backend 3 (setelah Tahap 2) |
| `AuthController`, `ProfileController`, `DashboardController`, `Admin/CategoryController`, `Middleware/RoleMiddleware`, `bootstrap/app.php` | Backend 1 |
| `ServiceRequestController`, `ServiceOfferController`, `HomeController`, `Requests/*`, `Policies/*`, trait `HandlesImageUploads` | Backend 2 |
| `ConversationController`, `ReviewController`, `NotificationController`, `app/Services/`, `app/Events/`, `app/Notifications/`, `routes/channels.php` | Backend 3 |
| `app/Http/Controllers/Api/`, `app/Http/Resources/`, `routes/api.php`, `routes/web.php`, `composer.json`, `.env.example` | Backend 3 |
| `resources/views/layouts/`, `home`, `requests/index|show`, `offers/index|show`, `errors/`, `partials/` kartu/filter/rating/navbar/footer | Frontend 1 |
| `resources/views/auth/`, `dashboard`, `requests/create|edit|_form`, `offers/create|edit|_form`, `chat/`, `profile/`, `notifications/`, `admin/`, `partials/form-field|avatar|dashboard/` | Frontend 2 |
| `tests/Feature/AuthTest, ProfileTest, AdminTest` | Backend 1 |
| `tests/Feature/ListingTest, BantuinTest` | Backend 2 |
| `tests/Feature/ChatTest†, ReviewTest†, ApiTest` | Backend 3 |

---

## 11. Alur Kerja Backend ↔ Frontend

1. Backend menetapkan **nama route** dan **data yang dikirim ke view** (Bagian 12) dan menuliskannya di deskripsi PR.
2. Frontend membangun Blade dengan data contoh, lalu menyambungkan ke data nyata setelah controller di-merge.
3. Permintaan perubahan lintas batas (mis. "butuh variabel baru di view") = **issue/PR ke pemilik file**, bukan mengedit file orang lain.
4. Saat Tahap 7, Backend 3 memimpin pengujian integrasi: seluruh alur (daftar → listing → chat → selesai → ulasan) dijalankan bersama.

---

## 12. Kontrak Data Backend–Frontend (Saran, Disepakati per Tahap)

> Nama variabel view **tidak disebut dokumentasi**; kolom "Data" adalah **saran†** untuk disepakati backend dan frontend sebelum menyambungkan.

| Halaman | View | Route (nama) | Backend | Frontend | Data yang dibutuhkan view† |
|---|---|---|---|---|---|
| Beranda | `home` | `home` | Backend 2 | Frontend 1 | 3 kebutuhan `open` terbaru, 3 penawaran `active` terbaru |
| Daftar Butuh Jasa | `requests/index` | `requests.index` | Backend 2 | Frontend 1 | daftar paginasi (status `open`), daftar kategori, nilai filter `q`/`category` |
| Detail Butuh Jasa | `requests/show` | `requests.show` | Backend 2 | Frontend 1 | satu kebutuhan + pemilik + kategori; izin ubah/hapus; status |
| Form Butuh Jasa | `requests/create`, `edit` | `requests.create/store/edit/update` | Backend 2 | Frontend 2 | daftar kategori; kebutuhan (saat ubah); error validasi |
| Daftar/Detail Menyediakan Jasa | `offers/index`, `show` | `offers.index/show` | Backend 2 | Frontend 1 | daftar paginasi (`active`), kategori; detail + penyedia + rating |
| Form Menyediakan Jasa | `offers/create`, `edit` | `offers.create/store/edit/update` | Backend 2 | Frontend 2 | daftar kategori; penawaran (saat ubah); error validasi |
| Login/Registrasi | `auth/login`, `register` | `login`, `register` | Backend 1 | Frontend 2 | error validasi; pilihan role (customer/provider) |
| Dashboard | `dashboard` | `dashboard` | Backend 1 | Frontend 2 | pelanggan: kebutuhan sendiri; penyedia: penawaran sendiri + rating; admin: jumlah pengguna/kebutuhan/penawaran/kategori |
| Profil | `profile/edit`, `show` | `profile.edit/update/password/show` | Backend 1 | Frontend 2 | pengguna; (publik) penawaran aktif + ulasan diterima |
| Chat | `chat/index`, `show` | `chat.index/show/messages/send/complete/review` | Backend 3 | Frontend 2 | daftar percakapan; percakapan + pesan + lawan bicara + status selesai; JSON polling **[SEPAKATI format]** |
| Notifikasi | `notifications/index` | `notifications.index/read/readAll` | Backend 3 | Frontend 2 | maks. 30 notifikasi terakhir |
| Kategori admin | `admin/categories/index` | `admin.categories.*` | Backend 1 | Frontend 2 | kategori + jumlah kebutuhan/penawaran terkait |

---

## 13. Aturan Akses Ringkas (Detail di PRD)

| Fitur | Pelanggan | Penyedia | Admin |
|---|---|---|---|
| Buat/ubah kebutuhan | Ya (pemilik) | – | – |
| Hapus kebutuhan | Ya (pemilik) | – | Ya |
| Buat/ubah penawaran | – | Ya (pemilik) | – |
| Hapus penawaran | – | Ya (pemilik) | Ya |
| Mulai chat | dari penawaran `active` | dari kebutuhan `open` | Tidak |
| Selesai + ulasan | Ya | Tidak | – |
| Statistik + kategori | – | – | Ya |

Middleware menentukan jenis akun (**403** jika salah); policy menentukan kepemilikan listing. Detail: `PROJECT_PRD.md` Bagian 10.

---

## 14. Hal yang Perlu Disepakati Tim ([SEPAKATI])

1. Nama anggota untuk tiap peran.
2. Nama berkas bertanda † (cocokkan dengan repository).
3. **[PENETAPAN] di dokumen ini** karena tidak ada di instruksi terbaru: profil, dashboard, kategori admin (Backend 1); beranda dan trait gambar (Backend 2); notifikasi/selesai/ulasan dan REST API (Backend 3); halaman error dan rating (Frontend 1); notifikasi dan admin kategori (Frontend 2). Setujui atau ubah.
4. **Beban Backend 1 paling besar** (seluruh migration, model, auth, profil, dashboard, kategori); tim dapat memindahkan sebagian (mis. profil/dashboard ke Backend 2 saat Tahap 4 belum padat).
5. Lokasi trait `HandlesImageUploads` (saran `app/Http/Controllers/Concerns/`†).
6. Nama test chat/ulasan (saran `ChatTest.php`†, `ReviewTest.php`†).
7. Format JSON polling pesan dan nama variabel view (Bagian 12).
8. Kapan status kebutuhan `in_progress` dipakai (tidak dijelaskan dokumentasi).
9. Entitas "Pesanan" terpisah: **tidak ada** di dokumentasi; jangan membuat tabel `orders` sebelum disepakati.
10. Nasib `package.json` (tidak dipakai tanpa Node.js); Play CDN pada produksi; verifikasi alamat CDN ke dokumentasi resmi Tailwind.
11. Konfigurasi realtime (Reverb): diaktifkan atau tetap polling.

---

## 15. Pemetaan dari Versi Sebelumnya (A–E → Peran Baru)

| Versi lama | Menjadi |
|---|---|
| A — akun, role, profil, dashboard | Backend 1 (PHP) + Frontend 2 (Blade) |
| A — layout, error | Frontend 1 |
| B — Butuh Jasa | Backend 2 (PHP) + Frontend 1 (daftar, detail) + Frontend 2 (form) |
| C — Menyediakan Jasa | Backend 2 (PHP) + Frontend 1 (daftar, detail) + Frontend 2 (form) |
| D — chat, notifikasi, ulasan | Backend 3 (PHP) + Frontend 2 (Blade) |
| E — kategori admin, API, integrasi, QA | Kategori: Backend 1 + Frontend 2; API/integrasi/QA: Backend 3; beranda: Backend 2 + Frontend 1 |
| Tahap lama 0–7 | Persiapan + Tahap 1–7 baru (Bagian 3); selesai/ulasan/notifikasi masuk Tahap 6; API/QA masuk Tahap 7 |

---

## 16. Instruksi ke AI (Templat)

```text
Saya [Backend 1/2/3 atau Frontend 1/2] project Laravel 13 "Bantuin" (PHP + Blade + MySQL, Tailwind via Play CDN,
TANPA Node.js/npm/Vite dan TANPA framework backend JavaScript).
Baca TEAM_TASK_ASSIGNMENT.md (bagian peran saya) dan PROJECT_PRD.md.
Tugas saya sekarang: [mis. Tahap 4 — backend Butuh Jasa].
Aturan:
1. Kerjakan HANYA file pada tabel peran saya. Backend tidak mengedit .blade.php; Frontend tidak mengedit PHP.
2. Jangan ubah file milik peran lain; tulis permintaan perubahan (file, perubahan, alasan) untuk diajukan lewat PR.
3. Untuk file bersama (routes/web.php, bootstrap/app.php, layouts/app.blade.php) isi hanya bagian milik saya.
4. Nama bertanda † adalah saran; cek dulu apakah berkas serupa sudah ada.
5. Frontend: pakai class Tailwind, data contoh dulu sesuai kontrak Bagian 12. Backend: tulis route name dan data view di PR.
6. Jika informasi tidak ada di dokumen, tandai [SEPAKATI] dan tanyakan; jangan mengarang struktur.
7. Sebutkan pekerjaan peran lain yang harus selesai lebih dulu.
```

---

## 17. Definition of Done

- [ ] Migration berjalan dari database kosong tanpa error FK (Backend 1).
- [ ] Route di blok milik sendiri; middleware (`auth`, `role`, `throttle`) dan policy sesuai Bagian 13.
- [ ] Setiap Blade memakai layout dan tampil dengan Tailwind tanpa build; tidak ada `@vite`.
- [ ] Form memakai `@csrf` dan method spoofing yang benar; error validasi tampil.
- [ ] Test modul lulus; Backend 3 menjalankan seluruh `php artisan test` setelah merge.
- [ ] Tidak ada file milik peran lain diubah tanpa PR koordinasi; `.env`, token, dan upload tidak ikut ter-commit.
