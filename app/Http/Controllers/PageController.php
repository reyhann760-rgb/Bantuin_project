<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Controller sementara untuk Frontend 1 (data dummy).
 * Nanti saat backend (Tahap 4 & 5) selesai, ganti method butuh()/sedia()
 * dengan query Eloquent, view-nya tidak perlu diubah banyak.
 */
class PageController extends Controller
{
    private array $kategori = [
        'Kebersihan', 'Desain Grafis', 'Les Privat',
        'Perbaikan', 'Pengantaran', 'Fotografi',
    ];

    private function butuh()
    {
        return collect([
            ['id' => 1, 'judul' => 'Butuh jasa bersih-bersih rumah type 36', 'kategori' => 'Kebersihan', 'deskripsi' => 'Rumah baru selesai renovasi, butuh dibersihkan menyeluruh: lantai, kaca, dan kamar mandi. Peralatan boleh dibawa sendiri.', 'lokasi' => 'Pekanbaru', 'harga' => 350000, 'pengguna' => 'Rina Sari', 'tanggal' => '2026-11-10', 'status' => 'Terbuka'],
            ['id' => 2, 'judul' => 'Butuh desain logo untuk usaha kopi', 'kategori' => 'Desain Grafis', 'deskripsi' => 'Logo minimalis untuk kedai kopi kecil. Revisi maksimal 3 kali. File akhir dalam format PNG dan vektor.', 'lokasi' => 'Online', 'harga' => 200000, 'pengguna' => 'Budi Hartono', 'tanggal' => '2026-11-11', 'status' => 'Terbuka'],
            ['id' => 3, 'judul' => 'Cari guru les Matematika kelas 9', 'kategori' => 'Les Privat', 'deskripsi' => 'Persiapan ujian, 2 kali seminggu, sore hari. Lebih disukai yang sabar dan berpengalaman mengajar SMP.', 'lokasi' => 'Pekanbaru', 'harga' => 100000, 'pengguna' => 'Ibu Dewi', 'tanggal' => '2026-11-12', 'status' => 'Terbuka'],
            ['id' => 4, 'judul' => 'Butuh tukang service AC rumah', 'kategori' => 'Perbaikan', 'deskripsi' => 'AC 1 PK kurang dingin, butuh cek freon dan cuci unit indoor/outdoor.', 'lokasi' => 'Pekanbaru', 'harga' => 150000, 'pengguna' => 'Andi Pratama', 'tanggal' => '2026-11-12', 'status' => 'Terbuka'],
        ])->map(fn ($i) => (object) $i);
    }

    private function sedia()
    {
        return collect([
            ['id' => 1, 'judul' => 'Jasa cuci dan setrika kiloan', 'kategori' => 'Kebersihan', 'deskripsi' => 'Cuci, kering, dan setrika rapi. Bisa antar jemput area kota. Estimasi selesai 2 hari.', 'lokasi' => 'Pekanbaru', 'harga' => 8000, 'pengguna' => 'Laundry Bu Sari', 'tanggal' => '2026-11-08', 'status' => 'Tersedia'],
            ['id' => 2, 'judul' => 'Desain poster dan feed Instagram', 'kategori' => 'Desain Grafis', 'deskripsi' => 'Desain konten promosi untuk UMKM. Pengerjaan 1-2 hari, revisi 2 kali.', 'lokasi' => 'Online', 'harga' => 75000, 'pengguna' => 'Dimas Kreatif', 'tanggal' => '2026-11-09', 'status' => 'Tersedia'],
            ['id' => 3, 'judul' => 'Fotografer acara dan wisuda', 'kategori' => 'Fotografi', 'deskripsi' => 'Dokumentasi acara dengan kamera mirrorless, hasil edit warna, dikirim via Google Drive.', 'lokasi' => 'Riau', 'harga' => 500000, 'pengguna' => 'Fajar Studio', 'tanggal' => '2026-11-10', 'status' => 'Tersedia'],
            ['id' => 4, 'judul' => 'Jasa antar barang dalam kota', 'kategori' => 'Pengantaran', 'deskripsi' => 'Antar paket, dokumen, dan belanjaan dengan motor. Cepat dan aman, tarif sesuai jarak.', 'lokasi' => 'Pekanbaru', 'harga' => 15000, 'pengguna' => 'Rudi Kurir', 'tanggal' => '2026-11-11', 'status' => 'Tersedia'],
        ])->map(fn ($i) => (object) $i);
    }

    private function filter($items, Request $request)
    {
        if ($q = $request->query('q')) {
            $items = $items->filter(fn ($i) => str_contains(strtolower($i->judul . ' ' . $i->deskripsi), strtolower($q)));
        }
        if ($k = $request->query('kategori')) {
            $items = $items->filter(fn ($i) => $i->kategori === $k);
        }
        return $items->values();
    }

    public function home()
    {
        return view('home', [
            'kategori' => $this->kategori,
            'butuhTerbaru' => $this->butuh()->take(3),
            'sediaTerbaru' => $this->sedia()->take(3),
        ]);
    }

    public function butuhIndex(Request $request)
    {
        return view('jasa.index', [
            'type' => 'butuh',
            'judulHalaman' => 'Daftar Butuh Jasa',
            'subjudul' => 'Permintaan jasa dari pengguna yang sedang mencari bantuan.',
            'items' => $this->filter($this->butuh(), $request),
            'kategori' => $this->kategori,
        ]);
    }

    public function butuhShow($id)
    {
        $item = $this->butuh()->firstWhere('id', (int) $id) ?? abort(404);
        return view('jasa.show', ['type' => 'butuh', 'item' => $item, 'lainnya' => $this->butuh()->where('id', '!=', $item->id)->take(3)]);
    }

    public function sediaIndex(Request $request)
    {
        return view('jasa.index', [
            'type' => 'sedia',
            'judulHalaman' => 'Daftar Menyediakan Jasa',
            'subjudul' => 'Penawaran jasa dari penyedia yang siap membantu kamu.',
            'items' => $this->filter($this->sedia(), $request),
            'kategori' => $this->kategori,
        ]);
    }

    public function sediaShow($id)
    {
        $item = $this->sedia()->firstWhere('id', (int) $id) ?? abort(404);
        return view('jasa.show', ['type' => 'sedia', 'item' => $item, 'lainnya' => $this->sedia()->where('id', '!=', $item->id)->take(3)]);
    }
}
