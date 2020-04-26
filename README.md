# Rosem webservice

Middleware based modern PHP web framework.

<a href="https://www.patreon.com/roshe"><img src="https://c5.patreon.com/external/logo/become_a_patron_button.png" alt="Become a Patron!" height="35"></a>

Key features:
- Non-blocking IO (Swoole is used)
- HTTP2 / HTTP3 support
- PSR respected
- Based on decoupled standalone packages
    - Modular via service providers
    - Extensible via contracts

To start PHP's internal webserver run the following command:
```bash
php -S localhost:8000 -t public server.php
```

## TODO

- Log package
- Cache package
- Security package
    - don't run app while
    ```php
    PHP_SAPI === 'cli-server' && $app->getEnvironment() === AppEnv::PRODUCTION;
    ```
  - remove / escape `HTTP_REFERER`
- `Filesystem\DirectoryList` class
- improve route pattern regex
- 422 not valid form data

## To investigate:

- [Assert](https://github.com/beberlei/assert)
- ValueObject
    - [IP](https://github.com/darsyn/ip)
    - [SemVer](https://github.com/nikolaposa/version)
    - [Money](https://github.com/moneyphp/money)
- [Option](https://github.com/schmittjoh/php-option)
- [Enum](https://github.com/marc-mabe/php-enum)
- [Diff](https://github.com/sebastianbergmann/diff)
- [Cycle ORM](https://github.com/cycle/orm)
- [Swoole](https://awesomeopensource.com/project/swooletw/awesome-swoole)
- [HTTP2](https://www.mnot.net/blog/2019/10/13/h2_api_multiplexing)
- [HTTP3](https://blog.cloudflare.com/http3-the-past-present-and-future/) / [HTTP3 RU](https://ru.hexlet.io/blog/posts/http-3-proshloe-nastoyaschee-i-buduschee)
- [Vulcain](https://github.com/dunglas/vulcain)
