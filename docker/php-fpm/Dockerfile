FROM php:7.4-fpm-alpine
COPY wait-for-it.sh /usr/bin/wait-for-it
RUN chmod +x /usr/bin/wait-for-it
RUN apk --update --no-cache add git
RUN apk --update add --virtual build-dependencies build-base openssl-dev autoconf
RUN docker-php-ext-install pdo_mysql
RUN pecl install mongodb; \
    docker-php-ext-enable mongodb
RUN apk del build-dependencies build-base openssl-dev autoconf \
    && rm -rf /var/cache/apk/*
COPY ./php /usr/local/etc/php
COPY --from=composer /usr/bin/composer /usr/bin/composer
WORKDIR /var/www
CMD composer install ; wait-for-it database:3306 -- php bin/console doctrine:migrations:migrate ; wait-for-it mongodb:27017 -- php bin/console doctrine:mongodb:schema:update ;  php-fpm
EXPOSE 9000
