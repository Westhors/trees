@echo off
composer dump && php artisan optimize:clear && php artisan optimize
