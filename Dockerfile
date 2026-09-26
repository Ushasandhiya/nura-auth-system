FROM dunglas/frankenphp:php8.4.26-trixie

# Install required PHP extensions
RUN install-php-extensions mongodb redis zip

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy the complete project
COPY . /app

# Install PHP dependencies inside the final application directory
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Use our Caddy configuration
COPY Caddyfile /etc/frankenphp/Caddyfile