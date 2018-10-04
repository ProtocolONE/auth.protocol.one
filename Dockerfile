FROM composer AS composer

WORKDIR /app

COPY composer.* /app
RUN composer install --ignore-platform-reqs --prefer-source --no-interaction

FROM p1hub/p1-dev.php-fpm
LABEL maintainer="Vadim Sabirov <vadim.sabirov@protocol.one>"

COPY . /app
WORKDIR /app

COPY --from=composer /app/vendor/ /app/vendor/

RUN chown -R www-data:www-data /app

CMD ["php-fpm"]