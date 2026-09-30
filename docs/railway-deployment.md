# BookEase on Railway

This application uses Laravel 12, Livewire, Vite, and MySQL. Railway builds
the PHP application and frontend through Railpack. `railway.json` configures
the `/up` health check. `start-container.sh` runs migrations and caches the
application, without running the demonstration seeder.

## Services and configuration

Use a BookEase web service and a MySQL service with persistent storage.
Set these variables on the web service (replace placeholders privately in Railway):

```dotenv
APP_NAME=BookEase
APP_ENV=production
APP_DEBUG=false
APP_KEY=<preserve the local key when importing existing encrypted data>
APP_URL=https://<generated Railway domain>
LOG_CHANNEL=stderr
LOG_LEVEL=warning
DB_CONNECTION=mysql
DB_HOST=${{MySQL.MYSQLHOST}}
DB_PORT=${{MySQL.MYSQLPORT}}
DB_DATABASE=${{MySQL.MYSQLDATABASE}}
DB_USERNAME=${{MySQL.MYSQLUSER}}
DB_PASSWORD=${{MySQL.MYSQLPASSWORD}}
SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true
CACHE_STORE=database
QUEUE_CONNECTION=sync
```

The MySQL reference name must match the actual Railway service name.
Leave `SESSION_DOMAIN` unset for the generated hostname. Keep the application
key stable across redeployments. Do not commit `.env` files or database exports.

The current app has no custom queued jobs or scheduled tasks. `sync` avoids
requiring a separate worker. Add a worker if asynchronous jobs are introduced.
Profile uploads are currently disabled; if local uploads are enabled later,
attach durable storage or configure object storage before using them.

## Existing data

1. Back up the local database using a consistent MySQL dump.
2. Inspect the target database before importing. Never overwrite existing
   Railway data without reviewing it and taking a backup.
3. Transfer existing application records and preserve IDs and password hashes.
   Do not transfer active login sessions or password-reset tokens.
4. Preserve the local `APP_KEY` so existing two-factor secrets remain readable.
5. Compare per-table record counts after import, then run pending migrations.
6. Check demonstration accounts for known/default passwords before public use.

## Email

Railway Trial, Free, and Hobby block outbound SMTP. Configure an HTTPS email
provider with a verified sender and the appropriate Laravel transport package.
Do not treat `MAIL_MAILER=log` as functioning email delivery. Email verification
and password resets must be checked after the provider is configured.

## Release checks

- Build with `npm run build` and run the relevant Laravel tests.
- Verify `/up`, home, login, and static assets over the generated HTTPS URL.
- Sign in with an imported account and check role-specific pages.
- Verify email delivery, email verification links, and password resets.
- Verify the removed administrator Activity route remains unavailable.
- Keep MySQL backups and review Railway usage before trial credit expires.

References:
- https://railpack.com/languages/php/
- https://docs.railway.com/config-as-code/reference
- https://docs.railway.com/networking/outbound-networking
