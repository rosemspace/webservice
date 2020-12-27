# Rosem webservice

Middleware based modern PHP web framework.

<a href="https://www.patreon.com/roshe"><img src="https://c5.patreon.com/external/logo/become_a_patron_button.png" alt="Become a Patron!" height="35"></a>

Key features:
- **TODO:** Non-blocking IO
- HTTP2 / HTTP3 support
- All PSRs respected
- Decoupled standalone packages
    - Modular via service providers
    - Extensible via contracts

## Getting started

To build the Docker container, install project dependencies and start the webserver run:

```shell
docker-compose build
docker-compose run --rm -u $(id -u):$(id -g) composer install --no-interaction --no-scripts
docker-compose up server
# or on a different IP and/or port
docker-compose run --rm -p 127.0.0.127:80:80 server
```

Run commands inside the container:

```shell
docker-compose exec server bin/rosem
# or
docker-compose exec server bash
```

To start PHP's internal webserver run the following command:
```bash
docker-compose exec server php -S 0.0.0.0:8000 -t public server.php
```
