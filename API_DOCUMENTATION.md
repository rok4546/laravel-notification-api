# Multi-Channel Notification and Activity Logging API

A Laravel 11 API service that handles incoming notification requests and logs those activities in a polymorphic structure.

## Project Overview

This API allows you to send notifications through multiple channels (email, SMS, Slack) and automatically logs all notification activities with polymorphic relationships, enabling tracking of notifications sent to different entity types.

## Tech Stack

- **Framework:** Laravel 11.47.0
- **PHP:** 8.2.12
- **Database:** SQLite (configured)
- **API:** RESTful JSON

## Installation

### Prerequisites
- PHP 8.2+
- Composer
- SQLite

### Setup Instructions

1. **Clone the repository**
   ```bash
   git clone https://github.com/YOUR_USERNAME/laravel-notification-api.git
   cd laravel-notification-api
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Create environment file**
   ```bash
   cp .env.example .env
   ```

4. **Generate application key**
   ```bash
   php artisan key:generate
   ```

5. **Create SQLite database**
   ```bash
   touch database/database.sqlite
   ```

6. **Run migrations**
   ```bash
   php artisan migrate
   ```

7. **Create test user (optional)**
   ```bash
   php artisan tinker
   App\Models\User::factory()->create();
   exit
   ```

8. **Start development server**
   ```bash
   php artisan serve
   ```

The server will run at `http://127.0.0.1:8000`

---

## API Endpoints

### Send Notification

**Endpoint:** `POST /api/notifications`

**Headers:**
```
Content-Type: application/json
```

**Request Body:**
```json
{
  "channel": "email",
  "message": "Your notification message",
  "notifiable_type": "App\\Models\\User",
  "notifiable_id": 1
}
```

**Response (201 Created):**
```json
{
  "success": true,
  "message": "Notification logged successfully.",
  "data": {
    "id": 1,
    "channel": "email",
    "message": "Your notification message",
    "notifiable_type": "App\\Models\\User",
    "notifiable_id": 1,
    "created_at": "2026-01-19T13:17:13Z"
  }
}
```

---

## Validation Rules

| Field | Rules |
|-------|-------|
| `channel` | Required, one of: `email`, `sms`, `slack` |
| `message` | Required, max 255 characters |
| `notifiable_type` | Required, one of: `App\Models\User`, `App\Models\Post` |
| `notifiable_id` | Required, integer, must exist in specified table |

---

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   └── NotificationController.php
│   ├── Requests/
│   │   └── NotificationRequest.php
│   └── Middleware/
│       └── ThrottleNotifications.php
├── Models/
│   ├── User.php
│   ├── Post.php
│   └── ActivityLog.php
│
database/
├── migrations/
│   ├── 0001_01_01_000000_create_users_table.php
│   ├── 0001_01_01_000003_create_posts_table.php
│   └── 0001_01_01_000004_create_activity_logs_table.php
└── factories/
    ├── UserFactory.php
    └── PostFactory.php

routes/
├── api.php
└── web.php
```

---

## Key Features

### 1. **Polymorphic Relationships**
- ActivityLog can be associated with User or Post models
- Extensible for future models

### 2. **Form Request Validation**
- Custom validation rules in `NotificationRequest`
- Dynamic validation for `notifiable_id` existence check
- Clear error messages

### 3. **Custom Middleware**
- `ThrottleNotifications` middleware validates API requests
- Header-based authentication support
- Returns 401 Unauthorized for invalid requests

### 4. **Activity Logging**
- Automatic log creation for all notifications
- Stores channel, message, and polymorphic relationship data
- Searchable and filterable activity records

---

## Testing

### Using curl

```bash
curl -X POST http://127.0.0.1:8000/api/notifications \
  -H "Content-Type: application/json" \
  -d '{"channel":"email","message":"Test notification","notifiable_type":"App\\Models\\User","notifiable_id":1}'
```

### Using Postman

1. Create new POST request
2. URL: `http://127.0.0.1:8000/api/notifications`
3. Headers:
   - `Content-Type: application/json`
4. Body (raw JSON):
```json
{
  "channel": "email",
  "message": "Test notification",
  "notifiable_type": "App\\Models\\User",
  "notifiable_id": 1
}
```

### Using Browser Console

```javascript
fetch('http://127.0.0.1:8000/api/notifications', {
  method: 'POST',
  headers: {'Content-Type': 'application/json'},
  body: JSON.stringify({
    channel: 'email',
    message: 'Test notification',
    notifiable_type: 'App\\Models\\User',
    notifiable_id: 1
  })
})
.then(r => r.json())
.then(data => console.log(data))
```

---

## Database Schema

### users table
- id (primary key)
- name
- email
- password
- timestamps

### posts table
- id (primary key)
- title
- content
- timestamps

### activity_logs table
- id (primary key)
- channel (enum: email, sms, slack)
- message (string, max 255)
- notifiable_type (string - model class)
- notifiable_id (integer - ID of the related model)
- timestamps

---

## Models

### ActivityLog Model
```php
public function notifiable()
{
    return $this->morphTo();
}
```

Allows polymorphic relationship with any model.

### User Model
```php
public function activityLogs()
{
    return $this->morphMany(ActivityLog::class, 'notifiable');
}
```

### Post Model
```php
public function activityLogs()
{
    return $this->morphMany(ActivityLog::class, 'notifiable');
}
```

---

## Error Handling

### 422 Unprocessable Entity
Returned when validation fails:
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "channel": ["Channel must be one of: email, sms, slack."],
    "message": ["Message cannot exceed 255 characters."]
  }
}
```

### 401 Unauthorized
Returned when middleware validation fails:
```json
{
  "success": false,
  "message": "Unauthorized: Missing X-Client-Key header."
}
```

---

## Contributing

1. Create a feature branch
2. Make your changes
3. Test thoroughly
4. Submit a pull request

---

## License

MIT License - feel free to use this project for commercial or personal use.

---

## Author

Created for Multi-Channel Notification API Task

**Created:** January 19, 2026
