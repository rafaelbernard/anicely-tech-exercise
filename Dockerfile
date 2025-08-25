ARG PHP_VERSION=8.4

FROM php:${PHP_VERSION}-alpine AS base-env

WORKDIR /tmp
RUN apk add --no-cache \
    libstdc++ nodejs npm \
    # composer \
    && wget https://getcomposer.org/installer \
    && php ./installer && rm installer \
    && mv composer.phar /usr/local/bin/composer

# ----------------
# PHP Extensions
# ----------------
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions intl pdo_mysql xdebug

COPY ./run-docker.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/run-docker.sh

WORKDIR /app

COPY package.json package-lock.json ./

RUN npm ci

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install --no-scripts --no-dev --no-autoloader

###> recipes ###
###< recipes ###

COPY . .

EXPOSE 8000

CMD ["/usr/local/bin/run-docker.sh"]
