# TAVP Stack

> **T**ailwind CSS + **A**lpine.js + **V**olt + **P**halcon = TAVP

TAVP is a curated PHP tech stack — not a framework. It combines Phalcon's C-extension performance with modern frontend tools (Tailwind CSS, Alpine.js) and Volt templating. Think of it as "Laravel ergonomics + Phalcon speed."

**Current Version: 1.0.0 (Stable)**

## Features

- **Phalcon 5.16** — High-performance C-extension MVC framework
- **Volt Templates** — Fast, secure template engine (compiled to PHP, not Livewire-based)
- **Tailwind CSS** — Utility-first CSS framework
- **Alpine.js** — Lightweight JavaScript framework
- **OTP Authentication** — Passwordless login via email, SMS, WhatsApp
- **JWT API Auth** — Token-based API authentication
- **Role & Permission** — RBAC system
- **CLI Tools** — Code generation, migrations, deployment
- **40+ UI Components** — TAVPblocks component library (Tailwind + Alpine)
- **AI Integration** — OpenAI, Anthropic, Ollama support
- **Module System** — Composer-based package discovery
- **Marketplace** — Module and theme marketplace
- **4 Runtimes** — PHP-FPM, TAVP Coil (Swoole), TAVP Relay (RoadRunner), TAVP Weave (PHP Fibers)
- **SaaS Billing** — Stripe, Midtrans, Xendit, PayPal
- **Kubernetes & Terraform** — Production deployment

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
- [Runtimes](https://docs.tavp.web.id/runtimes/overview)
- [FAQ](https://docs.tavp.web.id/reference/faq)

## System Requirements

- PHP 8.3+
- Phalcon 5.16+ (install with `tavp phalcon:install`)
- Node.js 18+ (for frontend assets)
- Composer 2.x

## Performance

Benchmarked on a 2-core VPS with 2GB RAM:

| Metric | PHP-FPM | TAVP Coil (Swoole) |
|--------|---------|---------------------|
| Requests/sec | 5,000+ | 12,000+ |
| P95 Latency | <5ms | <2ms |
| Memory per worker | <15MB | <8MB |

## Ecosystem

| Package | Description | Install |
|---------|-------------|---------|
| [tavp/core](https://github.com/tavp-stack/tavp-core) | Framework foundation | `composer create-project tavp/core` |
| [tavp/cli](https://github.com/tavp-stack/tavp-cli) | CLI tool (`tavp` command) | `composer global require tavp/cli` |
| [tavpid](https://github.com/tavp-stack/tavpid) | OTP-first authentication | `composer require tavp/tavpid` |
| [tavpkit](https://github.com/tavp-stack/tavpkit) | Starter kits & bundles | `composer require tavp/tavpkit` |
| [tavphub](https://github.com/tavp-stack/tavphub) | Admin panel | `composer require tavp/tavphub` |
| [tavpblocks](https://github.com/tavp-stack/tavpblocks) | 40+ UI components | `composer require tavp/tavpblocks` |
| [tavphive](https://github.com/tavp-stack/tavphive) | Billing & subscriptions | `composer require tavp/tavphive` |
| [tavp-marketplace](https://github.com/tavp-stack/tavp-marketplace) | Module marketplace | `composer require tavp/tavp-marketplace` |
| [tavp-installer](https://github.com/tavp-stack/tavp-installer) | Phalcon installer | `sh install_phalcon5.sh` |
| [tavp/analytics](https://github.com/tavp-stack/tavp-analytics) | Analytics & fraud detection | `composer require tavp/analytics` |
| [tavp/starter](https://github.com/tavp-stack/tavp-starter) | Project template | `tavp new my-app` |

## Versioning

TAVP follows Semantic Versioning:
- **1.0.0** — Stable (public API locked, SemVer applies)
- **1.x.y** — Minor (backward-compatible features)
- **x.0.0** — Major (breaking changes)

## License

MIT License

## Community

- [GitHub](https://github.com/tavp-stack)
- [Documentation](https://docs.tavp.web.id/)
- [Discord](https://discord.gg/tavp)
- [Twitter](https://twitter.com/tavpstack)
