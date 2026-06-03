# Booking System API

A Laravel-based Appointment Booking System that allows patients to book, cancel, and reschedule appointments while ensuring slot consistency and preventing concurrent booking conflicts.

## Features

* Laravel Sanctum Authentication
* Doctor Availability Management
* Appointment Booking
* Appointment Cancellation
* Appointment Rescheduling
* Event-Driven Architecture
* Notification System using Events & Listeners
* Database Transactions
* Row-Level Locking (`lockForUpdate`)
* Simulated Email Notifications
* Queue Support for Background Processing

---

## Technology Stack

* PHP 8.x
* Laravel 12
* MySQL
* Laravel Sanctum
* Laravel Events & Listeners
* Laravel Notifications
* Laravel Queue System

---

## Installation

### Clone Repository

```bash
git clone <repository-url>
cd booking-system
```

### Install Dependencies

```bash
composer install
```

### Environment Setup

Create environment file:

```bash
cp .env.example .env
```

Generate application key:

```bash
php artisan key:generate
```

---

## Database Configuration

Update the database settings in the `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=booking_system
DB_USERNAME=root
DB_PASSWORD=
```

Run migrations:

```bash
php artisan migrate
```

Run seeders:

```bash
php artisan db:seed
```

Or reset and seed:

```bash
php artisan migrate:fresh --seed
```

---

## Authentication

Authentication is implemented using Laravel Sanctum.

Generate Sanctum tables if required:

```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

All protected APIs require a valid Bearer Token.

---

## Email Notifications

The application uses Laravel Events, Listeners, and Notifications to trigger appointment-related email notifications.

Supported events:

* AppointmentBooked
* AppointmentCancelled
* AppointmentRescheduled

For development purposes, email sending is simulated using Laravel's log mail driver.

Configure in `.env`:

```env
MAIL_MAILER=log
```

Generated email content can be viewed in:

```text
storage/logs/laravel.log
```

No external email service is required during development.

---

## Queue Configuration

The application supports queue-based processing for notifications and background jobs.

Configure queue connection:

```env
QUEUE_CONNECTION=database
```

Generate queue tables:

```bash
php artisan queue:table
php artisan queue:failed-table
php artisan migrate
```

### Running Queue Worker

To process queued jobs:

```bash
php artisan queue:work
```

Check failed jobs:

```bash
php artisan queue:failed
```

Retry failed jobs:

```bash
php artisan queue:retry all
```

### Without Queue Processing

If queue workers are not used, listeners can be executed synchronously by removing the `ShouldQueue` interface from listener classes.

---

## Event-Driven Architecture

The system uses Laravel Events and Listeners to separate business logic from side effects.

### Events

* AppointmentBooked
* AppointmentCancelled
* AppointmentRescheduled

### Listeners

* SendAppointmentBookedNotification
* SendAppointmentCancelledNotification
* SendAppointmentRescheduledNotification

This architecture makes it easy to extend functionality such as:

* Email Notifications
* SMS Notifications
* Audit Logging
* Analytics Tracking
* Third-Party Integrations

---

## Concurrency Handling

To prevent double-booking and race conditions, the application uses:

* Database Transactions
* Row-Level Locking (`lockForUpdate()`)

This ensures appointment and slot updates remain consistent even under concurrent requests.

---

## Project Structure

```text
app/
├── Events/
├── Listeners/
├── Notifications/
├── Services/
├── Http/
│   ├── Controllers/
│   ├── Requests/
├── Models/
└── Providers/
```

---

## Logging

Application logs:

```text
storage/logs/laravel.log
```

Queue failures:

```text
failed_jobs
```

---

## Postman Collection

A Postman collection containing all API requests is included in the root directory of the repository.

Import the collection into Postman to test all endpoints.

---

## Assumptions

* Appointment slots belong to a single doctor.
* Appointments can only be rescheduled within the same doctor.
* Cancelled appointments cannot be rescheduled.
* Past slots cannot be booked or used for rescheduling.
* Only available slots can be booked or assigned during rescheduling.

---

## Future Enhancements

* SMTP Email Integration
* SMS Notifications
* Appointment Reminder Scheduler
* Admin Dashboard
* Audit Trail
* Redis Queue Driver
* Real-Time Notifications
* API Rate Limiting
* Multi-Clinic Support

---

## Author

Developed as part of the Appointment Booking System assessment/project using Laravel best practices, transactions, events, listeners, notifications, and queue-based architecture.

-------------------------------------------------------------------------

# API Documentation

## Base URL

```http
http://localhost:8000/api/v1
```

---

## Authentication

### Login

**POST** `/login`

Authenticate and receive a Sanctum access token.

#### Request

```json
{
    "email": "user@example.com",
    "password": "password"
}
```

#### Response

```json
{
    "success": true,
    "token": "1|xxxxxxxxxxxxxxxxxxxxxxxx"
}
```

---

## Authorization

All endpoints except Login require a Bearer Token.

#### Header

```http
Authorization: Bearer {token}
Accept: application/json
```

---

# Protected APIs

## 1. Get Doctor Availabilities

**GET** `/doctors/availabilities`

Returns available doctor slots.

### Headers

```http
Authorization: Bearer {token}
```

---

## 2. Create Doctor Availability

**POST** `/createAvailability`

Creates availability slots for a doctor.

### Request

```json
{
    "doctor_id": 1,
    "slot_start": "2026-06-10 10:00:00",
    "slot_end": "2026-06-10 10:30:00"
}
```

> Update the request body according to your actual validation rules.

---

## 3. Book Appointment

**POST** `/appointments`

Books an appointment for a patient.

### Request

```json
{
    "patient_id": 1,
    "slot_id": 10
}
```

### Success Response

```json
{
    "success": true,
    "reference_number": "APT123456"
}
```

---

## 4. Get Patients

**GET** `/patients`

Returns the list of patients.

### Headers

```http
Authorization: Bearer {token}
```

---

## 5. Cancel Appointment

**POST** `/appointments/cancel`

Cancels an existing appointment.

### Request

```json
{
    "reference_number": "APT123456"
}
```

### Success Response

```json
{
    "success": true,
    "message": "Appointment cancelled successfully."
}
```

---

## 6. Reschedule Appointment

**POST** `/appointments/reschedule`

Reschedules an existing appointment to a new available slot.

### Request

```json
{
    "reference_number": "APT123456",
    "new_slot_id": 15
}
```

### Success Response

```json
{
    "success": true,
    "message": "Appointment rescheduled successfully.",
    "reference_number": "APT123456"
}
```

---

# Event & Notification System

The application uses Laravel Events and Listeners.

### Events

* AppointmentBooked
* AppointmentCancelled
* AppointmentRescheduled

### Notifications

Whenever an appointment is:

* Booked
* Cancelled
* Rescheduled

a notification is generated automatically.

For development purposes, email sending is simulated using:

```env
MAIL_MAILER=log
```

Generated emails are written to:

```text
storage/logs/laravel.log
```

---

# Queue Configuration

If queue processing is enabled:

```env
QUEUE_CONNECTION=database
```

Generate queue tables:

```bash
php artisan queue:table
php artisan queue:failed-table
php artisan migrate
```

Start worker:

```bash
php artisan queue:work
```

---

# Postman Collection

A Postman collection containing all API requests is included in the project root directory.

Import the collection into Postman to test all endpoints.

Example:

```text
/Booking-System.postman_collection.json
```

---

# Development Notes

* Authentication is implemented using Laravel Sanctum.
* Database transactions are used for appointment booking, cancellation, and rescheduling.
* Slot locking is implemented using `lockForUpdate()` to prevent race conditions.
* Notifications are event-driven using Laravel Events and Listeners.
* Email notifications are simulated and written to application logs.
