@echo off
cd ".."

php artisan clear-compiled
composer dump-autoload
php artisan optimize

pause
