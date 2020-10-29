# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).


## [5.0.1] - 2020-10-29
### Changed
 * Require phpcodesniffer v0.7 for composer 2.0

## [5.0.0] - 2020-10-09
### Changed
 * Require php 7.4+
 * Tests
 
### Removed
 * PSR-7 implementation (prefer use nyholm implementation)
 * PSR-17 implementation (same reason)
 * Old session & data bag helper (now in kernel-http)


## [4.0.0] - 2018-11-16
### Added
 * Add HttpFactory implementations using new PSR-17

### Changed
 * Require php 7.2+
 * Move code
 
### Removed
 * Remove unsued & deprecated code
 * Remove deprecated tests
 * Removed PSR-15 for PHP 5.6 implementation

## [3.0.0] - 2018 - New major version: use PHP 7+
### Added
 * Add final PSR-15 / PHP 5.6 implementation
### removed
 * Remove old PSR-15 middleware implementation