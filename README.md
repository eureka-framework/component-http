# Component Http - PSR-15 implementation

[![Current version](https://img.shields.io/packagist/v/eureka/component-http.svg?logo=composer)](https://packagist.org/packages/eureka/component-http)
[![Supported PHP version](https://img.shields.io/static/v1?logo=php&label=PHP&message=7.4|8.0|8.1&color=777bb4)](https://packagist.org/packages/eureka/component-http)
![CI](https://github.com/eureka-framework/component-http/workflows/CI/badge.svg)
[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=eureka-framework_component-http&metric=alert_status)](https://sonarcloud.io/dashboard?id=eureka-framework_component-http)
[![Coverage](https://sonarcloud.io/api/project_badges/measure?project=eureka-framework_component-http&metric=coverage)](https://sonarcloud.io/dashboard?id=eureka-framework_component-http)

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
