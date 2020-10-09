# Component Http - PSR-15 implementation

[![Current version](https://img.shields.io/packagist/v/eureka/component-http.svg?logo=composer)](https://packagist.org/packages/eureka/component-http)
[![Supported PHP version](https://img.shields.io/static/v1?logo=php&label=PHP&message=%5E7.4&color=777bb4)](https://packagist.org/packages/eureka/component-http)
[![codecov](https://codecov.io/gh/eureka-framework/component-http/branch/master/graph/badge.svg)](https://codecov.io/gh/eureka-framework/component-http)
[![Build Status](https://travis-ci.org/eureka-framework/component-http.svg?branch=master)](https://travis-ci.org/eureka-framework/component-http)
![CI](https://github.com/eureka-framework/component-http/workflows/CI/badge.svg)

## Usage
 This component is used in [`eureka/kernel-http`](https://github.com/eureka-framework/kernel-http/) component.
 This is a simple implementation of the [PSR-15](https://www.php-fig.org/psr/psr-15/) (Request Handler).

## Installation

You can install the kernel (for testing) with the following command:
```bash
make install
```

## Update

You can update the kernel (for testing) with the following command:
```bash
make update
```

## Testing

You can test the kernel with the following commands:
```bash
make phpcs
make tests
make testdox
```