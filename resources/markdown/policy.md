# Privacy Policy

*Last updated: 26 September 2026*

This policy explains what BookEase stores about you. BookEase is an
academic project built for an Advanced Programming assignment; it is a
working demonstration system, not a commercial service, and this policy
does not claim compliance with GDPR, CCPA, or any other specific
data-protection law.

## 1. What we collect

- **Account details**: name, email address, and a hashed (never
  plaintext) password. If you enable two-factor authentication we also
  store your two-factor secret and recovery codes.
- **Role and business data**: your account role (customer, provider or
  administrator), and, if you operate a business, its name, description,
  contact details and address.
- **Bookings and reviews**: the services, availability slots, bookings,
  cancellation reasons and reviews associated with your account.
- **Activity logs**: an audit trail of significant actions (for example,
  becoming a provider, or a booking status change) that records the
  action, the acting user, an IP address and browser user-agent string,
  and a timestamp.
- **API tokens**: if you create a personal access token, we store the
  token's name and permitted abilities (not the token itself, which is
  only shown to you once).

## 2. How this data is used

Collected data is used only to operate BookEase: authenticating you,
showing you the correct workspace and data for your role, processing
bookings, and giving administrators the audit trail needed to moderate
the platform. It is not sold, shared with advertisers, or used for
anything beyond running the application.

## 3. Who can see your data

- Customers can see their own bookings, reviews and profile.
- Providers can see bookings, customer names and contact details only
  for appointments made with their own business.
- Administrators can see platform-wide account, business, booking and
  activity-log data in order to moderate the platform.

## 4. Retention

Account, booking and activity-log data is retained for as long as the
account exists. Because this is a student project rather than a hosted
production service, data may also be reset or deleted at any time during
development, testing, or when the assignment concludes.

## 5. Demonstration data

Seeded demonstration accounts, businesses and bookings created for
evaluation purposes are not real people's data and exist only to make
the system demonstrable.

## 6. Security

Passwords are hashed, sessions are protected by Laravel's built-in CSRF
and authentication safeguards, and API access requires a bearer token
scoped to specific abilities. No system is perfectly secure, and this
project has not undergone a formal third-party security audit.

## 7. Contact

Questions about this policy, or requests to review or remove your data
from this project, can be directed to the project maintainer listed in
the project's repository/documentation.
