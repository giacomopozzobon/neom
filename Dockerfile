FROM php:8.2-fpm

# Installa le librerie richieste per PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo_pgsql pgsql

# Installa Node.js e npm
RUN apt-get update && apt-get install -y nodejs npm

# Installa Composer usando curl
RUN apt-get update && apt-get install -y curl \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Pulisci i file temporanei per ridurre le dimensioni dell'immagine
RUN apt-get clean && rm -rf /var/lib/apt/lists/*