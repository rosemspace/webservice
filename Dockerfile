FROM php:8-apache

MAINTAINER Roman Shevchenko <iroman.via@gmail.com>

WORKDIR /var/www

# Install Xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Set up Apache2
COPY docker/apache/000-default.conf ${APACHE_CONFDIR}/sites-available/000-default.conf
RUN a2enmod rewrite

EXPOSE 443
