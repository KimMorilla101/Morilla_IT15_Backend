# Morilla IT15 Enrollment Backend

Laravel 12 REST API backend for enrollment, students, courses, school days, and dashboard analytics.

## Tech Stack

- PHP 8+
- Laravel 12
- MySQL / MariaDB
- Laravel Sanctum (token auth)

## Prerequisites

- PHP and Composer installed
- MySQL running

## Backend Setup

```bash
cd laravel-backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

If your local backend folder is named differently (for example, `Morilla_IT15_Backend`), use that folder name instead of `laravel-backend`.

## Default Seeded Login

- Email: admin@morilla.test
- Password: password123

## API Base URL

- Local artisan server: http://127.0.0.1:8000/api
- Apache/XAMPP setup (if configured): https://localhost/morilla_backend/api

## API Documentation

See [API_DOCUMENTATION.md](API_DOCUMENTATION.md).

## Useful Commands

```bash
php artisan route:list --path=api
php artisan test
```
