#!/bin/sh
#set -x

cd /app
npm ci
# It will run encore and prepare assets
npm run dev
composer install --no-scripts

# TODO: For prod
# - Use the webserver to be used in prod, such as httpd or nginx with php-fpm
# - composer optimised. ex: composer dump-autoload --optimize --classmap-authoritative
# TODO: For dev
# - To add a proxy to have ssl termination, such as jwilder-proxy

php -S 0.0.0.0:8000 -t public
