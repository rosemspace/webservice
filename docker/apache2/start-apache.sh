#!/usr/bin/env bash

cd /var/www || { echo "Failed to go to /var/www"; exit 1; }

env=${APP_ENV:-"production"}

if [ "$env" = "production" ]
then
  # Apache, by default, listens on port 80 (HTTP), this isn’t a problem when
  # running the server on your machine. But some cloud providers require that
  # containers use different ports.
  sed -i "s/Listen 80/Listen ${PORT:-80}/g" /etc/apache2/ports.conf
  sed -i "s/:80/:${PORT:-80}/g" /etc/apache2/sites-enabled/*
#else
fi

echo "Application environment: $env"

apache2-foreground
