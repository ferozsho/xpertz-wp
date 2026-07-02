# xpertz-wp

WordPress deployment scaffold for **Xpertz LMS**.

## Recommended Stack
- PHP **8.3+**
- MySQL **8.0+** or MariaDB **10.6+**
- WordPress **latest stable 6.x**

> Note: WordPress 7.0 is not a currently available stable release.

## Quick start (Docker)
```bash
cp .env.example .env
# edit .env values if needed

docker compose up -d
```

Then open:
- WordPress: http://localhost:9080
- phpMyAdmin: http://localhost:9081

## Database defaults
- Database: `xpertz-wp`
- Username: `superadmin`

## Local → Production Workflow

### 1. Folder structure
Create these folders in the repo root — they are tracked in Git:
```
themes/    ← put your custom themes here
plugins/   ← put your custom plugins here
uploads/   ← ignored by Git, managed on server
```

### 2. Local development
```bash
# Start local stack
docker compose up -d

# Edit files in ./themes/ or ./plugins/
# Changes are reflected instantly at http://localhost:9080

# Commit and push when ready
git add themes/ plugins/
git commit -m "feat: update theme"
git push origin main
```

### 3. Deploy to production (on your server)
```bash
cd /opt/xpertz-wp
git pull origin main
docker compose -f docker-compose.prod.yml restart wordpress
```

> **Note:** Change `/opt/xpertz-wp` to wherever you cloned the repo on your server.

## Security notes
- Rotate default credentials immediately.
- Never commit real production credentials.
- Keep `wp-config.php` out of version control in production.

## HTTPS deployment with Nginx reverse proxy
Domain configured: `xpertzwp.openxpertz.com`

1. Start production stack:
```bash
docker compose -f docker-compose.prod.yml up -d nginx wordpress db
```

2. Obtain Let's Encrypt certificate:
```bash
docker compose -f docker-compose.prod.yml run --rm certbot certonly \
  --webroot -w /var/www/certbot \
  -d xpertzwp.openxpertz.com \
  --email feroz.s@openxpertz.com --agree-tos --no-eff-email
```

3. Reload Nginx:
```bash
docker compose -f docker-compose.prod.yml restart nginx
```

4. Add renewal cron on host:
```cron
0 3 * * * cd /opt/xpertz-wp && docker compose -f docker-compose.prod.yml run --rm certbot renew && docker compose -f docker-compose.prod.yml restart nginx
```

5. In `wp-config.php`, ensure HTTPS is respected behind proxy:
```php
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
}
```

## Install reference
Based on WordPress advanced administration docs:
https://developer.wordpress.org/advanced-administration/before-install/howto-install/
