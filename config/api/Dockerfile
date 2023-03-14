FROM php:8.2.3-fpm-alpine

WORKDIR /var/www

RUN apk update && apk add freetype-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    libpng-dev \
    imagemagick-dev \
    libmcrypt-dev \
    oniguruma-dev \
    libmemcached-dev \
    icu-dev \
    libpq-dev \
    imap-dev \
    krb5-dev \
    postgresql \
    curl \
    git \
    tzdata \
    linux-headers \
#    libzip-dev \
#    unzip \
    $PHPIZE_DEPS

RUN docker-php-ext-install mbstring
RUN docker-php-ext-install mysqli
RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install intl
RUN docker-php-ext-install pdo_pgsql
RUN docker-php-ext-install pgsql
RUN docker-php-ext-install exif
RUN docker-php-ext-install opcache
#RUN docker-php-ext-install zip

RUN pecl install memcached \
    && docker-php-ext-enable memcached

RUN pecl install redis \
    && docker-php-ext-enable redis

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

RUN pecl install -n mcrypt \
    && docker-php-ext-enable mcrypt

RUN pecl install imagick \
    && docker-php-ext-enable imagick

RUN docker-php-ext-configure imap --with-kerberos --with-imap-ssl \
    && docker-php-ext-install imap

RUN docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ \
    && docker-php-ext-install gd

RUN curl -L -o /tmp/composer-setup.php "https://getcomposer.org/installer" \
    && php /tmp/composer-setup.php --install-dir=/bin --filename=composer \
    && rm /tmp/composer-setup.php

RUN echo "UTC" > /etc/timezone

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
