# BookEase viva: quick file map

Read the [learning guide](bookease-learning-guide.md) for explanations and the [filter code appendix](bookease-filter-code.md) for exact excerpts. Paths below describe the local source checked on 30 September 2026.

## Business pages

Click **UI** for appearance; click **PHP** for behavior. Browser routes are in [routes/web.php](../routes/web.php). `{service}`, `{slot}` and `{booking}` represent record identifiers.

| Page | URL | UI | PHP |
|---|---|---|---|
| Administrator dashboard | `/dashboard` | [UI](../resources/views/livewire/admin/dashboard.blade.php) | [PHP](../app/Livewire/Admin/Dashboard.php) |
| Administrator businesses | `/admin/businesses` | [UI](../resources/views/livewire/admin/business-manager.blade.php) | [PHP](../app/Livewire/Admin/BusinessManager.php) |
| Administrator users | `/admin/users` | [UI](../resources/views/livewire/admin/user-manager.blade.php) | [PHP](../app/Livewire/Admin/UserManager.php) |
| Administrator bookings | `/admin/bookings` | [UI](../resources/views/livewire/admin/booking-monitor.blade.php) | [PHP](../app/Livewire/Admin/BookingMonitor.php) |
| Customer catalogue | `/customer/services` | [UI](../resources/views/livewire/customer/service-catalog.blade.php) | [PHP](../app/Livewire/Customer/ServiceCatalog.php) |
| Service details | `/customer/services/{service}` | [UI](../resources/views/livewire/customer/service-details.blade.php) | [PHP](../app/Livewire/Customer/ServiceDetails.php) |
| Create booking | `/customer/services/{service}/book/{slot}` | [UI](../resources/views/livewire/customer/booking-creator.blade.php) | [PHP](../app/Livewire/Customer/BookingCreator.php) |
| Personal bookings | `/customer/bookings` | [UI](../resources/views/livewire/customer/booking-list.blade.php) | [PHP](../app/Livewire/Customer/BookingList.php) |
| Create review | `/customer/bookings/{booking}/review` | [UI](../resources/views/livewire/customer/review-creator.blade.php) | [PHP](../app/Livewire/Customer/ReviewCreator.php) |
| Provider business | `/provider/business` | [UI](../resources/views/livewire/provider/business-profile.blade.php) | [PHP](../app/Livewire/Provider/BusinessProfile.php) |
| Provider services | `/provider/services` | [UI](../resources/views/livewire/provider/service-manager.blade.php) | [PHP](../app/Livewire/Provider/ServiceManager.php) |
| Provider availability | `/provider/availability` | [UI](../resources/views/livewire/provider/availability-manager.blade.php) | [PHP](../app/Livewire/Provider/AvailabilityManager.php) |
| Provider bookings | `/provider/bookings` | [UI](../resources/views/livewire/provider/booking-manager.blade.php) | [PHP](../app/Livewire/Provider/BookingManager.php) |

The admin dashboard is embedded by [dashboard.blade.php](../resources/views/dashboard.blade.php). `/dashboard` redirects customers/providers to their relevant working pages. Providers can also access retained personal bookings.

## Authentication, profile and shared UI

| What to find | File | Backend starting point |
|---|---|---|
| Landing page | [welcome.blade.php](../resources/views/welcome.blade.php) | [web.php](../routes/web.php) |
| Navigation | [navigation-menu.blade.php](../resources/views/navigation-menu.blade.php) | [web.php](../routes/web.php) |
| Login | [auth/login.blade.php](../resources/views/auth/login.blade.php) | [FortifyServiceProvider.php](../app/Providers/FortifyServiceProvider.php) |
| Registration | [auth/register.blade.php](../resources/views/auth/register.blade.php) | [CreateNewUser.php](../app/Actions/Fortify/CreateNewUser.php) |
| Forgot password | [auth/forgot-password.blade.php](../resources/views/auth/forgot-password.blade.php) | [fortify.php](../config/fortify.php) |
| Reset password | [auth/reset-password.blade.php](../resources/views/auth/reset-password.blade.php) | [ResetUserPassword.php](../app/Actions/Fortify/ResetUserPassword.php) |
| Email verification | [auth/verify-email.blade.php](../resources/views/auth/verify-email.blade.php) | [fortify.php](../config/fortify.php) |
| Confirm password | [auth/confirm-password.blade.php](../resources/views/auth/confirm-password.blade.php) | [fortify.php](../config/fortify.php) |
| Two-factor login challenge | [auth/two-factor-challenge.blade.php](../resources/views/auth/two-factor-challenge.blade.php) | [fortify.php](../config/fortify.php) |
| Profile page assembly | [profile/show.blade.php](../resources/views/profile/show.blade.php) | [jetstream.php](../config/jetstream.php) |
| Account type | [profile/account-type.blade.php](../resources/views/profile/account-type.blade.php) | [BecomeProviderController.php](../app/Http/Controllers/BecomeProviderController.php) |
| Update profile | [profile/update-profile-information-form.blade.php](../resources/views/profile/update-profile-information-form.blade.php) | [UpdateUserProfileInformation.php](../app/Actions/Fortify/UpdateUserProfileInformation.php) |
| Update password | [profile/update-password-form.blade.php](../resources/views/profile/update-password-form.blade.php) | [UpdateUserPassword.php](../app/Actions/Fortify/UpdateUserPassword.php) |
| Two-factor settings | [profile/two-factor-authentication-form.blade.php](../resources/views/profile/two-factor-authentication-form.blade.php) | [TwoFactorAuthenticationForm.php](../vendor/laravel/jetstream/src/Http/Livewire/TwoFactorAuthenticationForm.php) |
| Other browser sessions | [profile/logout-other-browser-sessions-form.blade.php](../resources/views/profile/logout-other-browser-sessions-form.blade.php) | [LogoutOtherBrowserSessionsForm.php](../vendor/laravel/jetstream/src/Http/Livewire/LogoutOtherBrowserSessionsForm.php) |
| Delete account | [profile/delete-user-form.blade.php](../resources/views/profile/delete-user-form.blade.php) | [DeleteUser.php](../app/Actions/Jetstream/DeleteUser.php) |
| API token page | [api/index.blade.php](../resources/views/api/index.blade.php) | [JetstreamServiceProvider.php](../app/Providers/JetstreamServiceProvider.php) |
| API token controls | [api/api-token-manager.blade.php](../resources/views/api/api-token-manager.blade.php) | [JetstreamServiceProvider.php](../app/Providers/JetstreamServiceProvider.php) |
| Signed-in layout | [layouts/app.blade.php](../resources/views/layouts/app.blade.php) | [AppLayout.php](../app/View/Components/AppLayout.php) |
| Guest layout | [layouts/guest.blade.php](../resources/views/layouts/guest.blade.php) | [GuestLayout.php](../app/View/Components/GuestLayout.php) |

Some authentication/profile handlers are supplied by Fortify/Jetstream. A configuration/provider link is a starting point, not a claim that it contains every action. Package links are for reading, not editing. Reusable UI pieces live in [resources/views/components](../resources/views/components).

## Where to go for a lecturer's question

| Question | Open/search |
|---|---|
| How is this URL connected to PHP? | [web routes](../routes/web.php); for API use [API routes](../routes/api.php) |
| How does this filter work? | UI `wire:model` -> same PHP property -> `render()` -> model scope |
| Who may perform this action? | [policies](../app/Policies), [middleware](../app/Http/Middleware) |
| How are database tables related? | [models](../app/Models), then [migrations](../database/migrations) |
| What values can a status have? | [enums](../app/Enums) |
| Where is registration/account deletion? | [actions](../app/Actions) |
| Where is API validation? | [requests](../app/Http/Requests/Api) and API controller `validate()` calls |
| Where are JSON fields selected? | [resources](../app/Http/Resources) |
| What proves expected behavior? | [feature tests](../tests/Feature) |
| Where are installed PHP packages? | `vendor/`; dependencies listed in [composer.json](../composer.json) |
| Where is environment configuration? | `config/` plus private `.env`; avoid displaying secrets |

**Remember:** `resources/views` = UI templates; `app/Livewire` = interactive page behavior; `app/Models` = data relationships/queries. Use Ctrl+P to open a filename and Ctrl+Shift+F to trace a property/method. Do not rename source folders just to make demonstration navigation easier.
