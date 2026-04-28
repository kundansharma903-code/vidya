# Vidya — Hostinger Deployment Guide

Target URL: `https://vidya.monoloopproductions.in`

---

## Prerequisites

- Hostinger Premium or Business plan
- PHP 8.3+ enabled (set in hPanel → Advanced → PHP Configuration)
- MySQL 8.0 database created in hPanel
- SSH access enabled (hPanel → Advanced → SSH Access)
- Git available on server (usually pre-installed)

---

## Step 1: Create Subdomain

In hPanel → Domains → Subdomains:
- Subdomain: `vidya`
- Domain: `monoloopproductions.in`
- Document root: `/home/u{id}/domains/vidya.monoloopproductions.in/public_html`

---

## Step 2: Create MySQL Database

In hPanel → Databases → MySQL Databases:
- Create database: e.g. `u{id}_vidya`
- Create user + password
- Assign user to database (All Privileges)

Note down: DB name, DB user, DB password, DB host (usually `127.0.0.1`)

---

## Step 3: SSH and Clone Repo

```bash
ssh u{id}@{server-ip}

cd /home/u{id}/domains/vidya.monoloopproductions.in

# Remove default public_html if empty
rmdir public_html

# Clone the repo
git clone https://github.com/kundansharma903-code/vidya.git app

# Link Laravel's public/ as the web root
ln -s /home/u{id}/domains/vidya.monoloopproductions.in/app/laravel/public public_html
```

---

## Step 4: Install Dependencies

```bash
cd /home/u{id}/domains/vidya.monoloopproductions.in/app/laravel

composer install --no-dev --optimize-autoloader
```

---

## Step 5: Configure Environment

```bash
cp .env.production.example .env
nano .env
```

Fill in these values:
```env
APP_KEY=           # leave blank — will generate next
APP_URL=https://vidya.monoloopproductions.in

DB_HOST=127.0.0.1
DB_DATABASE=u{id}_vidya
DB_USERNAME=u{id}_vidya_user
DB_PASSWORD=your_secure_password
```

Generate app key:
```bash
php artisan key:generate
```

---

## Step 6: Run Migrations and Seed

```bash
php artisan migrate --force
php artisan db:seed --force
```

---

## Step 7: Cache and Optimize

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link
```

---

## Step 8: File Permissions

```bash
chmod -R 755 storage bootstrap/cache
chown -R u{id}:u{id} storage bootstrap/cache
```

---

## Step 9: SSL Certificate

In hPanel → SSL → Let's Encrypt:
- Install for `vidya.monoloopproductions.in`
- Force HTTPS: enable

---

## Step 10: Laravel Scheduler (Optional)

In hPanel → Advanced → Cron Jobs, add:
```
* * * * * cd /home/u{id}/domains/vidya.monoloopproductions.in/app/laravel && php artisan schedule:run >> /dev/null 2>&1
```

---

## Step 11: Post-Deployment Checks

- [ ] Visit `https://vidya.monoloopproductions.in/login` — loads correctly
- [ ] Login with `kavish101 / password123` — admin panel opens
- [ ] Change admin password immediately
- [ ] Test each role login
- [ ] Change all default passwords
- [ ] Verify SSL padlock shows in browser

---

## Initial Login Credentials

> **Change all passwords immediately after first login.**

| Role | Username | Password |
|---|---|---|
| Admin | kavish101 | password123 |
| Sub-Admin | priya_sharma | password123 |
| Teacher | amit_gupta | password123 |
| Academic Head | meera_krishnan | password123 |
| Owner | sanjay_agarwal | password123 |
| Student | aarav_mehta | password123 |
| Reception | neha_verma | password123 |

---

## Updating the App Later

```bash
cd /home/u{id}/domains/vidya.monoloopproductions.in/app/laravel

git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Troubleshooting

**500 error after deploy:**
```bash
php artisan config:clear
php artisan cache:clear
chmod -R 755 storage
```

**Migrations fail:**
- Check DB credentials in `.env`
- Ensure DB user has CREATE TABLE privileges

**CSS/JS not loading:**
- Run `php artisan storage:link`
- Check `APP_URL` matches the actual domain exactly (with https://)

**Session issues:**
- Ensure `SESSION_DRIVER=database`
- Run `php artisan migrate` to create sessions table
