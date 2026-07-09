# tavp-core

Scaffold / fondasi dasar dari **TAVP Stack**. Repo ini yang jadi titik awal
setiap project baru: sudah menggabungkan **Tailwind + Alpine + Volt + Phalcon**
jadi satu kesatuan yang rapi.

## Isi

Inti framework TAVP:

- Router
- Controller
- Volt (templating, pengganti Blade)
- ORM (basis Phalcon ORM + wrapper ala Eloquent)
- Migration system (gaya Laravel: up/down, rollback, fresh, status)
- Validation (FormRequest, rule-based)
- Middleware
- Dependency Injection (DI)
- Config & Helpers
- Exception handler

## Kenapa begini

Phalcon itu C-extension, jadi jauh lebih kenceng & irit RAM dari framework
PHP biasa. TAVP nambahin lapisan di atasnya biar pengalaman ngodingnya
seenak Laravel, tapi eksekusinya sekilat Phalcon.

## Status

Planning. Belum ada kode, baru struktur & konvensi.

## Cara pakai (rencana)

```
tavp new nama-project
cd nama-project
tavp migrate
```

Lihat `tavp-cli` untuk command-nya.
