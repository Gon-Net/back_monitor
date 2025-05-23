@echo off
cd C:\"path al archivo.bat"
php artisan app:migrate_predictions >> storage\logs/schedule.log 2>&1
