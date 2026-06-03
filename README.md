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
git clone https://github.com/amita2092/booking-system.git
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
## 7. Get Appointment Details

**POST** `/appointments/details`

Returns complete appointment information using the appointment reference number.

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
    "data": {
        "reference_number": "APT123456",
        "status": "booked",
        "patient": {
            "id": 1,
            "name": "John Doe"
        },
        "doctor": {
            "id": 2,
            "name": "Dr. Smith"
        },
        "slot": {
            "id": 10,
            "slot_start": "2026-06-10 10:00:00",
            "slot_end": "2026-06-10 10:30:00"
        },
        "created_at": "2026-06-03T12:00:00.000000Z"
    }
}
```

### Error Response

```json
{
    "success": false,
    "message": "Appointment not found."
}
```

---

## 8. Get Doctor Appointments

**POST** `/appointments/doctorAppointments`

Returns appointments for a doctor. By default, all appointments for the specified doctor are returned.

Supports optional filtering and pagination.

### Request

#### Get all appointments for a doctor

```json
{
    "doctor_id": 1
}
```

#### Filter by date

```json
{
    "doctor_id": 1,
    "date": "2026-06-10"
}
```

#### Filter by status

```json
{
    "doctor_id": 1,
    "status": "booked"
}
```

#### Paginated request

```json
{
    "doctor_id": 1,
    "page": 1,
    "per_page": 10
}
```

#### Combined filters

```json
{
    "doctor_id": 1,
    "date": "2026-06-10",
    "status": "booked",
    "page": 1,
    "per_page": 10
}
```

### Request Parameters

| Parameter | Required | Description                  |
| --------- | -------- | ---------------------------- |
| doctor_id | Yes      | Doctor ID                    |
| date      | No       | Filter appointments by date  |
| status    | No       | Filter by appointment status |
| page      | No       | Page number for pagination   |
| per_page  | No       | Records per page             |

### Success Response

```json
{
    "success": true,
    "data": [
        {
            "reference_number": "APT123456",
            "status": "booked",
            "patient_id": 1,
            "slot_id": 10
        }
    ],
    "pagination": {
        "current_page": 1,
        "per_page": 10,
        "total": 25,
        "last_page": 3
    }
}
```

### Notes

* If no filters are provided, all appointments for the specified doctor are returned.
* Date filtering returns appointments scheduled on the specified date.
* Pagination is supported using `page` and `per_page`.
* Status filtering can be used to retrieve booked, cancelled, or rescheduled appointments.


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

# Testing

The project includes automated feature tests to validate the core appointment booking workflows, business rules, authentication, and event-driven functionality.

## Test Coverage

### Appointment Management

The test suite covers:

- Booking an appointment
- Cancelling an appointment
- Rescheduling an appointment
- Retrieving appointment details
- Listing appointments for a doctor
- Filtering doctor appointments by date
- Pagination support for appointment listings

### Event Verification

The following events are verified during testing:

- AppointmentBooked
- AppointmentCancelled
- AppointmentRescheduled

### Authentication

Protected endpoints are tested using Laravel Sanctum authentication.

### Database Testing

Tests use Laravel's database refresh functionality to ensure:

- Clean database state for each test
- Test isolation
- Consistent and repeatable execution

---

## Running Tests

Run the complete test suite:

```bash
php artisan test
```

Run only appointment-related tests:

```bash
php artisan test tests/Feature/Api/AppointmentTest.php
```

Run a specific test method:

```bash
php artisan test --filter=test_can_book_appointment
```

Run tests with verbose output:

```bash
php artisan test --verbose
```

Stop execution on first failure:

```bash
php artisan test --stop-on-failure
```

---

## Testing Strategy

The test suite focuses on validating:

- API endpoint behavior
- Request validation
- Authentication requirements
- Business logic execution
- Database state changes
- Event dispatching
- Appointment lifecycle workflows

The goal is to ensure reliability, maintainability, and confidence when introducing new features or changes.

---

## Future Testing Enhancements

Potential future improvements include:

- Notification testing
- Queue job testing
- Authorization and role-based access testing
- Concurrent booking and race condition testing
- API rate limiting tests
- End-to-end integration testing
- Performance and load testing

---

## Key Principles

- Independent and repeatable tests
- Consistent database state across test runs
- Verification of event-driven architecture
- Coverage of critical appointment workflows
- Use of Laravel's built-in testing framework and best practices


-------------------------------------------------------------------

# Performance Considerations & Scalability

## Assumptions

The system is designed with the following scale assumptions:

- 200 active doctors
- 10,000 appointments per day
- Multiple concurrent booking requests
- High read volume for appointment and availability queries

---

## Current Performance Optimizations

### Database Transactions

Appointment booking, cancellation, and rescheduling operations are executed inside database transactions to ensure data consistency.

### Row-Level Locking

The system uses `lockForUpdate()` when booking or rescheduling appointments to prevent:

- Double bookings
- Race conditions
- Slot allocation conflicts

This ensures that only one request can modify a slot at a time.

### Event-Driven Architecture

Notifications are separated from business logic through Events and Listeners.

Benefits:

- Faster API responses
- Better maintainability
- Easier integration with external systems

### Queue-Based Processing

Notification processing can be handled asynchronously using Laravel Queues.

Benefits:

- Reduced request latency
- Better user experience
- Improved throughput under heavy load

---

## Database Scaling Strategies

### Indexing

Add indexes on frequently queried columns:

```sql
appointments.reference_number
appointments.patient_id
appointments.slot_id
appointments.status
appointment_slots.doctor_id
appointment_slots.slot_start
appointment_slots.status
```

Benefits:

- Faster searches
- Faster filtering
- Improved query performance

### Query Optimization

Use eager loading to prevent N+1 query problems:

```php
Appointment::with([
    'patient',
    'slot',
    'slot.doctor'
]);
```

Benefits:

- Fewer database queries
- Lower database load

### Pagination

Appointment listing endpoints should always use pagination:

```php
$query->paginate(20);
```

Benefits:

- Smaller responses
- Reduced memory usage
- Better API performance

---

## Horizontal Scaling

As traffic grows, the application can be scaled horizontally.

### Multiple Application Servers

Deploy multiple Laravel instances behind a load balancer:

```text
Load Balancer
     │
 ┌───┴───┐
 │       │
App 1  App 2
 │       │
 └───┬───┘
     │
 Database
```

Benefits:

- Increased request capacity
- High availability
- Improved fault tolerance

### Dedicated Queue Workers

Move queue processing to separate worker servers:

```text
API Servers
     │
 Queue
     │
Queue Workers
```

Benefits:

- Faster API responses
- Independent scaling of background jobs

---

## Caching Strategy

Frequently accessed data can be cached using Redis.

Examples:

- Doctor availability
- Doctor profiles
- Appointment statistics
- Patient lookup data

Benefits:

- Reduced database load
- Faster response times

Recommended cache driver:

```env
CACHE_DRIVER=redis
```

---

## Queue Scaling

For high notification volume:

```env
QUEUE_CONNECTION=redis
```

Run multiple workers:

```bash
php artisan queue:work
```

Benefits:

- Parallel job processing
- Improved throughput
- Better handling of peak traffic

---

## Database Replication

For large-scale deployments:

### Primary Database

Handles:

- INSERT
- UPDATE
- DELETE

### Read Replicas

Handle:

- Appointment searches
- Doctor availability lookups
- Reporting queries

Benefits:

- Reduced load on primary database
- Improved read performance

---

## Monitoring & Observability

Recommended tools:

- Laravel Telescope
- Laravel Horizon
- New Relic
- Datadog
- Grafana

Monitor:

- API response times
- Queue processing times
- Failed jobs
- Database performance
- Error rates

---

## Future Enhancements

To support significantly larger workloads:

- Redis-based caching
- Redis queue driver
- Database replication
- API rate limiting
- Distributed locking
- Elasticsearch for advanced search
- Containerized deployment using Docker
- Kubernetes-based orchestration
- Multi-region deployment
- Real-time appointment updates using WebSockets

---

## Expected Outcome

With proper indexing, pagination, queue processing, caching, and horizontal scaling, the system can comfortably support:

- 200+ doctors
- 10,000+ bookings per day
- High concurrent booking traffic
- Large appointment datasets

while maintaining data consistency and preventing double-booking conflicts.
