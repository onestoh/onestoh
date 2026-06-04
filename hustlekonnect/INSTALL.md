# HustleKonnect — Installation Guide

## System Requirements

| Requirement | Version | Install |
|---|---|---|
| PHP | 8.2+ | `sudo apt install php8.2-fpm php8.2-mysql php8.2-redis php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip php8.2-bcmath php8.2-gd` |
| Composer | 2.6+ | `curl -sS https://getcomposer.org/installer \| php && mv composer.phar /usr/local/bin/composer` |
| Node.js | 20 LTS | `curl -fsSL https://deb.nodesource.com/setup_20.x \| sudo -E bash - && sudo apt install nodejs` |
| MySQL | 8.0+ | `sudo apt install mysql-server-8.0` |
| Redis | 7+ | `sudo apt install redis-server` |
| Git | 2.x | `sudo apt install git` |

---

## Step-by-Step Installation

### 1. Clone

```bash
git clone https://github.com/onestoh/hustlekonnect.git
cd hustlekonnect
```

### 2. MySQL Database

```bash
sudo mysql
```
```sql
CREATE DATABASE hustlekonnect CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'hk_user'@'localhost' IDENTIFIED BY 'StrongPassword123!';
GRANT ALL PRIVILEGES ON hustlekonnect.* TO 'hk_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 3. Import Schema

```bash
mysql -u hk_user -p hustlekonnect < database/hustlekonnect.sql
```

This imports all 40+ tables plus seed data (market configs, financing partners, data products, and a default admin user).

### 4. Backend Setup

```bash
cd apps/api
composer install
cp .env.example .env
nano .env   # Fill in DB credentials and API keys
php artisan key:generate
php artisan storage:link
```

### 5. Frontend Setup

```bash
cd ../web
npm install
echo "NEXT_PUBLIC_API_URL=http://localhost:8000" > .env.local
```

### 6. Run

```bash
# Terminal 1 — API
cd apps/api && php artisan serve

# Terminal 2 — Queue (REQUIRED for payments + notifications)
cd apps/api && php artisan queue:work redis --queue=payments,notifications,bookings,default --sleep=3 --tries=3

# Terminal 3 — Frontend
cd apps/web && npm run dev
```

### 7. Create Admin

```bash
cd apps/api && php artisan tinker
```
```php
App\Models\User::where('email', 'admin@hustlekonnect.com')
    ->first()
    ->update(['password' => bcrypt('YourNewStrongPassword123!')]);
```

### 8. Verify

```bash
cd apps/api && php artisan system:health-check
```

---

## Production Checklist

- [ ] Set `APP_ENV=production` and `APP_DEBUG=false`
- [ ] Set strong `APP_KEY` (`php artisan key:generate`)
- [ ] Set `SESSION_SECURE_COOKIE=true`
- [ ] Set `CORS_ALLOWED_ORIGINS` to your frontend domain only
- [ ] Configure Supervisor for queue workers
- [ ] Configure Nginx (see `infrastructure/nginx/hustlekonnect.conf`)
- [ ] Set up SSL via Let's Encrypt (`certbot --nginx`)
- [ ] Add scheduler to crontab: `* * * * * cd /var/www/hustlekonnect/apps/api && php artisan schedule:run`
- [ ] Change default admin password immediately
- [ ] Set all payment gateway credentials
- [ ] Configure Firebase for push notifications
- [ ] Enable `CSP_ENABLED=true` in production

---

## Troubleshooting

| Problem | Fix |
|---|---|
| `Class not found` errors | Run `composer install` then `php artisan config:clear` |
| 500 on API | Check `storage/logs/laravel.log` |
| Queue not processing | Check Redis: `redis-cli ping` → should return PONG |
| M-Pesa callback fails | Use ngrok locally: `ngrok http 8000` and set `APP_URL` to ngrok URL |
| CORS error in browser | Add frontend URL to `CORS_ALLOWED_ORIGINS` in `.env` |
| Storage permission denied | `chmod -R 775 storage bootstrap/cache` |
| MySQL charset error | Ensure MySQL 8.0+ and `DB_CHARSET=utf8mb4` in `.env` |
