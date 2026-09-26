FROM dunglas/frankenphp:php8.4.26-trixie

# Install required PHP extensions
RUN install-php-extensions mongodb redis

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy Composer files first
COPY composer.json composer.lock ./

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Copy application files
COPY . /app

# Copy Caddy configuration
COPY Caddyfile /etc/frankenphp/Caddyfile