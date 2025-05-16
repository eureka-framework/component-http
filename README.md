# Component Http - PSR-15 implementation

[![Current version](https://img.shields.io/packagist/v/eureka/component-http.svg?logo=composer)](https://packagist.org/packages/eureka/component-http)
[![Supported PHP version](https://img.shields.io/static/v1?logo=php&label=PHP&message=7.4%20-%208.4&color=777bb4)](https://packagist.org/packages/eureka/component-http)
![CI](https://github.com/eureka-framework/component-http/workflows/CI/badge.svg)
[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=eureka-framework_component-http&metric=alert_status)](https://sonarcloud.io/dashboard?id=eureka-framework_component-http)
[![Coverage](https://sonarcloud.io/api/project_badges/measure?project=eureka-framework_component-http&metric=coverage)](https://sonarcloud.io/dashboard?id=eureka-framework_component-http)

## Usage
 This component is used in [`eureka/kernel-http`](https://github.com/eureka-framework/kernel-http/) component.
 This is a simple implementation of the [PSR-15](https://www.php-fig.org/psr/psr-15/) (Request Handler).

## Testing & CI (Continuous Integration)

You can run tests on your side with following commands:
```bash
make php/tests   # run tests with coverage
make php/test    # run tests with coverage
make php/testdox # run tests without coverage reports but with prettified output
```

You also can run code style check or code style fixes with following commands:
```bash
make php/check   # run checks on check style
make php/fix     # run check style auto fix
```

To perform a static analyze of your code (with phpstan, lvl 9 at default), you can use the following command:
```bash
make php/analyze # Same as phpstan but with CLI output as table
```

To ensure you code still compatible with current supported version and futures versions of php, you need to
run the following commands (both are required for full support):
```bash
make php/min-compatibility # run compatibility check on current minimal version of php we support
make php/max-compatibility # run compatibility check on last version of php we will support in future
```

And the last "helper" commands, you can run before commit and push is:
```bash
make ci
```
This command clean the previous reports, install component if needed and run tests (with coverage report),
check the code style and check the php compatibility check, as it would be done in our CI.

## Contributing

See the [CONTRIBUTING](CONTRIBUTING.md) file.

## License

This project is currently under The MIT License (MIT). See [LICENCE](LICENSE) file for more information.
