# Changelog

Semua perubahan penting pada **tavp-core** dicatat di sini.

Format mengikuti [Keep a Changelog](https://keepachangelog.com/en/1.1.0/) dan versi mengikuti ZeroVer `0.MINOR.PATCH` (major tidak pernah lewat 0, sesuai [0ver.org](https://0ver.org)).

## [Unreleased]

### Fixed
- Bersihkan file test yang ternyata dokumen markdown (disimpan sebagai `.php`): `Auth`, `Cache`, `Model`, `Router`, `Validation` ditulis ulang jadi test PHPUnit yang valid (namespace `Tavp\Core\Tests\Unit`).

## [0.1.12] - 07 Aug 2026

### Changed
- Captcha slider diperjelas: area target gambar kini digelapkan + diberi garis putus-putus sehingga terlihat jelas di mana potongan harus ditaruh.
- Potongan (slice) diberi border + bayangan agar menonjol dari latar foto.

## [0.1.11] - 07 Aug 2026

### Fixed
- Foto latar captcha slider diubah ke PNG agar kompatibel dengan runtime yang GD-nya tanpa dukungan JPEG (mis. image container `tavpbox-php`); `loadPhoto()` kini memprioritaskan PNG, dengan jpg/jpeg/webp sebagai cadangan.

## [0.1.10] - 07 Aug 2026

### Added
- Slider captcha memakai foto asli bundel (`resources/assets/captcha/bg_1..5`), tidak lagi pola GD murni. Bila folder foto kosong tetap ada fallback pola.

## [0.1.9] - 04 Aug 2026

### Fixed
- SMTP socket timeout keras 15 detik agar pengiriman OTP tidak macet (cocok untuk PHP-FPM).
- Warna ring captcha fokus disesuaikan ke brand (`#e6c446`).

## [0.1.8] - 04 Aug 2026

### Changed
- Soal captcha math default memakai penjumlahan sederhana (1–10); pengurangan/perkalian opsional lewat config.

## [0.1.7] - 04 Aug 2026

### Fixed
- Verifikasi kode balasan SMTP per langkah + tambah header `Message-ID`/`Date`.

## [0.1.6] - 04 Aug 2026

### Fixed
- Dukungan implicit TLS pada SMTP port 465 + penanganan balasan multi-baris.

## [0.1.5] - 02 Aug 2026

### Added
- Konstruktor `Response` menerima content + statusCode.

## [0.1.4] - 11 Jul 2026

### Added
- Queue worker, schedule runner, migrasi RBAC, `DatabaseQueue`, config `queue`.

## [0.1.3] - 11 Jul 2026

### Added
- Perluasan `CmsContent`: SEO, taxonomy, metadata, serialization.

## [0.1.2] - 10 Jul 2026

### Fixed
- Rendering Volt sungguhan di `ViewFactory` (compile + inheritance), tambah aksesor `Application::config()`.

## [0.1.1] - 10 Jul 2026

### Added
- Lapisan HTTP: `Kernel` memfinalisasi `Response` (redirect/json), helper `response()`/`redirect()`, `Response::getStatusCode()`/`__toString`, SMTP tanpa auth untuk dev mail.

## [0.1.0] - 10 Jul 2026

### Changed
- Core dirusutkan: subsistem opsional/duplikat dipisah ke modul masing-masing (hive, hub, kit, marketplace, ai, deploy, coil, relay, search, broadcasting, api, community, benchmark, security).

## [0.0.1] - 10 Jul 2026

### Fixed
- Koreksi namespace PSR-4 lintas subsistem, tambah classmap + autoload file helper.

## [0.0.0] - 10 Jul 2026

### Added
- README dirombak agar runtut untuk pembaca (manusia & AI): 4 runtime, angka performa yang konsisten, tabel ekosistem.