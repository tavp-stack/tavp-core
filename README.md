# TAVP Stack

> **T**ailwind CSS + **A**lpine.js + **V**olt + **P**halcon = TAVP

A curated tech stack for building fast, modern PHP web applications.

## Features

- **Phalcon 5.16** — High-performance C-extension MVC framework
- **Volt Templates** — Fast, secure template engine
- **Tailwind CSS** — Utility-first CSS framework
- **Alpine.js** — Lightweight JavaScript framework
- **OTP Authentication** — Passwordless login via email, SMS, WhatsApp
- **JWT API Auth** — Token-based API authentication
- **Role & Permission** — RBAC system
- **CLI Tools** — Code generation, migrations, deployment
- **40+ UI Components** — TAVPblocks component library
- **AI Integration** — OpenAI, Anthropic, Ollama support
- **Module System** — Composer-based package discovery
- **Marketplace** — Module and theme marketplace

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

- [Getting Started](https://tavp.dev/docs/getting-started)
- [CLI Reference](https://tavp.dev/docs/cli)
- [Authentication](https://tavp.dev/docs/auth)
- [Deployment](https://tavp.dev/docs/deployment)
- [API Reference](https://tavp.dev/docs/api)

## System Requirements

- PHP 8.3+
- Phalcon 5.16+
- Node.js 18+ (for frontend assets)
- Composer 2.x

## Performance

- P99 latency: <5ms (PHP-FPM)
- P99 latency: <2ms (TAVP Coil/Swoole)
- RAM: <15MB per worker
- Throughput: 5000+ req/s (2-core VPS)

## Versioning

TAVP follows [ZeroVer](https://0ver.org/) during development:
- **0.x.y** — Development (anything may change)
- **1.0.0** — Stable (public API locked, SemVer applies)

## License

MIT License

## Community

- [GitHub](https://github.com/tavp-stack)
- [Discord](https://discord.gg/tavp)
- [Twitter](https://twitter.com/tavpstack)
- [Marketplace](https://tavp.dev/marketplace)
