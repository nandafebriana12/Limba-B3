# Add Comprehensive Validation to All Inputs

## Goal
Menambahkan validasi sisi klien (HTML5) dan sisi server (PHP) yang kuat pada semua form di dalam sistem untuk mencegah data kosong, nilai negatif, atau tipe data tidak valid (terutama pada transaksi).

## Tasks
- [x] Task 1: Tambahkan validasi HTML5 (`required`, `min="0.01"`, `maxlength`) pada form Master Data (`admin/sampah`, `admin/upt`, `admin/vendors`, `admin/users`). → Verify: Submit form kosong di browser akan tertahan oleh tooltip bawaan browser.
- [x] Task 2: Perkuat validasi backend PHP untuk Master Data (cek duplikasi kode, batas karakter, validasi tipe data). → Verify: Bypass HTML5 di frontend, backend harus menolak data kosong/salah dengan pesan error.
- [x] Task 3: Tambahkan validasi frontend & backend untuk form **Transaksi** (`admin/transaksi/create_keluar.php` & `upt/transaksi_masuk/create.php`). → Verify: Quantity tidak bisa <= 0, item sampah minimal harus ada 1, dan format tanggal valid.
- [x] Task 4: Periksa dan perkuat validasi form **Login** (`auth/login.php`) serta form Edit. → Verify: Username/password tidak boleh di-bypass dengan spasi kosong saja.

## Done When
- [x] Seluruh `<input>`, `<select>`, `<textarea>` memiliki penanda `required` yang sesuai.
- [x] Input quantity pada form transaksi memiliki `min="0.01"` dan dicek di server `> 0`.
- [x] Jika form transaksi di-submit tanpa item sampah satupun, sistem akan menampilkan pesan error bukannya error SQL.
- [x] Tidak ada lagi "SQL Exception" yang muncul ke pengguna akibat input salah format.

