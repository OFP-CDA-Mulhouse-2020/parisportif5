FROM php:7.4.12-apache

ARG APCU_VERSION=5.1.19

ADD . /var/www/html/

# Composer
COPY --from=composer:1.10.17 /usr/bin/composer /usr/local/bin/composer

# PHP Extensions
RUN docker-php-ext-install opcache pdo_mysql -j$(nproc) \
    && pecl install xdebug-2.9.6 \
    && docker-php-ext-enable xdebug \
    && echo "xdebug.remote_enable=1" >> /usr/local/etc/php/php.ini \
    && echo "xdebug.remote_autostart=1" >> /usr/local/etc/php/php.ini \
    && echo "xdebug.remote_host=host.docker.internal" >> /usr/local/etc/php/php.ini \
    && a2enmod rewrite && docker-php-ext-configure opcache --enable-opcache

#INSTALL APCU
RUN pecl install apcu-${APCU_VERSION} && docker-php-ext-enable apcu \
    && echo "extension=apcu.so" >> /usr/local/etc/php/php.ini \
    && echo "apc.enable_cli=1" >> /usr/local/etc/php/php.ini \
    && echo "apc.enable=1" >> /usr/local/etc/php/php.ini
#APCU
