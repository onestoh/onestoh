#!/usr/bin/env bash
# EstateYard — Local Setup Script
# Usage: bash setup.sh
#
# NOTE: MySQL is now the default database engine.
# A pre-built SQL dump is available at setup/estateyard_mysql.sql.
# Import it into MySQL before running this script:
#
#   mysql -u root -p < setup/estateyard_mysql.sql
#
# Alternatively, if you prefer to run fresh migrations + seeders instead
# of importing the SQL dump, run after setup completes:
#
#   php artisan migrate --seed
#
# SQLite fallback: set DB_CONNECTION=sqlite and DB_DATABASE=/absolute/path/to/database/estateyard.sqlite in .env

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

# 4. Database setup
echo "[4/5] Setting up database..."
if [ "${DB_CONNECTION:-mysql}" = "sqlite" ]; then
  echo "      SQLite mode: copying pre-seeded database..."
  cp setup/estateyard.sqlite database/estateyard.sqlite
  DB_PATH="$(pwd)/database/estateyard.sqlite"
  if [[ "$OSTYPE" == "darwin"* ]]; then
    sed -i '' "s|DB_DATABASE=|DB_DATABASE=${DB_PATH}|" .env
  else
    sed -i "s|DB_DATABASE=|DB_DATABASE=${DB_PATH}|" .env
  fi
  echo "      DB_DATABASE => ${DB_PATH}"
  echo "      Database ready (35 tables, 66 demo users, 50 properties)"
else
  echo "      MySQL mode: import the SQL dump manually if you haven't already:"
  echo "        mysql -u root -p < setup/estateyard_mysql.sql"
  echo "      Or run migrations + seeders after setup: php artisan migrate --seed"
fi

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
