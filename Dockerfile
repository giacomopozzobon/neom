FROM php:8.2-fpm

# Installa librerie richieste per PostgreSQL, Node.js e npm
RUN apt-get update && apt-get install -y \
    libpq-dev \
    curl \
    && docker-php-ext-install pdo_pgsql pgsql \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g npm@latest \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*