# BookEase

BookEase is a SaaS-style service booking and scheduling application
developed with Laravel 12, Livewire, Jetstream, Tailwind CSS, MySQL and
Laravel Sanctum.

## Main Features

### Customers

- Browse and search services
- View available appointment slots
- Create and cancel bookings
- View booking history
- Publish reviews

### Service Providers

- Manage a business profile
- Manage services and availability
- View customer bookings
- Confirm, reject, complete and cancel bookings
- View provider statistics

### Administrators

- View system statistics
- Manage users and businesses
- Monitor bookings
- View activity logs

### REST API

- Versioned `/api/v1` endpoints
- Sanctum token authentication
- Token abilities
- API Resources and Form Requests
- Pagination and filtering
- Rate limiting

## Technologies

- PHP 8.2
- Laravel 12
- Laravel Livewire
- Laravel Jetstream
- Laravel Sanctum
- Tailwind CSS
- MySQL
- PHPUnit
- Vite

## Installation

```bash
git clone YOUR_REPOSITORY_URL
cd bookease
composer install
npm install
copy .env.example .env
php artisan key:generate