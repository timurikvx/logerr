#!/bin/bash
php artisan migrate
php artisan cache:clear
php artisan rabbit:errors
