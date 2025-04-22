@echo off
cd C:\Users\bacs2\Downloads\proy\precipitation_backend
php artisan app:migrate-mems >> storage\logs\schedule.log 2>&1
