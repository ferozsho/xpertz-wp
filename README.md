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
- WordPress: http://localhost:8080
- phpMyAdmin: http://localhost:8081

## Database defaults
- Database: `xpertz-wp`
- Username: `superadmin`

## Security notes
- Rotate default credentials immediately.
- Never commit real production credentials.
- Keep `wp-config.php` out of version control in production.

## Install reference
Based on WordPress advanced administration docs:
https://developer.wordpress.org/advanced-administration/before-install/howto-install/
