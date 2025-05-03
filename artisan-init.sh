#!/bin/bash
php artisan migrate
php artisan storage:link
php artisan config:cache
php artisan cache:clear
