# TAVP Stack — API Lock Notice

## Public API Lock

The TAVP Stack public API is versioned using **Zero-based Versioning** (ZeroVer, see [0ver.org](https://0ver.org)). The major version stays at `0`:

- **No breaking changes** without a minor (x) bump in `0.x.y`
- **Deprecation policy**: Deprecated features remain for at least 2 minor versions
- **Backward compatibility** guaranteed for all public interfaces

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

When upgrading between versions, refer to the CHANGELOG for:
- Deprecated features
- New alternatives
- Migration steps

## Versioning Commitments

- **ZeroVer**: major version stays at `0`, minor bumps for breaking changes, patch for bug fixes
- **Changelog**: Detailed per-version entries
- **Backward Compatibility**: Deprecated features kept for 2+ minor versions
