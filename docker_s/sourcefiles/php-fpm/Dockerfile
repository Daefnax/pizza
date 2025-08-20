FROM php:8.3-fpm

WORKDIR /application

# Базовые пакеты
RUN apt-get update && apt-get install -y --no-install-recommends \
    curl git openssh-client nano \
    libzip-dev zip libicu-dev libpq-dev \
    libfreetype6-dev libjpeg62-turbo-dev libpng-dev \
    libsqlite3-dev libsodium-dev \
 && rm -rf /var/lib/apt/lists/*

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# PHP extensions
RUN docker-php-ext-install zip bcmath pdo_sqlite sodium \
 && docker-php-ext-configure intl \
 && docker-php-ext-install intl \
 && docker-php-ext-configure pgsql --with-pgsql=/usr/local/pgsql \
 && docker-php-ext-install pdo_pgsql pgsql \
 && docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ \
 && docker-php-ext-install gd

RUN git config --system --add safe.directory /application

RUN mkdir -p /application/storage /application/bootstrap/cache \
 && chown -R www-data:www-data /application \
 && chmod -R ug+rwX /application

USER www-data

CMD ["php-fpm"]
