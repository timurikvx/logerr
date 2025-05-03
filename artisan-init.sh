#!/bin/bash
php artisan migrate
php artisan storage:link
php artisan cache:clear
