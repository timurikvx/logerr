#!/bin/bash

composer update
npm install
npm run build

php artisan migrate
php artisan storage:link
php artisan config:cache
php artisan cache:clear

exec php-fpm -F
