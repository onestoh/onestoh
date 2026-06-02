#!/usr/bin/env bash
# ============================================================
#  EstateYard — Local Setup Script
#  Usage: bash setup.sh
#
#  Requirements: PHP 8.2+, Composer, MySQL 8.0+
# ============================================================

set -e

GREEN='\033[0;32m'
GOLD='\033[0;33m'
NC='\033[0m'

echo ""
echo -e "${GOLD}======================================"
echo "  EstateYard — Local Setup"
echo -e "======================================${NC}"
echo ""

# ── Step 1: Composer ──────────────────────────────────────────
echo "[1/6] Installing PHP dependencies..."
composer install --no-interaction --prefer-dist --optimize-autoloader
echo -e "      ${GREEN}Done.${NC}"

# ── Step 2: .env ──────────────────────────────────────────────
echo "[2/6] Creating .env file..."
cp .env.example .env
echo -e "      ${GREEN}Done. Edit .env to set your DB credentials.${NC}"

# ── Step 3: App key ───────────────────────────────────────────
echo "[3/6] Generating application key..."
php artisan key:generate --quiet
echo -e "      ${GREEN}Done.${NC}"

# ── Step 4: Database import ───────────────────────────────────
echo "[4/6] Database setup..."
echo ""
echo "  Open .env and set your MySQL credentials:"
echo "    DB_HOST=127.0.0.1"
echo "    DB_DATABASE=estateyard"
echo "    DB_USERNAME=root"
echo "    DB_PASSWORD=your_password"
echo ""
echo "  Then import the database dump:"
echo "    mysql -u root -p estateyard < setup/estateyard_mysql.sql"
echo ""
echo "  (Or run fresh migrations: php artisan migrate --seed)"
echo ""

# ── Step 5: Storage link ──────────────────────────────────────
echo "[5/6] Creating storage symlink..."
php artisan storage:link --quiet 2>/dev/null || true
echo -e "      ${GREEN}Done.${NC}"

# ── Step 6: Clear caches ──────────────────────────────────────
echo "[6/6] Clearing caches..."
php artisan config:clear --quiet
php artisan view:clear --quiet
php artisan cache:clear --quiet
echo -e "      ${GREEN}Done.${NC}"

echo ""
echo -e "${GOLD}======================================"
echo "  Setup complete!"
echo ""
echo "  Once DB is configured, start the server:"
echo "    php artisan serve"
echo ""
echo "  App URL: http://localhost:8000"
echo ""
echo "  Demo Accounts (password: password)"
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
echo -e "  ─────────────────────────────────────────────${NC}"
echo ""
