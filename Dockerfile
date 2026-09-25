FROM dunglas/frankenphp:php8.4.26-trixie

RUN install-php-extensions mongodb redis

COPY Caddyfile /etc/frankenphp/Caddyfile

COPY . /app

WORKDIR /app