# Change Log
All notable changes to this project are documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased](https://github.com/orca-services/cakephp-app-version)

### Added
- More ways to expose the app version, all reading `App.version` [#18](https://github.com/orca-services/cakephp-app-version/issues/18):
    - `AppVersion` view helper with `get()` and `meta()` (HTML meta tag, disabled by default)
    - Optional JSON version endpoint, e.g. `GET /version` (disabled by default, configurable path and middleware)
    - `bin/cake app_version` CLI command
    - Optional banner with the PHP, CakePHP and app version before every CLI command (disabled by default)

### Changed

### Dependencies

### Fixed

## [1.0.1](https://github.com/orca-services/cakephp-app-version/releases/tag/1.0.1) - 2026-06-25

### Changed
- Improved overall documentation
- Exclude repository-only files from archives to reduce Composer distribution package size [#7](https://github.com/orca-services/cakephp-app-version/issues/7)

### Dependencies
- cakephp/cakephp updated from 5.3.0 to 5.3.4 patch
- phpunit/phpunit updated from 10.5.60 to 10.5.63 patch
- cakephp/cakephp-codesniffer installed in version 5.3.0

## [1.0.0](https://github.com/orca-services/cakephp-app-version/releases/tag/1.0.0) - 2026-02-17

### Changed
- Upgraded to CakePHP 5.0.x

### Dependencies
- cakephp/chronos updated from 2.4.5 to 3.3.1 major
- cakephp/cakephp updated from 4.6.3 to 5.3.0 major
- phpunit/phpunit updated from 9.6.31 to 10.5.60 major

## [0.1.0](https://github.com/orca-services/cakephp-app-version/releases/tag/0.1.0) - 2024-10-03

### Added
- Initial functionality with configurable app version HTTP header adding middleware

### Dependencies
- cakephp/chronos installed in version 2.4.5
- cakephp/cakephp installed in version 4.6.3
- phpunit/phpunit installed in version 9.6.31
