# Rosem route management

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

## Structure

If any of the following are applicable to your project, then the directory structure should follow industry best practices by being named the following.

```
bin/        
config/
src/
tests/
vendor/
```


## Install

Via Composer

``` bash
$ composer require rosem/route
```

## Usage

``` php
$router = new Rosem\Route\Router();

$router->addRoute('GET', '/user/{id:\d+}', 'handle')
    ->addMiddleware('Auth', ['setType' => 'digest'])
    ->addMiddleware('CSRF');

$result = $router->dispatch('GET', '/user/123');

echo $result === [
    // HTTP status code
    0 => 200,
    // Handler
    1 => 'handle',
    // Middleware list
    2 => [
         0 => [
            0 => 'Auth',
            1 => [
                'setName' => 'digest',
            ]
         ],
         1 => [
            0 => 'CSRF',
            1 => []
         ],
    ],
    // Variables list
    3 => [
         'id' => '123',
    ],
];
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please email iroman.via@gmail.com instead of using the issue tracker.

## Credits

- [Roman Shevchenko][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/rosem/route.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/rosem/route/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/rosem/route.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/rosem/route.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/rosem/route.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/rosem/route
[link-travis]: https://travis-ci.org/rosem/route
[link-scrutinizer]: https://scrutinizer-ci.com/g/rosem/route/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/rosem/route
[link-downloads]: https://packagist.org/packages/rosem/route
[link-author]: https://github.com/roshecode
[link-contributors]: ../../contributors
