# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

```
## [tag] - YYYY-MM-DD
[tag]: https://github.com/eureka-framework/component-http/compare/5.1.0...master
### Changed
 - Change 1
### Added
 - Added 1
### Removed
 - Remove 1
```
----

## [5.2.0] - 2023-06-14
[5.2.0]: https://github.com/eureka-framework/component-http/compare/5.1.0...5.2.0
### Added
- PHPStan config for PHP 8.2 compatibility check
### Changed
- Now compatible with PHP 8.2
- Update Makefile

## [5.1.0] - 2022-10-29
[5.1.0]: https://github.com/eureka-framework/component-http/compare/5.0.1...5.1.0
### Added
 - Add PHPStan + config
### Removed
 - PHP Compatibility
### Changed
 - Some minor code style
 - Improve some phpdoc
 - Fix namespace for tests

## [5.0.1] - 2020-10-29
[5.0.1]: https://github.com/eureka-framework/component-http/compare/5.0.0...5.0.1
### Changed
 - Require phpcodesniffer v0.7 for composer 2.0
 - Update GitHub action
### Added
 - Sonarcloud config

## [5.0.0] - 2020-10-09
[5.0.0]: https://github.com/eureka-framework/component-http/compare/4.0.0...5.0.0
### Changed
 - Require php 7.4+
 - Tests
### Removed
 - PSR-7 implementation (prefer use nyholm implementation)
 - PSR-17 implementation (same reason)
 - Old session & data bag helper (now in kernel-http)


## [4.0.0] - 2018-11-16
[5.0.0]: https://github.com/eureka-framework/component-http/compare/3.0.0...4.0.0
### Added
 - Add HttpFactory implementations using new PSR-17
### Changed
 - Require php 7.2+
 - Move code
### Removed
 - Remove unused & deprecated code
 - Remove deprecated tests
 - Removed PSR-15 for PHP 5.6 implementation

## 3.0.0 - 2018 - New major version: use PHP 7+
### Added
 - Add final PSR-15 / PHP 5.6 implementation
### removed
 - Remove old PSR-15 middleware implementation
