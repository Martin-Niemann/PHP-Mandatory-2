FROM docker.io/library/php:fpm
COPY ./www.conf /usr/local/etc/php-fpm.d/zzz-docker.conf
RUN pecl install xdebug
RUN docker-php-ext-enable xdebug
RUN docker-php-ext-install pdo pdo_mysql