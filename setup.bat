@echo off
REM ============================================================
REM  EstateYard — Windows/WAMP Setup Script
REM  Run this ONCE after cloning/extracting the project.
REM  Open Command Prompt as Administrator, then run: setup.bat
REM ============================================================

echo.
echo ======================================
echo   EstateYard -- Windows Setup
echo ======================================
echo.

REM -- Step 1: Create all required writable directories
echo [1/7] Creating required directories...
if not exist "bootstrap\cache"                    mkdir bootstrap\cache
if not exist "storage\app\private"                mkdir storage\app\private
if not exist "storage\app\public"                 mkdir storage\app\public
if not exist "storage\framework\cache\data"       mkdir storage\framework\cache\data
if not exist "storage\framework\sessions"         mkdir storage\framework\sessions
if not exist "storage\framework\testing"          mkdir storage\framework\testing
if not exist "storage\framework\views"            mkdir storage\framework\views
if not exist "storage\logs"                       mkdir storage\logs
echo       Done.

REM -- Step 2: Grant full write permissions to PHP/WAMP
echo [2/7] Setting folder permissions...
icacls "bootstrap\cache"  /grant Everyone:(OI)(CI)F /T /Q 2>nul
icacls "storage"          /grant Everyone:(OI)(CI)F /T /Q 2>nul
echo       Done.

REM -- Step 3: Composer install
echo [3/7] Installing PHP dependencies...
composer install --no-interaction --prefer-dist --optimize-autoloader
if %errorlevel% neq 0 (
    echo       ERROR: composer install failed. Make sure Composer is installed.
    pause
    exit /b 1
)
echo       Done.

REM -- Step 4: .env file
echo [4/7] Creating .env file...
if not exist ".env" (
    copy .env.example .env >nul
    echo       .env created. Edit it now to set your DB credentials.
) else (
    echo       .env already exists, skipping.
)

REM -- Step 5: App key
echo [5/7] Generating application key...
php artisan key:generate --quiet
echo       Done.

REM -- Step 6: Clear all caches
echo [6/7] Clearing Laravel caches...
php artisan config:clear --quiet
php artisan view:clear  --quiet
php artisan cache:clear --quiet
php artisan route:clear --quiet
echo       Done.

REM -- Step 7: Storage symlink
echo [7/7] Creating storage symlink...
php artisan storage:link 2>nul
echo       Done.

echo.
echo ======================================
echo   Setup complete!
echo.
echo   NEXT STEPS:
echo.
echo   1. Edit .env  ^(open with Notepad^):
echo         DB_HOST=127.0.0.1
echo         DB_DATABASE=estateyard
echo         DB_USERNAME=root
echo         DB_PASSWORD=  ^(your WAMP MySQL password^)
echo         APP_URL=http://localhost/kan/onestoh/public
echo.
echo   2. Create DB in phpMyAdmin or run:
echo         mysql -u root -p -e "CREATE DATABASE estateyard CHARACTER SET utf8mb4;"
echo.
echo   3. Import the database:
echo         mysql -u root -p estateyard ^< setup\estateyard_mysql.sql
echo      ^(Or import via phpMyAdmin: select estateyard DB, Import tab^)
echo.
echo   4. Open your browser:
echo         http://localhost/kan/onestoh/public
echo.
echo   Demo login ^(password: password^):
echo         admin1@estateyard.co.ke
echo         landlord1@estateyard.co.ke
echo         tenant1@estateyard.co.ke
echo ======================================
echo.
pause
