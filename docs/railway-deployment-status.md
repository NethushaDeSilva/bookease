# BookEase deployment verification — 29 September 2026

## Result

The hosted application is connected to Railway MySQL. The existing local
application data has **not** been imported. Hosting and database connectivity
are working, but the requested transfer of existing accounts and bookings is
unfinished.

## Deployment

- Website: https://bookease-production-870e.up.railway.app
- Railway project: `incredible-adventure`
- Project ID: `3ee4f021-2f68-4c9b-884d-392a8e2449f8`
- Environment: `production`
- Application service: `bookease`
- Database service: `MySQL`, with `mysql-volume`
- Latest verified deployment: `8bca0e57-b14b-4958-aa33-0d90f631877b`
- Deployment status: `SUCCESS`
- Current release was uploaded from the local checkout through Railway CLI.
  Local changes have not been committed/pushed by this deployment work.
  The GitHub source remains connected: commit/push the intended changes before
  relying on a future GitHub deployment to reproduce this release.

## Verified evidence

1. Privately compared the web service's database host, port, database name,
   username, and password with the Railway MySQL service: all matched.
   No credentials are included in this document.
2. `DB_CONNECTION=mysql` and `SESSION_DRIVER=database` are configured.
3. The initial deployment successfully ran the application migrations.
   The latest deployment reported `Nothing to migrate`, confirming it could
   access the existing migration table.
4. Fresh HTTPS requests to `/up` and `/login` returned HTTP 200.
   The login route uses database-backed sessions; this provides runtime
   database access evidence beyond a static landing page or health check.
5. Railway's database browser showed application tables, including `users`,
   `bookings`, `businesses`, `sessions`, and `migrations`.
6. Directly inspected `users` and `bookings` in that database browser:
   both displayed `This table is empty`.
7. Earlier deployment checks verified the home page, production CSS and JS,
   a Secure session cookie, and HTTP 404 for `/admin/activity-logs`.

## Data comparison

| Records | Local MySQL, checked this turn | Railway MySQL, checked this turn |
| --- | ---: | ---: |
| Users | 13 | 0 |
| Bookings | 10 | 0 |
| Businesses | 2 | Not counted |

These are separate databases; no automatic synchronization is configured.
Local accounts cannot sign in to the hosted application until imported.
Do not diagnose the empty hosted tables as a disconnected database.

## Configuration and checks completed

- Railway build and `/up` health-check configuration: `railway.json`.
- Startup migrations and Laravel caches: `start-container.sh`.
- HTTPS proxy handling on Railway: `bootstrap/app.php`.
- The existing local application key was transferred privately to preserve
  encrypted two-factor data when it is imported.
- Production debug output is disabled; secure session cookies are enabled.
- The administrator Activity page removal is included in the hosted release.
- Local production asset build passed.
- Login, email-verification, and Railway HTTPS proxy tests: 7 passed.

## Outstanding work and limits

- Existing data transfer remains pending. Automatic approval review rejected
  generating/registering a persistent SSH key because it adds account access.
  A specific user approval request was issued; no SSH key was created or
  registered by this work. Do not interpret general deployment authorization
  as approval to bypass that rejection.
- The last password check found one administrator, one provider, and one
  customer still using the demo seeder's shared password. The user was asked
  to change these locally; no completion confirmation has been received.
- Email setup was explicitly deferred. `MAIL_MAILER=log` records messages
  instead of delivering verification or password-reset emails. Registration
  requiring verification is therefore not a completed public-user workflow.
- No authenticated hosted booking workflow has been validated, since existing
  accounts have not been imported.
- No database backup schedule has been configured or verified by this work.
- Railway CLI warns that `railway.json` is deprecated and supported only until
  1 December 2026. Migrate the deployment configuration before that date.

## Next steps

After explicit SSH-key approval and resolving the demo-password issue:
back up the source database, recheck the target for newly created records,
import the application data without overwriting unrelated records, compare
table counts, test imported-account login and role workflows, then remove
the temporary key's Railway access if approved on that condition.

Operational setup instructions: [railway-deployment.md](railway-deployment.md).
