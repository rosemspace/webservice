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

Build the Docker container and start the webserver:

```shell
docker-compose build --build-arg uid=$(id -u $USER) --build-arg gid=$(id -g $USER)
docker-compose up server
# or on a different IP and/or port
docker-compose run --rm -p 127.0.0.127:80:80 server
```

Install project dependencies:

```shell
docker-compose exec server composer install --ignore-platform-reqs --no-interaction --no-scripts
```

To run commands inside the container:

```shell
# Rosem CLI commands
docker-compose exec server bin/rosem
# OS commands
docker-compose exec server bash
```

To start PHP's internal webserver on port 8000:
```bash
docker-compose exec server php -S 0.0.0.0:8000 -t public server.php
```
