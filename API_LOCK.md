# TAVP Stack — Stable Release (1.0.0)

## Public API Lock Notice

As of version 1.0.0, the TAVP Stack public API is **locked**. This means:

- **No breaking changes** without a major version bump
- **Semantic Versioning** (SemVer) applies from this point
- **Backward compatibility** guaranteed for all public interfaces
- **Deprecation policy**: Deprecated features remain for at least 2 minor versions

## What is Locked?

### PHP Classes and Interfaces
- All public classes in `src/` namespace
- All public methods and their signatures
- All interfaces and their contracts
- All trait definitions

### Configuration
- Configuration file structure
- Environment variable names
- Default values

### CLI Commands
- Command names and arguments
- Command output format
- Exit codes

### Routes
- API endpoint paths
- HTTP methods
- Request/response format

### Views
- View file names and locations
- Template variable names
- Component API

## What Can Change?

- Internal implementation details
- Performance optimizations
- Bug fixes
- New features (backward-compatible)
- Documentation

## Migration Guide

When upgrading between minor versions, refer to the CHANGELOG for:
- Deprecated features
- New alternatives
- Migration steps

## Post-1.0 Commitments

- **SemVer**: MAJOR.MINOR.PATCH
- **LTS**: 2 years of security patches per MAJOR version
- **Changelog**: Detailed per-version entries
- **Backward Compatibility**: Deprecated features kept for 2+ minor versions
- **RFC Process**: Major changes go through community review
