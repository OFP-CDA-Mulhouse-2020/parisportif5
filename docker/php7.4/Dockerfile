FROM php:7.4.12-apache
# COPY config/php.ini var/www/php/config/php.ini

WORKDIR /app

COPY . /app/

ENV COMPOSER_ALLOW_SUPERUSER=1
COPY --from=composer:1.10.17 /usr/bin/composer /usr/local/bin/composer

RUN docker-php-ext-install pdo_mysql \
    && pecl install xdebug && docker-php-ext-enable xdebug \
    && docker-php-ext-install opcache \
    && echo "xdebug.remote_enable=on" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.remote_host = host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini 


# FROM alpine:3.4

# # Install ca-certificates, required for the "release message" feature:
# RUN apk --no-cache add \
#     ca-certificates

# # Install MailHog:
# RUN apk --no-cache add --virtual build-dependencies \
#     go \
#     git \
#   && mkdir -p /root/gocode \
#   && export GOPATH=/root/gocode \
#   && go get github.com/mailhog/MailHog \
#   && mv /root/gocode/bin/MailHog /usr/local/bin \
#   && rm -rf /root/gocode \
#   && apk del --purge build-dependencies

# # Add mailhog user/group with uid/gid 1000.
# RUN adduser -D -u 1000 mailhog

# USER mailhog

# WORKDIR /home/mailhog

# ENTRYPOINT ["MailHog"]

# # Expose the SMTP and HTTP ports:
# EXPOSE 1025 8025