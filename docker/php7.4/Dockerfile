FROM php:7.4.12-apache

ADD . /var/www/html/

# Composer
COPY --from=composer:1.10.17 /usr/bin/composer /usr/local/bin/composer

# PHP Extensions
RUN docker-php-ext-install -j$(nproc) opcache pdo_mysql \
    && pecl install xdebug-2.9.6 \
    && docker-php-ext-enable xdebug \
    && echo "xdebug.remote_enable=1" >> /usr/local/etc/php/php.ini \
    && echo "xdebug.remote_autostart=1" >> /usr/local/etc/php/php.ini \
    && echo "xdebug.remote_host=host.docker.internal" >> /usr/local/etc/php/php.ini \
    && a2enmod rewrite
