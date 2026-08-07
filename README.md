# TAVP Stack

> **T**ailwind CSS + **A**lpine.js + **V**olt + **P**halcon = TAVP

TAVP is a curated PHP tech stack for building web applications. It pairs Phalcon's C-extension performance with modern frontend tooling (Tailwind CSS, Alpine.js) and the Volt templating engine.

**Current Version: 0.1.12**

## Features

- **Phalcon 5.16** — C-extension MVC framework
- **Volt Templates** — Template engine compiled to PHP
- **Tailwind CSS** — Utility-first CSS framework
- **Alpine.js** — Lightweight JavaScript framework
- **OTP Authentication** — Passwordless login (email, SMS, WhatsApp)
- **JWT API Auth** — Token-based API authentication
- **Role & Permission** — RBAC system
- **CLI Tools** — Code generation, migrations, deployment
- **Module System** — Composer-based package discovery
- **Async Foundation** — Swoole-based async API layer (`Async`)

## Quick Start

```bash
# Install TAVP
composer create-project tavp/core my-app

# Start development
cd my-app
tavp serve

# Open browser
open http://localhost:8000
```

## Documentation

- [docs.tavp.web.id](https://docs.tavp.web.id/) — Official documentation (Bahasa Indonesia + English)
- [Getting Started](https://docs.tavp.web.id/guide/what-is-tavp)
- [CLI Reference](https://docs.tavp.web.id/reference/cli)
- [FAQ](https://docs.tavp.web.id/reference/faq)

## System Requirements

| PHP 8.3+ | Phalcon 5.16+ (install with `tavp phalcon:install`) | Node.js 18+ (frontend) | Composer 2.x |

## Ecosystem

| Package | Description | Install |
|---------|-------------|---------|
| [tavp/core](https://github.com/tavp-stack/tavp-core) | Framework foundation | `composer create-project tavp/core` |
| [tavp/cli](https://github.com/tavp-stack/tavp-cli) | CLI tool (`tavp` command) | `composer global require tavp/cli` |
| [tavpid](https://github.com/tavp-stack/tavpid) | OTP-first authentication | `composer require tavp/tavpid` |
| [tavpkit](https://github.com/tavp-stack/tavpkit) | Starter kits & bundles | `composer require tavp/tavpkit` |
| [tavphub](https://github.com/tavp-stack/tavphub) | Admin panel | `composer require tavp/tavphub` |
| [tavpblocks](https://github.com/tavp-stack/tavpblocks) | UI components | `composer require tavp/tavpblocks` |
| [tavp/analytics](https://github.com/tavp-stack/tavp-analytics) | Analytics | `composer require tavp/analytics` |
| [tavp-installer](https://github.com/tavp-stack/tavp-installer) | Phalcon installer | `sh install_phalcon5.sh` |

## Versioning

TAVP follows Zero-based Versioning (ZeroVer):

| Version | Meaning |
|---------|---------|
| 0.x.0 | Breaking changes bump the minor (x) |
| 0.x.y | Bug fixes bump the patch (y) |

See [0ver.org](https://0ver.org).

## Security

See [SECURITY.md](SECURITY.md) for guidance on handling secrets and reporting security issues.

## License

MIT License

## Community

- [GitHub](https://github.com/tavp-stack)
- [Documentation](https://docs.tavp.web.id/)