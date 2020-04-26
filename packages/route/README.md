# Rosem route - Fast request routes management

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Total Downloads][ico-downloads]][link-downloads]

## Install

Via Composer

``` bash
$ composer require rosem/route
```

## Usage

``` php
$router = new Rosem\Route\Router();
$router->addRoute('GET', '/user/{id:\d+}', 'handle');
$result = $router->dispatch('GET', '/user/rosem');

echo $result === [
    // HTTP status code
    0 => 200,
    // Handler
    1 => 'handle',
    // Variables list
    2 => [
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

Special thanks to these guys and their articles:

1. [Fast request routing using regular expressions](http://nikic.github.io/2014/02/18/Fast-request-routing-using-regular-expressions.html) by [Nikita Popov](https://github.com/nikic)
1. [Making Symfony router lightning fast 1/2](https://medium.com/@nicolas.grekas/making-symfonys-router-77-7x-faster-1-2-958e3754f0e1) by [Nicolas Grekas](https://github.com/nicolas-grekas)
2. [Making Symfony router lightning fast 2/2](https://medium.com/@nicolas.grekas/making-symfony-router-lightning-fast-2-2-19281dcd245b) by [Nicolas Grekas](https://github.com/nicolas-grekas)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/rosem/route.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/rosem/route.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/rosem/route
[link-downloads]: https://packagist.org/packages/rosem/route
[link-author]: https://github.com/roshecode
[link-contributors]: ../../contributors
