# tavp-core

The heart of the **TAVP Stack** — **Tailwind + Alpine + Volt + Phalcon**,
with a Laravel-style ergonomic layer on top of Phalcon's C-extension speed.

> "As fast as Phalcon, as pleasant as Laravel."

## What this repo contains

`tavp-core` is the framework foundation every TAVP project is built on:

- **Bootstrap & DI** — `Application`, `Kernel`, service container
- **Routing** — `Route::get/post/resource`, groups, middleware, named routes
- **Controllers** — `BaseController`, `ApiController`, request/response wrappers
- **Volt templating** — inheritance, partials, macros, auto-escaping
- **ORM wrapper** — thin Eloquent-style layer over native Phalcon models
- **Migrations** — Laravel-style `up/down`, rollback, fresh, status
- **Middleware, Validation, Exceptions** — CSRF, auth, throttle, FormRequest
- **Helpers** — `asset()`, `url()`, `route()`, `csrf_token()`, `old()`, `session()`

## Requirements

- PHP 8.1+
- Phalcon 5.x (C-extension)
- Composer
- Node.js 18+ (for frontend assets)

## Install (development)

```bash
composer install
npm install
cp .env.example .env
tavp key:generate
```

## Quick start

```bash
# Start dev server
tavp serve

# Create a model
tavp make:model User

# Run migrations
tavp migrate
```

## Testing

```bash
composer test          # Run PHPUnit tests
composer stan          # Run PHPStan static analysis
composer cs            # Check code style
composer cs-fix        # Auto-fix code style
```

## Project structure

```
tavp-core/
├── app/                # Application code (controllers, models, etc.)
├── config/             # Configuration files
├── database/           # Migrations and seeders
├── public/             # Web root (index.php, assets)
├── resources/          # Views, frontend assets
├── routes/             # Route definitions
├── scripts/            # Utility scripts (phalcon install, etc.)
├── src/                # Framework source code
├── storage/            # Compiled views, logs, cache
├── tests/              # Test files
├── bin/                # CLI entry point (tavp command)
├── composer.json       # PHP dependencies
├── package.json        # Node.js dependencies
├── vite.config.js      # Vite build configuration
├── tailwind.config.js  # Tailwind CSS configuration
├── phpunit.xml         # PHPUnit configuration
├── phpstan.neon        # PHPStan configuration
└── .php-cs-fixer.php   # Code style configuration
```

## Status

Part of **0.1.0 Genesis** (ZeroVer `0.MINOR.PATCH`). API is not yet stable
until `1.0.0`. See `tavp-docs` for the full milestone checklist.

## License

MIT
