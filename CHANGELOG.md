# Changelog

All notable changes to TAVP Stack will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.8.0] - 2026-07-09

### Added
- Module marketplace system
- Theme marketplace system
- Revenue tracking and payouts
- Review and rating system
- Community guidelines and templates

## [0.7.0] - 2026-07-09

### Added
- AI Manager with OpenAI, Anthropic, Ollama, TAVP Cloud drivers
- AI Coder for module generation
- AI Content for optimization and translation
- AI Assistant chatbot
- Theme system with options

## [0.6.0] - 2026-07-09

### Added
- TAVP Relay (RoadRunner runtime)
- TAVPhive billing (Stripe, Midtrans, Xendit, PayPal)
- Kubernetes manifests (deployment, service, ingress, HPA)
- Terraform modules (VPC, RDS, ElastiCache)

## [0.5.0] - 2026-07-09

### Added
- TAVP Coil (Swoole runtime with coroutines)
- Connection pooling
- Search abstraction (Meilisearch, Elasticsearch, Database)
- Broadcasting (Soketi, Pusher, Redis, Log)

## [0.4.0] - 2026-07-09

### Added
- Structured logging (JSON format, request ID correlation)
- Sentry integration
- Health check endpoints (DB, Redis, Queue, Storage)
- Production optimization command
- Docker production build
- PHPStan level 8 configuration
- OpenAPI documentation generator

## [0.3.0] - 2026-07-09

### Added
- TAVPkit (Teams, API tokens, Profile management)
- TAVPhub (Resource generator, Table builder, Form builder)
- Module system (Registry, Discovery, Service Providers)
- Cache abstraction (File, Redis, APCu)
- Queue abstraction (Database, Redis)
- Storage abstraction (Local, S3)
- Social OAuth (Google, Apple)
- TAVPblocks expanded to 40+ components

## [0.2.0] - 2026-07-09

### Added
- CLI: make:scaffold, weave:enable, migrate:fresh --seed
- OTP via SMS (Twilio)
- OTP via WhatsApp (Twilio)
- Role and Permission system
- Magic Link authentication
- Event dispatcher and listeners
- Mail abstraction (SMTP, Mailgun, SES)
- Flash messages
- Form validation patterns
- Debug toolbar
- Hot reload server
- VS Code extension (syntax highlighting, snippets)
- TAVPblocks basic (15 components)

## [0.1.0] - 2026-07-09

### Added
- Core framework (Router, Controller, Volt, ORM, Migration, Validation, Middleware, DI, Config)
- CLI commands (new, serve, make:model, make:controller, make:migration, migrate)
- Auto Phalcon installer
- OTP authentication (Email)
- Session authentication
- JWT authentication
- Rate limiting
- Dashboard views
- Error pages (403, 404, 419, 429, 500, 503)
- Database migrations
- Documentation site
