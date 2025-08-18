FROM php:8.3-fpm

WORKDIR "/application"

# Установка всех необходимых пакетов
RUN apt-get update && apt-get install -y --no-install-recommends \
    curl \
    git \
    openssh-client \
    nano \
    libzip-dev \
    zip \
    libicu-dev \
    libpq-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libsqlite3-dev \
    libsodium-dev \
    && rm -rf /var/lib/apt/lists/*

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# PHP расширения (включая sodium)
RUN docker-php-ext-install \
    zip \
    bcmath \
    pdo_sqlite \
    sodium \
    && docker-php-ext-configure intl && docker-php-ext-install intl \
    && docker-php-ext-configure pgsql --with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo_pgsql pgsql \
    && docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ \
    && docker-php-ext-install gd

# Пользователи и права
ARG UID=1000
ARG GID=1000

RUN groupadd -g ${GID} app \
    && useradd -u ${UID} -s /bin/bash -d /home/app -m -g app app \
    && usermod -a -G app www-data

# Создание каталогов и настройка прав
RUN mkdir -p /application/storage /application/bootstrap/cache \
    && chown -R app:app /application \
    && chmod -R 755 /application

USER www-data

CMD ["php-fpm"]
