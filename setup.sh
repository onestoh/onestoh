#!/usr/bin/env bash
# EstateYard — Local Setup Script
# Usage: bash setup.sh

set -e

echo ""
echo "======================================"
echo "  EstateYard — Local Setup"
echo "======================================"
echo ""

# 1. Install PHP dependencies
echo "[1/5] Installing Composer dependencies..."
composer install --no-interaction --prefer-dist

# 2. Copy .env
echo "[2/5] Creating .env from example..."
cp .env.example .env

# 3. Generate app key
echo "[3/5] Generating application key..."
php artisan key:generate

# 4. Copy pre-seeded SQLite database
echo "[4/5] Setting up database..."
cp setup/estateyard.sqlite database/estateyard.sqlite
echo "      Database ready (35 tables, 66 demo users, 50 properties)"

# Update DB path in .env to absolute path
DB_PATH="$(pwd)/database/estateyard.sqlite"
if [[ "$OSTYPE" == "darwin"* ]]; then
  sed -i '' "s|DB_DATABASE=|DB_DATABASE=${DB_PATH}|" .env
else
  sed -i "s|DB_DATABASE=|DB_DATABASE=${DB_PATH}|" .env
fi
echo "      DB_DATABASE => ${DB_PATH}"

# 5. Clear caches and start
echo "[5/5] Clearing caches..."
php artisan config:clear
php artisan view:clear

echo ""
echo "======================================"
echo "  READY! Starting server..."
echo ""
echo "  URL:  http://localhost:8000"
echo ""
echo "  Demo Login Credentials (password: password)"
echo "  ─────────────────────────────────────────────"
echo "  Admin:            admin1@estateyard.co.ke"
echo "  Landlord:         landlord1@estateyard.co.ke"
echo "  Tenant:           tenant1@estateyard.co.ke"
echo "  Broker:           broker1@estateyard.co.ke"
echo "  Developer:        developer1@estateyard.co.ke"
echo "  Investor:         investor1@estateyard.co.ke"
echo "  Auctioneer:       auctioneer1@estateyard.co.ke"
echo "  Finance:          finance1@estateyard.co.ke"
echo "  Valuer:           valuer1@estateyard.co.ke"
echo "  Surveyor:         surveyor1@estateyard.co.ke"
echo "  Property Manager: property_manager1@estateyard.co.ke"
echo "  Corporate:        corporate1@estateyard.co.ke"
echo "  ─────────────────────────────────────────────"
echo "======================================"
echo ""
php artisan serve
