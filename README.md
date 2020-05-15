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

## Ubuntu requirements

- unzip

## PHP requirements

- php7.4-zip (zip)
- php7.4-xml (ext-dom)
- php7.4-mbstring (ext-mbstring)
