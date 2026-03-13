# Enrollment System Backend API

Laravel backend API for student enrollment, courses, school-day records, and dashboard analytics.

## Documentation

- API reference: [API_DOCUMENTATION.md](API_DOCUMENTATION.md)

## Tech Stack

- PHP 8+
- Laravel 12
- MySQL / MariaDB
- Laravel Sanctum (token authentication)

## Backend Setup Instructions

### 1. Go to backend folder

```bash
cd Morilla_IT15_Backend
```

### 2. Install dependencies

```bash
composer install
```

### 3. Create environment file

```bash
cp .env.example .env
```

PowerShell alternative:

```powershell
Copy-Item .env.example .env
```

### 4. Configure `.env`

Set your local database values:

```env
APP_NAME=Enrollment_Backend
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_db_username
DB_PASSWORD=your_db_password
```

Optional HTTPS enforcement:

```env
FORCE_HTTPS=true
```

### 5. Generate app key

```bash
php artisan key:generate
```

### 6. Run migrations and seeders

```bash
php artisan migrate --seed
```

This seeds core data including courses, students, school days, and an admin account.

### 7. Start backend server

```bash
php artisan serve
```

Default local API base URL:

- http://127.0.0.1:8000/api

## Default Seeded Admin Login

- Email: admin@morilla.test
- Password: password123

## Optional HTTPS Local Setup (Apache/XAMPP)

If you run through Apache instead of `php artisan serve`, use the backend `public` folder as document root.

Recommended `.env` values:

```env
APP_URL=https://localhost/morilla_backend
FORCE_HTTPS=true
```

Clear cached config after changing `.env`:

```bash
php artisan optimize:clear
```

Expected behavior when HTTPS is enabled:

- HTTPS requests work normally.
- HTTP requests to protected API group return HTTP 426 with message `HTTPS is required for all API requests.`

## API Quick Start

### 1. Login

POST `/api/login`

```json
{
	"email": "admin@morilla.test",
	"password": "password123"
}
```

### 2. Use token in protected requests

```http
Authorization: Bearer <token>
Accept: application/json
```

### 3. Main endpoints

- `/api/dashboard`
- `/api/profile`
- `/api/students`
- `/api/courses`
- `/api/school-days`

## Useful Commands

```bash
php artisan route:list --path=api
php artisan test
php artisan optimize:clear
```
