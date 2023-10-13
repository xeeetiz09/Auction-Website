FROM php:fpm-alpine
RUN apk add --no-cache $PHPIZE_DEPS
RUN docker-php-ext-install pdo pdo_mysql
RUN apk update