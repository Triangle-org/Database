# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Changed
- Support Illuminate Database, Events, and Pagination 12.x and 13.x. Illuminate 13 requires PHP 8.4.1 or newer; PHP 8.2 and 8.3 continue to resolve Illuminate 12.
- Verify both Illuminate major-version tracks in GitHub Actions.

---

## [2.0.0] - 2026-11-21

### Changed
- Require Triangle Engine 9 and Localzet Server 7.
- Upgrade Illuminate Database, Events, and Pagination to 12.x.
- Require Symfony VarDumper 7.4 and PHP 8.2 or newer.

### Removed
- Drop compatibility with Triangle Engine 8 and earlier.

### Tests
- Add a SQLite integration check for Engine 9 bootstrap and Eloquent model reads/writes.

---

## [1.0.0] - 2024-07-31 - LTS Release

### Added
- Initial LTS release
- Full documentation
- Integration with Illuminate Database
- Query Builder support
- Eloquent ORM support
- Database migrations support
- Connection management
- Multiple database connections

### Changed
- Updated Illuminate Database to ~11.19.0 for LTS stability

### Security
- SQL injection protection through parameterized queries
- Proper connection pooling
