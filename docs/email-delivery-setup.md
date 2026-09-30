# BookEase email delivery setup

## Current state

Brevo HTTPS API support is implemented locally using the Symfony Brevo mailer
bridge. Composer validation passes. Six focused tests pass for transport
selection, missing credentials, email verification, and Railway HTTPS URLs.
No real email has been sent or delivery verified through Brevo yet.
The production mailer remains `log` until setup below is complete.

## Account and sender setup

1. Finish Brevo signup at https://onboarding.brevo.com/account/register.
   The account owner must accept terms and complete password/email checks.
2. Complete onboarding with accurate details and confirm transactional sending
   is enabled. Resolve any account review before attempting delivery.
3. Add a sender named BookEase and verify ownership of the sender address.
   Prefer an authenticated domain. Brevo documents temporary sender rewriting
   for free email addresses; confirm that this is supported for the account
   rather than assuming signup alone enables delivery.
4. Create an API key and enter it privately in Railway variables. Do not paste
   keys into chat or commit them to Git. An SMTP password is not an API key.

## Railway variables

After deploying the new integration code, configure:

```dotenv
MAIL_MAILER=brevo
BREVO_API_KEY=<private API key>
MAIL_FROM_ADDRESS=<verified sender address>
MAIL_FROM_NAME=BookEase
```

Keep the existing `APP_KEY`, database references, and HTTPS `APP_URL` intact.
Do not enable `brevo` before the key and sender are ready. Missing keys fail
explicitly instead of silently falling back to logging.

## End-to-end verification

Resend verification for the user's existing hosted account. Check the provider's
delivery result and the user's inbox, then follow the HTTPS verification link
while signed into the matching account. Verify the user can reach their normal
role page afterward. Provider acceptance alone is not proof of inbox delivery.
Do not disable verification or manually mark the account verified as a substitute.

Database migration is separate from email delivery. Recheck Railway tables
before any future data import: the user has since reached the hosted account's
verification page, so the earlier empty-users snapshot is no longer a safe
assumption for an import.

References:
- https://symfony.com/doc/7.4/mailer.html
- https://help.brevo.com/hc/en-us/articles/14925263522578-Comply-with-Gmail-Yahoo-and-Microsoft-s-requirements-for-email-senders
