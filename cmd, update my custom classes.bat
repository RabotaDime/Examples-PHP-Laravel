@echo off

php artisan clear-compiled
composer dump-autoload
php artisan optimize

