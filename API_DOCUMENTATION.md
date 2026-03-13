# API Documentation

## Overview

This backend provides enrollment-related REST APIs for authentication, dashboard analytics, students, courses, and school days.

- Base path (artisan serve): http://127.0.0.1:8000/api
- Base path (Apache/XAMPP setup): https://localhost/morilla_backend/api

## Security and Headers

All API requests should send:

- Accept: application/json
- Content-Type: application/json

Additional security behavior:

- HTTPS enforcement: if FORCE_HTTPS=true, non-HTTPS requests return HTTP 426.
- Frontend API key: if FRONTEND_API_KEY is set, include header X-API-KEY with the exact value.
- Sanctum token auth: protected routes require Authorization: Bearer <token>.

## Authentication Flow

1. Call POST /api/login with email and password.
2. Save the returned bearer token.
3. Include Authorization: Bearer <token> for all protected endpoints.
4. Call POST /api/logout to revoke active tokens.

## Input Sanitization

All request strings are trimmed. Empty strings become null before validation.

## Public Endpoints

### GET /api/ping

Health check endpoint.

Response 200:

{
  "status": "ok",
  "message": "API is running."
}

### GET /api/login

Instruction endpoint only.

Response 405:

{
  "message": "Use POST /api/login with {\"email\",\"password\"} to authenticate."
}

### POST /api/login

Login and generate Sanctum token.

Request body:

{
  "email": "admin@morilla.test",
  "password": "password123"
}

Validation:

- email: required, valid email, max 255
- password: required, string, max 255

Response 200:

{
  "message": "Login successful.",
  "token": "1|...",
  "token_type": "Bearer",
  "user": {
    "id": 1,
    "name": "School Admin",
    "email": "admin@morilla.test"
  },
  "profile": {
    "id": 1,
    "name": "School Admin",
    "email": "admin@morilla.test",
    "role": "School Admin",
    "avatar": {
      "initials": "SA",
      "background_color": "#A0000F",
      "text_color": "#FFFFFF"
    },
    "sidebar_card": {
      "title": "School Admin",
      "subtitle": "admin@morilla.test"
    }
  }
}

Error 401:

{
  "message": "Invalid credentials."
}

## Protected Endpoints

Protected routes require:

- Authorization: Bearer <token>
- X-API-KEY header if FRONTEND_API_KEY is configured

### GET /api/profile

Returns authenticated user profile object.

### POST /api/logout

Revokes user tokens and logs out current session.

Response 200:

{
  "message": "Logout successful."
}

### GET /api/dashboard

Returns dashboard analytics payload:

- overview
- students_by_gender
- students_by_department
- students_by_year_level
- courses_by_department
- top_courses
- school_days_by_type
- monthly_attendance
- recent_calendar

## Students API

Resource routes:

- GET /api/students
- POST /api/students
- GET /api/students/{student}
- PUT/PATCH /api/students/{student}
- DELETE /api/students/{student}

### GET /api/students Query Parameters

- search: string, max 100
- department: string, max 100
- year_level: integer, 1-6
- course_id: integer, must exist in courses.id
- per_page: integer, 1-100

Response is Laravel paginator format with data, links, and meta fields.

### Student Payload (POST and PUT/PATCH)

Fields:

- student_number: required on POST, unique, string max 20
- first_name: required on POST, string max 100
- last_name: required on POST, string max 100
- email: required on POST, unique, valid email, max 255
- gender: required on POST, one of male, female, non-binary, prefer_not_to_say
- date_of_birth: required on POST, date, before today
- department: required on POST, string max 100
- year_level: required on POST, integer 1-6
- phone_number: nullable, string max 30
- address: nullable, string max 255
- status: required on POST, one of active, inactive, graduated, leave_of_absence
- course_ids: nullable array of existing course IDs

POST response 201:

{
  "message": "Student created successfully.",
  "student": { ... }
}

PUT/PATCH response 200:

{
  "message": "Student updated successfully.",
  "student": { ... }
}

DELETE response 200:

{
  "message": "Student deleted successfully."
}

## Courses API

Resource routes:

- GET /api/courses
- POST /api/courses
- GET /api/courses/{course}
- PUT/PATCH /api/courses/{course}
- DELETE /api/courses/{course}

### GET /api/courses Query Parameters

- search: string, max 100
- department: string, max 100
- semester: one of first, second, summer
- active: boolean
- per_page: integer, 1-100

### Course Payload (POST and PUT/PATCH)

Fields:

- code: required on POST, unique, string max 20
- title: required on POST, string max 150
- department: required on POST, string max 100
- description: nullable, string max 1000
- credits: required on POST, integer 1-6
- semester: required on POST, one of first, second, summer
- capacity: required on POST, integer 10-500
- is_active: required on POST, boolean

POST response 201:

{
  "message": "Course created successfully.",
  "course": { ... }
}

PUT/PATCH response 200:

{
  "message": "Course updated successfully.",
  "course": { ... }
}

DELETE response 200:

{
  "message": "Course deleted successfully."
}

## School Days API

Resource routes:

- GET /api/school-days
- POST /api/school-days
- GET /api/school-days/{school_day}
- PUT/PATCH /api/school-days/{school_day}
- DELETE /api/school-days/{school_day}

### GET /api/school-days Query Parameters

- search: string, max 100
- type: one of class, holiday, event
- month: format YYYY-MM
- is_school_open: boolean
- per_page: integer, 1-100

### School Day Payload (POST and PUT/PATCH)

Fields:

- date: required on POST, unique date in school_days
- title: required on POST, string max 150
- type: required on POST, one of class, holiday, event
- description: nullable, string max 1000
- attendance_rate: nullable numeric 0-100
- is_school_open: required on POST, boolean

Holiday normalization rule:

- If type is holiday, backend forces attendance_rate=null and is_school_open=false.

POST response 201:

{
  "message": "School day created successfully.",
  "school_day": { ... }
}

PUT/PATCH response 200:

{
  "message": "School day updated successfully.",
  "school_day": { ... }
}

DELETE response 200:

{
  "message": "School day deleted successfully."
}

## Common Error Responses

401 Unauthorized:

{
  "message": "Unauthenticated."
}

401 Invalid API key:

{
  "message": "The provided API key is invalid."
}

422 Validation error:

{
  "message": "The given data was invalid.",
  "errors": {
    "field_name": [
      "Validation message"
    ]
  }
}

426 HTTPS required (when FORCE_HTTPS=true):

{
  "message": "HTTPS is required for all API requests."
}
