## Hostinger Deployment Guide (Laravel)

This project is a Laravel app. On Hostinger, the **document root must point to `public/`**.

### 1) Upload structure (recommended)

- **`laravel-app/`** (outside `public_html`):
  - Upload everything **except** the `public/` folder contents.
- **`public_html/`**:
  - Upload the contents of `public/` into `public_html/`

Then edit `public_html/index.php` to point to the correct paths:

- Change:
  - `require __DIR__.'/../vendor/autoload.php';`
  - `$app = require_once __DIR__.'/../bootstrap/app.php';`
- To (example if your app folder is `laravel-app` one level above `public_html`):
  - `require __DIR__.'/../laravel-app/vendor/autoload.php';`
  - `$app = require_once __DIR__.'/../laravel-app/bootstrap/app.php';`

If Hostinger lets you set the domain document root to `public/`, you can skip the index.php edits and just set docroot to:
- `<your-app-folder>/public`

### 2) Build frontend assets (Vite)

On your local machine (or Hostinger SSH terminal if available):

```bash
npm install
npm run build
```

Upload:
- `public/build/` (generated)

### 3) `.env` for production

Create `.env` on the server (do NOT upload it inside `public_html`).

Minimum recommended values:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

LOG_LEVEL=warning

DB_CONNECTION=mysql
DB_HOST=...
DB_PORT=3306
DB_DATABASE=...
DB_USERNAME=...
DB_PASSWORD=...
```

Also set mail settings (Hostinger SMTP) if you want emails to work.

### 4) Install PHP dependencies

If you have SSH:

```bash
composer install --no-dev --optimize-autoloader
php artisan key:generate
```

### 5) Storage + cache

```bash
php artisan storage:link
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 6) File permissions (common Hostinger fix)

Ensure writable:
- `storage/`
- `bootstrap/cache/`

### 7) Upload size / timeouts (important)

This repo’s `public/.htaccess` includes upload limits, but Hostinger often uses PHP-FPM, where `php_value` is ignored or can error.

Set these in **Hostinger → PHP Configuration** instead:
- `upload_max_filesize`
- `post_max_size`
- `max_execution_time`
- `memory_limit`

### 8) Queues / scheduler (optional but recommended)

If you use queues:
- Set `QUEUE_CONNECTION=database`
- Run a queue worker via cron/terminal if Hostinger supports it.

Scheduler (cron):

```bash
* * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1
```

