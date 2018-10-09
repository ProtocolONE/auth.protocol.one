FROM composer AS composer

WORKDIR /app

COPY composer.* /app/
RUN composer install --ignore-platform-reqs --prefer-source --no-interaction

FROM p1hub/p1-dev.php-fpm
LABEL maintainer="Vadim Sabirov <vadim.sabirov@protocol.one>"

WORKDIR /app
COPY --from=composer /app/vendor/ /app/vendor/
COPY . /app/

RUN chown -R www-data:www-data /app/var

CMD ["php-fpm"]