# Vidya — Coaching Analytics SaaS

> Topic-level mastery tracking for NEET and IIT-JEE coaching institutes.
> Sister product to [Arya](https://github.com/kundansharma903-code/arya) (school analytics).

**Status: MVP COMPLETE — Production-ready**

---

## What It Is

Vidya sits alongside your existing coaching ERP (ScholarSERP, Proctur, Addmen). Upload your OMR results as Excel — Vidya adds subtopic-level academic mastery analytics your current software doesn't have.

Where competitors show "Physics: 65%", Vidya shows:
```
Physics > Mechanics > Kinematics > Motion in 1D: 42%
Physics > Mechanics > Kinematics > Projectile Motion: 78%
```

**Target:** Indian coaching institutes preparing students for NEET and IIT-JEE.

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 13.6, PHP 8.4 |
| Database | MySQL 8.0 |
| Frontend | Blade + Alpine.js + Chart.js 4.4 |
| Font | Inter |
| Hosting | Hostinger (subdomain) |

---

## Roles (7 total)

| Role | Accent | Primary Responsibility |
|---|---|---|
| **Admin** | Blue #7a95c8 | Full system setup and management |
| **Sub-Admin** | Blue #7a95c8 | Test creation, OMR upload, result analysis |
| **Teacher** | Blue #7a95c8 | View own subject heatmap and insights |
| **Academic Head** | Blue #7a95c8 | Cross-subject analytics, teacher effectiveness |
| **Owner** | Purple #a392c8 | Financial/ROI dashboard, staff decisions |
| **Student** | Blue #7a95c8 | Personal results, mastery journey, goals |
| **Reception** | Coral #c87064 | Walk-in student result lookup |

---

## Local Setup

### Prerequisites
- PHP 8.3+ with extensions: pdo_mysql, mbstring, openssl, tokenizer, xml, ctype, json
- MySQL 8.0+
- Composer 2+

### Steps
```bash
git clone https://github.com/kundansharma903-code/vidya.git
cd vidya/laravel

composer install

cp .env.example .env
php artisan key:generate
```

Edit `.env` with your DB credentials:
```env
DB_DATABASE=vidya
DB_USERNAME=root
DB_PASSWORD=your_password
```

```bash
php artisan migrate
php artisan db:seed

# Start server (artisan serve may fail on some Windows setups — use this instead)
php -S 127.0.0.1:8000 -t public
```

Visit: http://127.0.0.1:8000/login

---

## Test Credentials (after fresh seed)

| Role | Username | Password |
|---|---|---|
| Admin | kavish101 | password123 |
| Sub-Admin | priya_sharma | password123 |
| Teacher | amit_gupta | password123 |
| Academic Head | meera_krishnan | password123 |
| Owner | sanjay_agarwal | password123 |
| Student | aarav_mehta | password123 |
| Reception | neha_verma | password123 |

> Change all passwords immediately after first login on production.

---

## Environment Variables

| Variable | Description |
|---|---|
| `APP_KEY` | Generate with `php artisan key:generate` |
| `APP_URL` | Your domain (e.g. `https://vidya.monoloopproductions.in`) |
| `DB_HOST` | MySQL host |
| `DB_DATABASE` | Database name |
| `DB_USERNAME` | DB user |
| `DB_PASSWORD` | DB password |
| `SESSION_DRIVER` | Use `database` for production |
| `CACHE_STORE` | Use `database` for production |

See `.env.production.example` for full production template.

---

## Deployment (Hostinger)

See [DEPLOYMENT.md](./DEPLOYMENT.md) for full step-by-step guide.

Quick summary:
1. Create subdomain `vidya.monoloopproductions.in` in hPanel
2. Create MySQL DB in hPanel
3. SSH in → `git clone` the repo
4. `composer install --no-dev --optimize-autoloader`
5. Configure `.env` with real credentials
6. `php artisan migrate --force`
7. `php artisan db:seed --force`
8. Cache: `php artisan config:cache && php artisan route:cache && php artisan view:cache`
9. `php artisan storage:link`

---

## Repo Structure

```
vidya/
├── README.md
├── DEPLOYMENT.md          ← Hostinger deployment guide
├── DECISIONS.md           ← All locked architecture decisions
├── VIDYA_CONTEXT.md       ← Business concept and positioning
├── VIDYA_BLUEPRINT.md     ← Technical blueprint (25 tables, 48+ pages)
├── DESIGN_TOKENS.md       ← Colors, typography, spacing
├── FIGMA_INVENTORY.md     ← Designed screens with Figma node IDs
├── FIGMA_LINKS.md         ← Quick-access Figma links
├── .gitignore
└── laravel/               ← Full Laravel application
    ├── app/
    ├── database/
    ├── resources/views/
    ├── routes/web.php
    └── ...
```

---

## Figma

**File:** https://www.figma.com/design/lgUeyshjJH2kkFkQXxDZjH/Vidya-Designs
See [FIGMA_INVENTORY.md](./FIGMA_INVENTORY.md) for screen-by-screen map.

---

**Built by Kavish Sharma, Jaipur — 2026**
