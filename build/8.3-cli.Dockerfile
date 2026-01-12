FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
    cron \
    openssl \
    git \
    unzip \
    pkg-config \
    libssl-dev \
    && rm -rf /var/lib/apt/lists/*

RUN curl -sSLf \
    -o /usr/local/bin/install-php-extensions \
    https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions \
    && chmod +x /usr/local/bin/install-php-extensions

RUN install-php-extensions \
    mongodb-1.17.3 \
    gettext \
    iconv \
    intl \
    tidy \
    zip \
    sockets \
    pgsql \
    mysqli \
    pdo_mysql \
    pdo_pgsql \
    redis \
    xdebug \
    @composer

COPY php.ini /usr/local/etc/php/

WORKDIR /var/php
