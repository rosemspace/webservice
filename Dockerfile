FROM php:8-apache

MAINTAINER Roman Shevchenko <iroman.via@gmail.com>

# Arguments defined in docker-compose.yml
ARG user
ARG uid=1000
ARG gid=1000
ARG APP_ENV

COPY docker/build /home/root/docker
RUN /home/root/docker/install.sh

# Set working directory
WORKDIR /var/www

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin

RUN install-php-extensions xdebug \
    && docker-php-ext-enable opcache

# Copy Composer to be able to run it later
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set up Apache2
RUN a2enmod rewrite

# Create system user to run Composer and CLI commands. It's important to use the
# `--no-log-init` flag with `useradd`. There is an issue where a high UID value
# will generate huge log files and freeze your system.
# See https://github.com/moby/moby/issues/5419
RUN groupadd -g $gid $user && \
    useradd --no-log-init --create-home --uid $uid --gid $gid --groups www-data,root $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

EXPOSE 80 443 8000

# Set default user
USER $user
