# BookEase: a beginner's guide to the code

Source checked: 30 September 2026. This describes the local checkout, not proof that the same version is deployed. Start with sections 1–4, then practise one filter at a time. Keep the [quick file map](bookease-file-map.md) open during your viva.

## 1. Your architecture explanation: what is right and what needs changing?

Your explanation is about **7/10**: you correctly connected the UI, application logic, models and database. The missing pieces are routes, middleware, and Livewire. Also, opening a page starts with a browser request; it does not require an existing view to run first.

**A view is the UI template.** Blade runs on the server and produces HTML. The browser displays the HTML, applies CSS, and runs JavaScript. A `.blade.php` file can contain PHP expressions, so “Blade is frontend and every other PHP file is backend logic” is a useful starting point, but not an exact classification. PHP files also define configuration, routes, tests and database structure.

For a traditional Laravel page:

```text
Browser requests a URL
  -> Laravel matches a route and runs middleware
  -> Controller handles the request
  -> Model/query asks the database for records
  <- Database returns records
  -> Controller supplies those records to a Blade view
  <- Browser receives the rendered response
```

The database does not select the page design. The model does not normally send a page directly to the browser. The controller coordinates the work, and the view describes its presentation. Not every request needs a database query or a view: a response can also be a redirect or JSON.

**BookEase mostly uses Livewire for its business pages:**

```text
GET /customer/services
  -> routes/web.php and access checks
  -> app/Livewire/Customer/ServiceCatalog.php
  -> Service model/query -> database
  -> resources/views/livewire/customer/service-catalog.blade.php
  -> HTML displayed in the browser

Customer types in Search
  -> Livewire JavaScript sends a background update
  -> PHP component's search property changes
  -> updatedSearch() resets pagination
  -> render() queries matching services
  -> Livewire updates the displayed results
```

Subsequent component interactions normally use `/livewire/update`. They do not need a new custom controller for each button. Livewire is not the database; it connects interactive Blade templates to server-side PHP component state and methods.

**Viva answer:** “The project uses Laravel's MVC foundations with Livewire components for most interactive pages. Routes and middleware control entry, components handle page interactions, Eloquent models query data, and Blade views render the interface. API requests use conventional controllers and JSON resources.”

## 2. Learn the folders before learning individual files

| Folder/file | What it means | Example task |
|---|---|---|
| `resources/views/` | Blade UI templates, layouts and reusable pieces | Change a heading or form layout |
| `app/Livewire/` | PHP logic and state for interactive pages | Change a search query or button action |
| `app/Models/` | Eloquent models: relationships, casts and reusable queries | Define which services customers may see |
| `app/Http/Controllers/` | Request handlers used by controller routes | Change an API response workflow |
| `app/Http/Requests/` | Dedicated API input validation/authorization | Validate a booking request |
| `app/Http/Resources/` | Shapes returned JSON data | Choose API booking fields; this is not a UI folder |
| `app/Http/Middleware/` | Access checks along the request path | Reject suspended users or the wrong role |
| `app/Policies/` | Permissions for actions on records | Can this provider confirm this booking? |
| `app/Actions/` | Focused operations used by authentication/account packages | Register or delete an account |
| `app/Enums/` | Named sets of allowed values | Pending, confirmed, completed booking statuses |
| `app/Providers/` | Connects framework services to application behavior | Register the account deletion action |
| `routes/web.php` | Application browser routes | Find the PHP class behind a page URL |
| `routes/api.php` | API routes | Find a JSON endpoint |
| `bootstrap/app.php` | Application startup, routing and middleware setup | Register `role` and `active` middleware aliases |
| `config/` | Application/framework settings | Authentication features, mail, session, database settings |
| `.env` | This environment's settings and secrets | Local database connection; do not show passwords in a viva |
| `database/migrations/` | Versioned database structure changes | Add a column or foreign key |
| `database/seeders/` | Code that inserts initial/demo data when run | Seed data is not the same thing as existing live data |
| `database/factories/` | Generates model data for tests | Create test users |
| `tests/` | Automated behavior checks | Verify registration and account deletion |
| `resources/css/`, `resources/js/` | Frontend source assets | Styling and browser behavior |
| `public/` | Web entry point and public assets | Compiled asset output is usually under `public/build` |
| `storage/` | Runtime files, compiled views, logs and caches | Diagnose an error |
| `vendor/` | Composer-installed PHP dependencies | Laravel, Jetstream, Fortify, Livewire implementation |
| `node_modules/` | Installed frontend dependencies | Build tooling; not page source |
| `composer.json`, `composer.lock` | PHP dependencies and exact resolved versions | Understand installed packages |
| `package.json`, `vite.config.js` | Frontend scripts/build setup | Understand how CSS/JS are built |

### The two Livewire folders

`app/Livewire/Customer/ServiceCatalog.php` is the **behavior**. `resources/views/livewire/customer/service-catalog.blade.php` is its **presentation**. The difference is their full paths and purpose, not merely capital L versus lowercase l.

Do not casually rename `livewire` to `UI`: view references and framework conventions depend on these paths. `resources/views` already means the UI template area, including auth/profile pages outside `livewire`. A file map gives you fast navigation without changing working code.

### Package code versus application-owned code

Installed implementation is mainly in `vendor`; application customization belongs in your source folders. However, an application-owned file may originally have been generated or published by a package. Its location does not establish who authored it.

For example, [profile/show.blade.php](../resources/views/profile/show.blade.php) contains `@livewire('profile.two-factor-authentication-form')`. Jetstream registers that component in its package service provider. Its package PHP class renders the application's published [two-factor template](../resources/views/profile/two-factor-authentication-form.blade.php). This is why not every profile template has a matching class in `app/Livewire`.

Read package files to understand behavior; do not customize `vendor` directly because dependency installation/update can replace it.

## 3. What controllers, components, models and views actually do

### Route: the address book

Open [routes/web.php](../routes/web.php). Search `ServiceCatalog::class`. That route connects `/customer/services` to the catalogue component. A route **name**, such as `customer.services.index`, is a stable label used by `route(...)` to generate its URL.

The protected business routes use authentication, active-account, verified-email and role checks. Knowing a URL is not permission to use it.

### Component: the page's server-side worker

Open [ServiceCatalog.php](../app/Livewire/Customer/ServiceCatalog.php):

- `public string $search = '';` stores the current search text. Empty string means no search term.
- `mount()` in components that have it handles initial setup. It is not the same as `render()`.
- `updatedSearch()` is a Livewire lifecycle method invoked when that property updates through Livewire.
- `render()` prepares the current data and selects the view.
- Action methods such as `save()` or `createReview()` perform work when the UI calls them.

### Model: the application's interface to stored records

Open [Service.php](../app/Models/Service.php). `Service::query()` starts an Eloquent query for services. Chained conditions build the query. Calls such as `get()`, `first()`, `count()` or `paginate()` execute queries and return results.

`business()` declares a relationship. `scopeSearch()` defines a reusable query fragment called as `->search(...)`. A model is not a copy of the entire database; individual model instances usually represent individual rows.

### View: display the supplied records

The component uses `view('livewire.customer.service-catalog', ['services' => ...])`. Dots map to folders; Laravel finds `resources/views/livewire/customer/service-catalog.blade.php`. The key `services` becomes `$services` in Blade.

Common Blade syntax:

| Syntax | Meaning |
|---|---|
| `{{ $service->name }}` | Output an escaped value |
| `@foreach (...)` | Repeat markup for each record |
| `@if (...)` | Display something conditionally |
| `@error('name')` | Display a validation error for that field |
| `@include('profile.account-type')` | Include another Blade template |
| `<x-app-layout>` | Use a reusable Blade layout component |
| `<livewire:admin.dashboard />` | Render a Livewire component |
| `wire:click="methodName"` | Call a component method |
| `wire:submit="save"` | Submit through a component action |
| `wire:model...="search"` | Bind a field to a component property |

### Conventional controllers still exist

[BecomeProviderController.php](../app/Http/Controllers/BecomeProviderController.php) handles a POST request from the account-type UI. Its `__invoke()` method is the controller's single entry point. It validates account eligibility, locks the user inside a transaction, changes customer to provider, records activity and redirects to the business profile. Repeating it for an existing provider does not repeat the transition.

The three API controllers are [ServiceController](../app/Http/Controllers/Api/ServiceController.php), [customer BookingController](../app/Http/Controllers/Api/BookingController.php), and [provider BookingController](../app/Http/Controllers/Api/Provider/BookingController.php). They return JSON through resource classes rather than rendering these Blade pages. The two BookingController classes have different namespaces and responsibilities.

## 4. System features and where their behavior lives

Use the [file map](bookease-file-map.md) for UI links paired with these classes.

| Area | Implemented behavior | Main source |
|---|---|---|
| Public | Landing page | `resources/views/welcome.blade.php` |
| Authentication | Customer/provider registration, login/logout, verification link, password reset | `app/Actions/Fortify`, `config/fortify.php`, `resources/views/auth` |
| Profile | Update identity/password, two-factor settings, other browser sessions, account deletion | `resources/views/profile`, Fortify/Jetstream actions |
| Account type | Customer becomes provider; existing personal bookings remain accessible | `BecomeProviderController.php`, `routes/web.php` |
| Customer catalogue | Search/sort services; optionally show only those with available slots | `Customer/ServiceCatalog.php` |
| Service detail | Business/service details, available slots, date selection, recent reviews | `Customer/ServiceDetails.php` |
| Booking creation | Select a slot, add notes, create pending booking/reference | `Customer/BookingCreator.php` |
| Personal bookings | Search/status filtering, eligible cancellation, link to review | `Customer/BookingList.php` |
| Reviews | One eligible review per completed booking; rating 1–5, optional comment up to 2,000 characters | `Customer/ReviewCreator.php`, `BookingPolicy.php` |
| Provider business | Create/update business profile | `Provider/BusinessProfile.php` |
| Provider services | Create/edit services, price/duration, active toggle, guarded deletion, search | `Provider/ServiceManager.php` |
| Provider schedule | Create/edit slots, capacity, active toggle, service/date filters, guarded deletion | `Provider/AvailabilityManager.php` |
| Provider bookings | Search/filter business bookings; confirm, reject, complete or cancel eligible bookings | `Provider/BookingManager.php` |
| Administrator dashboard | Platform metrics, recent bookings and recent activity | `Admin/Dashboard.php` |
| Administrator businesses | Search/filter; approve, suspend, reactivate businesses | `Admin/BusinessManager.php` |
| Administrator users | Search/filter customers/providers; suspend/reactivate them | `Admin/UserManager.php` |
| Administrator bookings | Platform booking monitoring and filters | `Admin/BookingMonitor.php` |
| API | Service/slot reads; personal booking create/read/cancel; provider booking status actions | `routes/api.php`, API controllers |
| API tokens | Token creation/deletion and selected abilities | `resources/views/api`, `JetstreamServiceProvider.php` |
| Audit history | Activity records and booking status histories | `ActivityLog.php`, `BookingStatusHistory.php`, action code |

The separate administrator Activity page has been removed. Logging and the dashboard's recent activity remain. Do not claim there is still a standalone Activity route.

Feature configuration matters: Jetstream API tokens/account deletion are enabled; teams, profile photos, and terms/privacy feature flags are commented out. A team-invitation template existing on disk does not make teams an enabled feature. Fortify has passkeys enabled in configuration, but the profile template inspected here does not include a passkey management section. Do not claim a complete demonstrated passkey UI solely from that flag. Similarly, a `reschedule` policy method is not proof of a rescheduling page.

## 5. Database relationships, slowly

| Model | Main table | How it connects |
|---|---|---|
| User | `users` | Provider owns a business; customer has bookings/reviews |
| Business | `businesses` | `owner_id` points to user; business has services |
| Service | `services` | `business_id` points to business; service has slots/bookings |
| AvailabilitySlot | `availability_slots` | `service_id` points to service; bookings reference its ID |
| Booking | `bookings` | `customer_id`, `service_id`, `slot_id` connect the reservation |
| Review | `reviews` | `booking_id`, `customer_id` connect the review |
| BookingStatusHistory | `booking_status_histories` | Records a booking transition and who changed it |
| ActivityLog | `activity_logs` | Records actor, action and target entity |

Example: User 12 owns Business 5. Business 5 offers Service 8. Slot 20 belongs to Service 8. Customer 30 books Slot 20. The booking connects Customer 30, Service 8 and Slot 20; it does not copy the entire business record.

`belongsTo` means “this row points to its parent.” `hasMany` means “other rows point to this row.” `hasOne` describes one associated record. Service reviews use `hasManyThrough`: reach reviews through service bookings.

A **foreign key** protects relationships. Physically deleting a parent can fail if related records still require it. The current [DeleteUser action](../app/Actions/Jetstream/DeleteUser.php) instead anonymizes and soft-deletes the account, revokes access, cancels pending/confirmed related bookings, and disables the deleted provider's business offerings. Historical related records remain. [User.php](../app/Models/User.php) uses `SoftDeletes`; the `deleted_at` migration supports it. This is account closure, not physical erasure of every historical row.

Local XAMPP and Railway are separate environments unless deliberately configured to use the same database. Deploying PHP files does not automatically copy local rows. Migrations create/change structure; they do not import existing bookings.

## 6. What are the “filter files”?

There is no separate universal filter folder. For a page filter, follow this chain:

1. **Blade:** the search box, dropdown or date input.
2. **Livewire PHP:** the property holding the selection and the query using it.
3. **Model scope:** reusable restrictions such as `search()` or `available()`.
4. **Database:** evaluates the query and returns matching records.

Keep these concepts separate:

- **Filter:** changes which records qualify, such as status = pending.
- **Sort:** changes their order, such as price ascending.
- **Pagination:** splits results into pages; it does not mean only that many exist.
- **Validation:** checks whether submitted values are acceptable.
- **Authorization:** checks whether the user may access/modify something.
- **Middleware:** checks requests along their route; it is not the catalogue search filter.

### Read one actual binding

The catalogue input contains `wire:model.live.debounce.400ms="search"`.

1. `wire:model` binds the input to `$search` in the component.
2. `.live` sends updates during interaction rather than waiting only for another action.
3. `.debounce.400ms` waits for a short pause in typing before sending the update.
4. `updatedSearch()` calls `$this->resetPage()`.
5. `render()` rebuilds the query with `->search($this->search)`.
6. `paginate(9)` gets the current page of results.
7. The Blade loop shows those results.

Why reset the page? If you were on page 5 and a search leaves only one page, remaining on page 5 could look like there are no matches.

`#[Url(as: 'q')]` keeps search state in the `q` URL query parameter. For example, `/customer/services?q=hair&sort=price_low`. This is not a separate route. Only properties marked for URL synchronization should be assumed to persist this way.

### Query vocabulary used by all the filters

| Code | Read it as |
|---|---|
| `where('status', $value)` | Keep records whose status equals this value |
| `when($condition, fn (...) => ...)` | Add this restriction only if the condition is true |
| `whereDate('starts_at', $date)` | Compare the date part of appointment start time |
| `whereHas('service', ...)` | Keep records whose related service matches |
| `orWhere(...)` | An alternative match inside the current query group |
| `with('service')` | Load related service data for display; this alone does not filter bookings |
| `withCount('reviews')` | Add a calculated review count |
| `withAvg('reviews', 'rating')` | Add a calculated average rating |
| `orderBy(...)` / `orderByDesc(...)` | Ascending / descending order |
| `latest()` | Newest `created_at` first by default; not necessarily soonest appointment |
| `paginate(10)` | Return one page of up to ten records plus pagination information |
| `whereRaw('1 = 0')` | Intentionally return no records |

Search uses SQL `LIKE` with `%term%`: a term can appear anywhere in the searched value. It is not AI, fuzzy matching or a full-text search engine. Case sensitivity depends on database collation. `%` and `_` can act as SQL LIKE wildcards; parameter binding is not the same as escaping those wildcard meanings.

**AND versus OR is crucial.** Personal bookings mean `owned by me AND (reference matches OR service matches OR business matches) AND selected status`. The nested `where(function (...) {...})` groups alternatives so an OR does not accidentally include somebody else's booking.

## 7. Every interactive page filter

The [source excerpt appendix](bookease-filter-code.md) contains the actual current `render()` methods, property declarations and model scopes. Use it alongside these explanations; it avoids pretending that shortened teaching examples are the complete implementation.

### 7.1 Customer service catalogue

Files: [UI](../resources/views/livewire/customer/service-catalog.blade.php), [PHP](../app/Livewire/Customer/ServiceCatalog.php), [shared Service queries](../app/Models/Service.php).

**Search:** `$search` calls `Service::scopeSearch`. A match in service name OR service description OR business name qualifies. It does not search business address.

**Only available:** `$onlyAvailable` adds a `whereHas('availabilitySlots', ...)` requiring at least one available slot. Available means active, starts in the future, and pending/confirmed booking count is below capacity. A capacity-3 slot with two active bookings qualifies; one with three does not. Cancelled/rejected/completed bookings do not count toward this particular active count.

**Sort:** `name` sorts alphabetically; `price_low` ascending price; `price_high` descending price; `rating` descending average review rating; `newest` descending creation time. Price/rating sorts use name as a secondary order. Sorting by rating is not a minimum-rating filter.

**Always applied:** service active, business active, owner active and normal soft-delete restrictions. Clearing visible filters never overrides these conditions. There are nine services per page. Reset restores empty search, name sort and unchecked only-available.

Try: search a real word in a service name, select low-to-high price, then enable only-available. Results must satisfy the search and availability together; price only determines ordering.

### 7.2 Customer service detail: appointment date

Files: [UI](../resources/views/livewire/customer/service-details.blade.php), [PHP](../app/Livewire/Customer/ServiceDetails.php).

`$dateFilter` adds `whereDate('starts_at', $this->dateFilter)` to this service's already-available slots. It filters appointment start date, not booking creation date. Results are ordered by start time and paginated ten at a time using the named paginator `slotsPage`. Updating/clearing the date resets that paginator. The component validates date changes. Recent reviews are a separate latest-six query; the slot date does not filter those reviews.

Try: select a date with a known slot, then another date. Clear the date to return to all qualifying future slots for that service.

### 7.3 Customer My bookings

Files: [UI](../resources/views/livewire/customer/booking-list.blade.php), [PHP](../app/Livewire/Customer/BookingList.php).

`forCustomer($user)` first limits bookings to the logged-in person's customer ID. Search matches reference OR service name OR business name. Status is one of pending, confirmed, completed, cancelled, rejected; empty status adds no status restriction. Search/status combine with AND. Results are newest-created first, ten per page. Search/status updates reset pagination; Reset clears both. URL names are `q` and `status`.

`cancellationReason` is a form field for an action, not a list filter. Converted providers can still access this personal-booking page; it is separate from their business's received bookings.

### 7.4 Provider services

Files: [UI](../resources/views/livewire/provider/service-manager.blade.php), [PHP](../app/Livewire/Provider/ServiceManager.php).

Search uses the same `Service::scopeSearch`, but inside the current business restriction. It matches service name, description or business name. Consequently searching the provider's own business name can match all that business's services. No business means an intentionally empty query. Results are newest-created first, ten per page. Search changes reset pagination. Inactive services can appear here so the provider can manage them.

`isActive`, `name`, `price` and `durationMinutes` in the editor are fields being saved, not extra list filters.

### 7.5 Provider availability

Files: [UI](../resources/views/livewire/provider/availability-manager.blade.php), [PHP](../app/Livewire/Provider/AvailabilityManager.php).

First, slots must belong to services in the provider's business. `$filterServiceId` optionally restricts the service ID; `$dateFilter` optionally restricts appointment start date. Both combine with AND. Results are ordered by start time, ten per page; either change resets pagination. This management list does not automatically apply the customer's future/active/capacity restrictions.

Do not confuse `$filterServiceId` (which rows to display) with `$serviceId` (which service a slot being created/edited belongs to). The dropdown only offers the provider's services, and the base query still limits ownership. The date update hook here resets pagination without the explicit date validation used on some other pages; this guide records that implementation difference rather than claiming every date field behaves identically.

### 7.6 Provider bookings

Files: [UI](../resources/views/livewire/provider/booking-manager.blade.php), [PHP](../app/Livewire/Provider/BookingManager.php).

The base `providerBookingsQuery($user)` uses the related business's `owner_id`. Search matches reference OR customer name/email OR service name. Status restricts to an allowed booking status. Date restricts the related slot's `starts_at` date. All selected filters combine with AND, inside the ownership boundary. Results are newest-created first, ten per page. Search, status and date changes reset pagination; Reset clears all three. Search/status use URL state; date has no `#[Url]` attribute here.

Example: search `Nimal`, status pending, date 2026-10-05 means pending appointments on that date whose customer/reference/service text matches Nimal, served by this provider's business.

### 7.7 Administrator businesses

Files: [UI](../resources/views/livewire/admin/business-manager.blade.php), [PHP](../app/Livewire/Admin/BusinessManager.php).

Search matches business name/email/phone OR owner name/email. Status allows pending, active or suspended. Search/status changes reset pagination; Reset clears them. URL names are `q` and `status`. Ordering prioritizes pending, then active, then suspended businesses, followed by newest creation time within the group. Ten records per page.

The status summary cards are separate overall queries, not counts of only the currently searched rows. A search returning two rows does not mean the global pending card must become two.

### 7.8 Administrator users

Files: [UI](../resources/views/livewire/admin/user-manager.blade.php), [PHP](../app/Livewire/Admin/UserManager.php).

**The base query includes customer and provider roles only.** Administrators are not listed by this manager, even with “All roles.” Search matches name OR email. Role chooses customer/provider; status chooses active/suspended. Every selected filter combines with AND. Newest-created first, ten per page. Updates reset pagination, and Reset clears all three. URL names: `q`, `role`, `status`.

Role/status values are checked against allowed values before being applied. An invalid role does not grant extra access: the base customer/provider restriction still exists. Metrics describe that overall manageable population, rather than the current search alone. Normally soft-deleted users are excluded by Eloquent.

### 7.9 Administrator booking monitor

Files: [UI](../resources/views/livewire/admin/booking-monitor.blade.php), [PHP](../app/Livewire/Admin/BookingMonitor.php).

- Search: reference OR customer name/email OR service name OR business name OR business owner name/email.
- Status: an allowed booking status.
- Business: a numeric business ID, checked through the service relationship.
- Exact date: related slot's start date.
- Upcoming: slot starts after now.
- Past: slot ends before now.

An appointment currently in progress is neither “upcoming” nor “past” under those definitions. Period is a time filter, not a booking status: a future cancelled appointment can still match upcoming if status is unrestricted. Exact date and period both apply when selected; contradictory selections can correctly return nothing.

Results are newest-created first, fifteen per page. Filter changes reset pagination; Reset clears selections. URL state covers `q`, `status`, `business`, `period`; exact date is a separate component property. Dashboard-style totals/status counts use separate queries and do not necessarily shrink with the table filters. Completed booking value is a sum of stored booking prices, not proof of payment collection.

### 7.10 Pages without a result-list filter

Booking creation, review creation and business profile use form fields for data entry. Selecting review stars changes the submitted rating; it does not filter existing reviews. Profile/auth pages likewise have account form fields. The administrator dashboard presents summaries rather than these interactive list filters. A link that opens a filtered manager is navigation to that manager, not another filtering engine.

## 8. API filters: same concepts, different entry point

Read [routes/api.php](../routes/api.php). These routes are under `/api/v1`, protected by authentication, active/verified checks, throttling and endpoint-specific token abilities. An API client passes query parameters; it does not use `wire:model`.

| GET endpoint | Supported query parameters | Behavior |
|---|---|---|
| `/api/v1/services` | `search`, `sort`, `only_available`, `per_page` | Customer-visible services; same search/sort concepts as catalogue; default 15 per page |
| `/api/v1/services/{service}/slots` | `date`, `per_page` | Available slots for a visible service; date cannot be before today; earliest start first; default 30 |
| `/api/v1/bookings` | `status`, `per_page` | Logged-in customer's personal bookings, including retained personal bookings for converted providers; newest first; default 15 |
| `/api/v1/provider/bookings` | `search`, `status`, `date`, `per_page` | Provider-owned bookings; reference/customer/service search; newest first; default 15 |

API page size is validated between 1 and 50. Service/provider search is limited to 100 characters. Sort/status/date values are validated in the controllers. Personal-bookings API does not implement the UI's text search parameter. Do not assume identical UI/API options merely because both list bookings.

Example query: `/api/v1/services?search=hair&sort=price_low&only_available=1`. It still needs authorized access. `withQueryString()` keeps filter parameters in generated pagination links. Resource classes convert resulting models to JSON fields; they do not render Blade.

## 9. Follow a booking from button to database

1. Provider creates a business profile. Administrator approval/active business status is part of customer visibility.
2. Provider creates an active service with duration and price.
3. Provider creates future availability with capacity. End time is calculated from start time plus service duration. The schedule code checks overlaps across the business's slots, not merely within one service.
4. Customer browses visible services and selects an available slot.
5. `BookingCreator` validates input and checks access. It rechecks availability when submitting, because a slot visible a minute ago can become full.
6. Within a database transaction it locks the slot, checks active booking count and duplicate active bookings for that customer/slot, and creates a pending booking.
7. Booking price is stored on the booking. History/activity records document the operation.
8. Provider may confirm/reject a pending booking, complete a confirmed booking, or cancel a pending/confirmed booking according to the policy.
9. Customer cancellation requires an eligible active booking more than 24 hours before the appointment start, as defined by `Booking::canBeCancelledByCustomer()`.
10. After completion, the owner of the booking may submit a review if none exists. A converted provider may review their own retained personal booking too.

**Transaction:** related database writes succeed together or roll back on failure. **Row lock:** helps serialize conflicting operations on the selected record. Neither term means “the whole website becomes locked.” See [BookingCreator](../app/Livewire/Customer/BookingCreator.php), [BookingPolicy](../app/Policies/BookingPolicy.php), [Booking](../app/Models/Booking.php), and [ReviewCreator](../app/Livewire/Customer/ReviewCreator.php).

Service/slot deletion has guards when bookings exist. Account deletion follows its separate anonymization/soft-deletion workflow. Do not assume every Delete button uses the same database operation.

## 10. Security and supporting features in plain language

**Authentication:** who are you? **Authorization:** may you do this? [EnsureUserHasRole](../app/Http/Middleware/EnsureUserHasRole.php) and [EnsureUserIsActive](../app/Http/Middleware/EnsureUserIsActive.php) help enforce route access. Policies check record/action access. Hiding a button is not enough; the server checks again.

**Validation:** registration only accepts customer/provider roles; it does not accept self-registering as admin. `CreateNewUser` checks input and hashes the password. Other forms have their own rules. A browser dropdown is not a substitute for server-side checking.

**CSRF:** protects browser form/update requests against unwanted cross-site submissions. A 419 relates to request/session/token handling; it is not proof that an email address is invalid. This guide is not diagnosing any current 419 without inspecting that request.

**Email verification versus two-factor authentication:** verification confirms ownership using an emailed link. Two-factor login is a separate feature with authenticator/recovery-code handling. See the different templates under `auth` and `profile`.

**Mail integration:** [AppServiceProvider](../app/Providers/AppServiceProvider.php), [mail.php](../config/mail.php), and [services.php](../config/services.php) connect the configured mailer, including Brevo. Mail is an external delivery concern; a working database does not prove email delivery. See [email setup](email-delivery-setup.md).

**Tests:** `tests/Feature/AuthenticationTest.php`, `AccountRoleTest.php`, `RoleSystemIntegrityTest.php`, `DeleteAccountTest.php`, and `tests/Feature/Api/` provide examples of expected behavior. Test source is evidence of intended checks; saying tests passed requires actually running them. No new runtime test result is asserted by this document.

## 11. Practise finding files in under a minute

1. Identify the page and role: for example, provider bookings.
2. Open the [quick map](bookease-file-map.md), click its Blade file: this is the UI.
3. Click its PHP component: this contains properties, actions and `render()`.
4. For a filter, find `wire:model` in Blade and search that property in PHP.
5. Follow model scope calls such as `search()` into the corresponding `scopeSearch()` method.
6. For access questions, open `routes/web.php` and the relevant policy/middleware.
7. For schema questions, open the model and the matching migration.

VS Code: **Ctrl+P** finds files by name/path; **Ctrl+Shift+F** searches source text. Type `service-catalog.blade.php` for UI, `ServiceCatalog.php` for behavior. Search `wire:model` to find input bindings; `render` to find the list query; `scopeSearch` to find the reusable search definition. Do not begin by browsing `vendor` for your business pages.

### Short answers to rehearse

**“Show the UI for service search.”** Open `resources/views/livewire/customer/service-catalog.blade.php`; point to the input's `wire:model.live.debounce.400ms="search"`.

**“Where does that search run?”** “The Livewire property is used in `ServiceCatalog::render()`, which calls the Service model's `scopeSearch`. The database evaluates the resulting query.”

**“Why can't a provider see everybody's bookings?”** “The base query restricts bookings through `service.business.owner_id` to the authenticated provider, independently of the optional filters.”

**“Why is there no controller for this page?”** “This route uses a full-page Livewire component. Its PHP class handles state/actions and renders Blade. Conventional controllers handle the API and account-role conversion.”

**“Why are some totals unchanged after searching?”** “Those cards use separate overall summary queries; the search applies to the results table.”

**“Why does an existing service not appear for a customer?”** “Check the environment/database, service activity, business status, owner status, deletion state, and selected filters. Only-available additionally requires a future active slot with remaining capacity.”

### A manageable learning order

First session: learn the folder table and find three UI/PHP pairs. Second: explain catalogue search using the seven binding steps. Third: practise provider/date/status filters. Fourth: follow one booking and its relationships. Fifth: rehearse the short answers while opening the actual files. Understanding one complete path is more useful than memorizing filenames without their connections.
