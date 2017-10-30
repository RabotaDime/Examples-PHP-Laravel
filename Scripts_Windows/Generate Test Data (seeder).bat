@echo on
cls
cd ".."

php artisan migrate:refresh
php artisan db:seed

pause