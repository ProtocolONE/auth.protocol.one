FROM php:7.2.2-fpm

LABEL maintainer="Vadim Sabirov <vadim.sabirov@protocol.one>"

RUN apt-get update \
    && apt-get install -y \
    git \
    libc-client-dev \
    libfreetype6-dev \
    libicu-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libssl-dev \
    libssl-doc \
    libmcrypt-dev \
    libsasl2-dev \
    zlib1g-dev

RUN docker-php-ext-install \
    mbstring \
    zip \
    && docker-php-ext-configure gd \
    --with-freetype-dir=/usr/include/ \
    --with-jpeg-dir=/usr/include/ \
    --with-png-dir=/usr/include/ \
    && docker-php-ext-install gd \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl \
    && docker-php-ext-install opcache \
    && docker-php-ext-enable opcache \
    && pecl install mongodb && docker-php-ext-enable mongodb \
    && pecl install redis && docker-php-ext-enable redis \
    && apt-get autoremove -y --purge \
    && apt-get clean \
    && rm -Rf /tmp/* \
    && rm -Rf /app/*

ADD ./etc/php/php.ini /usr/local/etc/php/conf.d/40-custom.ini

COPY . /app
WORKDIR /app

RUN curl -sS https://getcomposer.org/installer | php \
        && mv composer.phar /usr/local/bin/ \
        && ln -s /usr/local/bin/composer.phar /usr/local/bin/composer \
        && composer install --prefer-source --no-interaction

RUN chown -R www-data:www-data /app

CMD ["php-fpm"]