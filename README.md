# Rosem webservice

Middleware based modern PHP web framework.

<a href="https://www.patreon.com/roshe"><img src="https://c5.patreon.com/external/logo/become_a_patron_button.png" alt="Become a Patron!" height="35"></a>

Key features:
- **TODO:** Non-blocking IO (Swoole is used)
- HTTP2 / HTTP3 support
- PSR respected
- Based on decoupled standalone packages
    - Modular via service providers
    - Extensible via contracts

## Usage

To start PHP's internal webserver run the following command:
```bash
php -S localhost:8000 -t public server.php
```

## TODO

- Log package
- Cache package
- Security package
    - Don't run app while
    ```php
    PHP_SAPI === 'cli-server' && $app->getEnvironment() === AppEnv::PRODUCTION;
    ```
  - Remove / escape `HTTP_REFERER`
- `Filesystem\DirectoryList` class
- Improve route pattern regex
- `422` not valid form data
- Revert reflection container API from the commit `21ba46404771f5bea982736a9891992f4169b8e2`

## Inspired by / used:

Technologies:

- [HTTP2](https://www.mnot.net/blog/2019/10/13/h2_api_multiplexing)
- [HTTP3](https://blog.cloudflare.com/http3-the-past-present-and-future/) / [HTTP3 RU](https://ru.hexlet.io/blog/posts/http-3-proshloe-nastoyaschee-i-buduschee)
- [Swoole](https://awesomeopensource.com/project/swooletw/awesome-swoole)
- [Vulcain](https://github.com/dunglas/vulcain)
- [Service provider](https://github.com/container-interop/service-provider)
- [Queue interop](https://github.com/queue-interop/queue-interop)

Linting:

- [Simplify](https://github.com/symplify/symplify)

Monorepo:

- [Monorepo builder](https://github.com/symplify/monorepo-builder)

Server:

- [Laminas Diactoros](https://github.com/laminas/laminas-diactoros)
- [GraphQL](https://github.com/webonyx/graphql-php)

Session:

- [Storageless](https://github.com/psr7-sessions/storageless)

Routing:

- [FastRoute](https://github.com/nikic/FastRoute)
- [Symfony Routing](https://github.com/symfony/routing)

Templating:

- [Plates](https://github.com/thephpleague/plates)

Validation:

- [Valitron](https://github.com/vlucas/valitron)

Utilities:

- [Bottomline](https://github.com/maciejczyzewski/bottomline)
- [Assert](https://github.com/beberlei/assert)
- [Slugify](https://github.com/cocur/slugify)

Task runners:

- [Robo](https://github.com/consolidation/Robo)

Types:

- ValueObject
    - [IP](https://github.com/darsyn/ip)
    - [SemVer](https://github.com/nikolaposa/version)
    - [Money](https://github.com/moneyphp/money)
- [Option](https://github.com/schmittjoh/php-option)
- [Enum](https://github.com/marc-mabe/php-enum)
- [Diff](https://github.com/sebastianbergmann/diff)

Databases:

- [Latitude](https://github.com/shadowhand/latitude) - SQL query builder
- [Doctrine ORM](https://github.com/doctrine/orm)
- [Cycle ORM](https://github.com/cycle/orm)

Data:

- [FreeGeoDB](https://github.com/delight-im/FreeGeoDB)

Documents:

- [Mail](https://github.com/genkgo/mail)
- [Spout](https://github.com/box/spout) - CSV, XLSX...

Other:

- [Math](https://github.com/markrogoyski/math-php)
