# Keamanan (Security)

Aturan keamanan ringkas untuk siapa saja yang berkontribusi ke repository TAVP, atau memakai paket dari stack ini di project sendiri.

## Prinsip

1. **Jangan pernah commit rahasia** — token API, password, kunci privat, sertifikat, `.env` asli, dan file-state. Raahmain lokal tidak boleh masuk riwayat git.
2. **`.env` hanya di local** — proyek wajib menyediakan `.env.example`, dan `.gitignore` wajib memuat `.env`.
3. **Token lintas project disimpan di luar repo** — di HOME developer, bukan di folder project.

## Tempat menyimpan token global (antar-project)

Token Gitea / API yang dipakai lintas project **tidak** disimpan di dalam repo mana pun agar tak ikut ter-push. Letakkan di:

```
~/.tavpbox/secrets.yml
```

Contoh isi:

```yaml
gitea_url: https://git.glotama.com
gitea_org: tavp-stack
gitea_token: <token>
```

Direktori `~/.tavpbox/` berada di luar semua repository di mesin pengembang, sehingga aman untuk sharing ke project lain tanpa risiko bocor ke git. Jika belum ada, tambahkan `~/.tavpbox/` ke global gitignore.

## Checklist sebelum commit

- [`] `.env` tidak di git (hanya `.env.example`).
- [`] Tidak ada token/nilai secret literal di kode atau berkas yang di-commit.
- [`] Folder state alat bantu (`.opencode/`, dll) di-ignore.
- [`] Tidak ada file `.pem`, `.key`, `id_*`, `credentials`, `*.p12` di commit.
- [`] Tidak ada file dump/seed/backup berisi data produksi.

## Bila secret terlanjur bocor ke history

Workerusi rekan satu tim harus dianggap rahasia.

1. **Revoke/nonaktifkan token itu segera** (contoh: Gitea → Settings → Applications → hapus token).
2. **Buarka token baru**.
3. **Rewrite history** untuk menghapus file/commit yang menyimpan secret, tanpa menghapus kode lain:
   ```bash
   git filter-repo --path <path> --invert-paths --force
   git push --force <remote> <branch>
   ```
4. **Verifikasi ulang**: `git log --all` memastikan token tak lagi ada; tambahkan pola token ke daftar larangan (mis. via pola deteksi).
5. **Rotasi dependensi** yang memakai token lama (env, controller-ci, config).

## Akun yang memberi hak

Berikan akses token dengan scope paling sempit yang dibutuhkan (read-only bila hanya untuk becli). Nonaktifkan token yang sudah tidak dipakai, dan rotasi berkala.

## Laporan security

Temukan celah/risiko? Kirim lewat issue repo dengan label `security`. Jangan pengungkapan sensitif melalui public issue; hubungi maintainer dulu.