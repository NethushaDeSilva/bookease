# BookEase Security Documentation

## 1. Authentication

BookEase uses Laravel Jetstream and Laravel Fortify for user
registration, login, password reset, email verification, two-factor
authentication and browser-session management.

Passwords are hashed by Laravel and are never stored as plain text.

## 2. Authorization

BookEase contains three user roles:

- Customer
- Service provider
- Administrator

Role middleware protects role-specific routes. Laravel Policies enforce
record-level permissions and ownership rules for businesses, services,
bookings and reviews.

## 3. Account Status Protection

Suspended users are prevented from accessing protected web pages and API
endpoints. Web requests redirect suspended users to login, while API
requests return a JSON `403 Forbidden` response.

## 4. API Security

The REST API is protected by Laravel Sanctum. API endpoints require a
valid personal access token.

The application uses these token abilities:

- `services:read`
- `bookings:read`
- `bookings:create`
- `bookings:cancel`
- `provider:manage-bookings`

The API is versioned under `/api/v1`.

## 5. Input Validation

Livewire validation and Laravel Form Requests validate user input.
Validation includes required fields, data types, maximum lengths,
existing database IDs and permitted status values.

Laravel automatically returns `422 Unprocessable Entity` for invalid API
input.

## 6. Booking Security

Bookings are created inside database transactions. The selected slot is
locked with `lockForUpdate()` while capacity is checked.

This prevents:

- Booking inactive or past slots
- Booking a full slot
- Duplicate active bookings
- Race conditions involving the final available place

Booking status changes are protected by Laravel Policies.

## 7. Web Security

Laravel provides CSRF protection for web forms. Blade escapes output by
default, reducing cross-site scripting risks.

Authenticated routes also require active and email-verified accounts.

## 8. Rate Limiting

Versioned API endpoints use the `throttle:api` middleware. Each
authenticated user is limited to 60 API requests per minute.

Requests exceeding the limit receive `429 Too Many Requests`.

## 9. Activity Logging

Important actions such as booking creation, cancellation and status
changes are recorded in the `activity_logs` table.

Booking status changes are also recorded in
`booking_status_histories`.

## 10. Production Security

The production application must use:

- HTTPS
- `APP_ENV=production`
- `APP_DEBUG=false`
- A strong application key
- Secure database credentials
- Protected environment variables
- Restricted file permissions
- Regular database backups

The `.env` file, API tokens and passwords must never be committed to
GitHub.