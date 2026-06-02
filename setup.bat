@echo off
REM ============================================================
REM  EstateYard — Windows/WAMP Setup Script
REM  Usage: Double-click or run from command prompt
REM
REM  Requirements: PHP 8.2+, Composer, MySQL 8.0+
REM  Run from: C:\wamp64\www\<project-folder>\
REM ============================================================

echo.
echo ======================================
echo   EstateYard -- Windows Setup
echo ======================================
echo.

REM -- Step 1: Create required writable directories
echo [1/7] Creating required directories...
if not exist "bootstrap\cache" mkdir bootstrap\cache
if not exist "storage\app\public" mkdir storage\app\public
if not exist "storage\framework\cache\data" mkdir storage\framework\cache\data
if not exist "storage\framework\sessions" mkdir storage\framework\sessions
if not exist "storage\framework\views" mkdir storage\framework\views
if not exist "storage\logs" mkdir storage\logs
echo       Done.

REM -- Step 2: Set permissions (icacls grants full control)
echo [2/7] Setting folder permissions...
icacls "bootstrap\cache" /grant Everyone:(OI)(CI)F /T /Q
icacls "storage" /grant Everyone:(OI)(CI)F /T /Q
echo       Done.

REM -- Step 3: Composer install
echo [3/7] Installing PHP dependencies...
composer install --no-interaction --prefer-dist --optimize-autoloader
echo       Done.

REM -- Step 4: .env file
echo [4/7] Creating .env file...
if not exist ".env" (
    copy .env.example .env
    echo       .env created. Edit it to set your DB credentials.
) else (
    echo       .env already exists, skipping.
)

REM -- Step 5: App key
echo [5/7] Generating application key...
php artisan key:generate --quiet
echo       Done.

REM -- Step 6: Storage link
echo [6/7] Creating storage symlink...
php artisan storage:link 2>nul
echo       Done.

REM -- Step 7: Clear caches
echo [7/7] Clearing caches...
php artisan config:clear --quiet
php artisan view:clear --quiet
php artisan cache:clear --quiet
echo       Done.

echo.
echo ======================================
echo   Setup complete!
echo.
echo   Next steps:
echo   1. Edit .env and set your MySQL credentials:
echo         DB_HOST=127.0.0.1
echo         DB_DATABASE=estateyard
echo         DB_USERNAME=root
echo         DB_PASSWORD=your_password
echo.
echo   2. Create the database in phpMyAdmin or MySQL:
echo         CREATE DATABASE estateyard CHARACTER SET utf8mb4;
echo.
echo   3. Import the database:
echo         mysql -u root -p estateyard < setup\estateyard_mysql.sql
echo.
echo   4. Start WAMP and open:
echo         http://localhost/kan/onestoh/public
echo.
echo   Demo Accounts (password: password)
echo   -------------------------------------------
echo   Admin:    admin1@estateyard.co.ke
echo   Landlord: landlord1@estateyard.co.ke
echo   Tenant:   tenant1@estateyard.co.ke
echo   Broker:   broker_licensed1@estateyard.co.ke
echo   -------------------------------------------
echo ======================================
echo.
pause
