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
0 3 * * * cd /path/to/xpertz-wp && docker compose -f docker-compose.prod.yml run --rm certbot renew && docker compose -f docker-compose.prod.yml restart nginx
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
