# SierraTecnologia Transmissor

**SierraTecnologia Transmissor** transmissor services transmissor and providers for users required by various SierraTecnologia packages. Validator functionality, and basic controller included out-of-the-box.

[![Packagist](https://img.shields.io/packagist/v/sierratecnologia/transmissor.svg?label=Packagist&style=flat-square)](https://packagist.org/packages/sierratecnologia/transmissor)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/sierratecnologia/transmissor.svg?label=Scrutinizer&style=flat-square)](https://scrutinizer-ci.com/g/sierratecnologia/transmissor/)
[![Travis](https://img.shields.io/travis/sierratecnologia/transmissor.svg?label=TravisCI&style=flat-square)](https://travis-ci.org/sierratecnologia/transmissor)
[![StyleCI](https://styleci.io/repos/60968880/shield)](https://styleci.io/repos/60968880)
[![License](https://img.shields.io/packagist/l/sierratecnologia/transmissor.svg?label=License&style=flat-square)](https://github.com/sierratecnologia/Transmissor/blob/master/LICENSE)


## Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
  - [Notifications](#notifications)
  - [User Messaging](#user-messaging)
  - [Activity Logging](#activity-logging)
  - [Webhooks](#webhooks)
- [Facades](#facades)
- [API Reference](#api-reference)
- [Testing](#testing)
- [Changelog](#changelog)
- [Support](#support)
- [Contributing & Protocols](#contributing--protocols)
- [Security Vulnerabilities](#security-vulnerabilities)


## Overview

**Transmissor** is a comprehensive messaging and notification library for Laravel applications. It provides a unified interface for managing user notifications, internal messaging systems, activity logging, and webhook handling. The package integrates seamlessly with Laravel's notification system and extends it with additional features like threaded messaging, user activity tracking, and real-time notifications.


## Features

- **User Notifications System**: Create, manage, and deliver notifications to users with support for multiple channels
- **Internal Messaging**: Complete threaded messaging system with support for groups, participants, and message history
- **Activity Logging**: Track user activities and system events with detailed request information
- **Webhook Management**: Handle incoming webhooks with support for various integrations
- **Real-time Broadcasting**: Built-in support for Laravel Broadcasting channels
- **Flexible Policies**: Includes authorization policies for threads, topics, and replies
- **Multiple Database Support**: Compatible with MySQL, PostgreSQL, SQLite, and SQL Server
- **API Ready**: Includes API controllers and resources for RESTful implementations
- **Helper Functions**: Convenient helper functions for quick access to features
- **Cacheable Eloquent Models**: Performance optimization through model caching
- **Laravel Integration**: Seamless integration with Laravel's service container, facades, and events


## Requirements

- PHP 8.3 or higher
- Laravel 13.x
- Dependencies:
  - `sierratecnologia/muleta` ^2.1
  - `sierratecnologia/crypto` ^2.1
  - `sierratecnologia/integrations` ^2.1
  - `sierratecnologia/porteiro` ^2.1
  - `sierratecnologia/population` ^2.1
  - `sierratecnologia/locaravel` ^2.1
  - `sierratecnologia/telefonica` ^2.1


## Installation

Install the package via Composer:

```bash
composer require sierratecnologia/transmissor
```

The package will automatically register its service provider thanks to Laravel's package auto-discovery.

### Publish Configuration and Assets

Publish the configuration files:

```bash
php artisan vendor:publish --provider="Transmissor\TransmissorProvider" --tag="config"
```

Publish views (optional):

```bash
php artisan vendor:publish --provider="Transmissor\TransmissorProvider" --tag="views"
```

Publish translations (optional):

```bash
php artisan vendor:publish --provider="Transmissor\TransmissorProvider" --tag="lang"
```

### Run Migrations

The package includes migrations for notifications, messages, threads, and related tables:

```bash
php artisan migrate
```


## Configuration

After publishing, you'll find the configuration file at `config/sitec/transmissor.php`. While the default configuration is empty (allowing you to use defaults), you can customize various aspects of the package.

### Available Configuration Options

```php
return [
    // Add custom configuration here if needed
];
```

### Email Delivery

Transmissor sends email alerts through Laravel's `Mail` facade, so it uses
whatever mailer the host application configures. The lib does not pick a
provider.

The recommended provider for the group's apps is [Resend](https://resend.com).
Laravel ships the `resend` mailer; it only needs the SDK:

```bash
composer require resend/resend-php
```

```dotenv
MAIL_MAILER=resend
RESEND_API_KEY=re_...
MAIL_FROM_ADDRESS="alerts@your-domain.com"
```

The sending domain must be verified in Resend. Run `php artisan config:cache`
after changing `.env`. Alert recipients come from `sitec.transmissor.email.to`,
`transmissor.alerts_email` or `central.alerts_email`, in that order.


## Usage

### Notifications

The Transmissor package extends Laravel's notification system with additional features.

#### Creating Notifications

You can create notifications programmatically using the `NotificationService`:

```php
use Transmissor\Services\NotificationService;

$notificationService = app(NotificationService::class);

// Send a notification to a specific user
$notificationService->notify(
    $userId,
    'info', // flag: info, warning, error, success
    'Welcome to Transmissor',
    'This is a detailed notification message.'
);

// Create a notification with full control
$notificationService->create([
    'user_id' => $userId,
    'flag' => 'success',
    'title' => 'Task Completed',
    'details' => 'Your task has been completed successfully.',
]);

// Broadcast to all users (user_id = 0)
$notificationService->create([
    'user_id' => 0,
    'flag' => 'announcement',
    'title' => 'System Maintenance',
    'details' => 'Scheduled maintenance on Sunday.',
]);
```

#### Retrieving Notifications

```php
// Get all notifications (paginated)
$notifications = $notificationService->paginated();

// Get notifications for a specific user
$userNotifications = $notificationService->userBased($userId);

// Get paginated notifications for a user
$userNotifications = $notificationService->userBasedPaginated($userId);

// Search notifications
$results = $notificationService->search('keyword', $userId);

// Find by UUID
$notification = $notificationService->findByUuid($uuid);
```

#### Managing Notifications

```php
// Mark as read
$notificationService->markAsRead($notificationId);

// Update a notification
$notificationService->update($notificationId, [
    'title' => 'Updated Title',
    'details' => 'Updated details',
]);

// Delete a notification
$notificationService->destroy($notificationId);
```

#### Using the Notification Facade

```php
use Transmissor\Facades\Notifications;

// Access notification service methods through the facade
Notifications::notify($userId, 'info', 'Title', 'Details');
```


### User Messaging

Transmissor includes a complete messaging system based on threads and participants.

#### Models

- **Thread**: Represents a conversation
- **Message**: Individual messages within a thread
- **Participant**: Users participating in a thread

#### Creating a Message Thread

```php
use Transmissor\Models\Messenger\Thread;
use Transmissor\Models\Messenger\Message;
use Transmissor\Models\Messenger\Participant;

// Create a new thread
$thread = Thread::create([
    'subject' => 'Discussion Topic',
]);

// Add the first message
$message = Message::create([
    'thread_id' => $thread->id,
    'user_id' => auth()->id(),
    'body' => 'Hello! This is the first message.',
]);

// Add participants
$thread->addParticipant($userId);
$thread->addParticipants([$userId1, $userId2, $userId3]);
```

#### Using the Messagable Trait

Add the trait to your User model:

```php
use Transmissor\Traits\Messagable;

class User extends Authenticatable
{
    use Messagable;

    // Your user model code...
}
```

Now you can use messaging methods directly on user instances:

```php
// Get all threads for a user
$threads = $user->threads();

// Get unread messages count
$unreadCount = $user->unreadMessagesCount();

// Check if user is participant in a thread
$isParticipant = $user->isParticipantInThread($threadId);
```

#### Retrieving Messages

```php
// Get all threads
$threads = Thread::getAllLatest();

// Get threads for current user
$userThreads = Thread::forUser(auth()->id());

// Get messages in a thread
$messages = $thread->messages;

// Get latest message
$latestMessage = $thread->latestMessage;

// Get participants
$participants = $thread->participants;
```


### Activity Logging

Track user activities and system events throughout your application.

#### Using the Activity Service

```php
use Transmissor\Services\ActivityService;

$activityService = app(ActivityService::class);

// Log an activity
$activityService->log('User viewed dashboard');

// Get activities for a user
$activities = $activityService->getByUser($userId);

// Get paginated activities
$activities = $activityService->getByUser($userId, 25);
```

#### Using the Activity Helper

The package includes a convenient helper function:

```php
// Log an activity anywhere in your application
activity('User completed checkout process');
activity('Admin updated settings');
```

#### Activity Middleware

Apply the activity middleware to routes for automatic logging:

```php
use Transmissor\Http\Middleware\Activity;

Route::middleware([Activity::class])->group(function () {
    // Your routes here
});
```

#### What Gets Logged

Each activity record includes:
- User ID
- Description
- Request details (URL, method, query string, IP address, payload)
- Timestamp


### Webhooks

Handle incoming webhooks from external services.

#### Webhook Controller

The package includes controllers for webhook handling:

```php
use Transmissor\Http\Controllers\Webhook\StoreController;
use Transmissor\Http\Controllers\WebhookReceivedController;
```

#### Webhook Jobs

Process webhooks asynchronously with included job classes:

```php
use Transmissor\Jobs\Webhook\UptimeCheckFailed;
use Transmissor\Jobs\Webhook\BrokenLinksFound;
```

#### Creating Custom Webhook Handlers

```php
namespace App\Jobs;

use Transmissor\Jobs\Webhook\UptimeCheckFailed;

class HandleUptimeAlert extends UptimeCheckFailed
{
    public function handle()
    {
        // Your custom webhook handling logic
        $payload = $this->webhookCall->payload;

        // Process the webhook data
        // Send notifications, update database, etc.
    }
}
```


## Facades

The package provides convenient facades for quick access to services:

### Transmissor Facade

```php
use Transmissor\Facades\Transmissor;

// Access the main Transmissor service
Transmissor::method();
```

### Notifications Facade

```php
use Transmissor\Facades\Notifications;

// Access notification service methods
Notifications::notify($userId, 'info', 'Title', 'Details');
```

### Activity Facade

```php
use Transmissor\Facades\Activity;

// Access activity service methods
Activity::log('User performed action');
```


## API Reference

### NotificationService Methods

| Method | Parameters | Description |
|--------|-----------|-------------|
| `all()` | - | Get all notifications |
| `paginated()` | - | Get paginated notifications |
| `userBased($id)` | `$id` (int) | Get all notifications for a user |
| `userBasedPaginated($id)` | `$id` (int) | Get paginated notifications for a user |
| `search($input, $id)` | `$input` (string), `$id` (int|null) | Search notifications |
| `notify($userId, $flag, $title, $details)` | `$userId` (int), `$flag` (string), `$title` (string), `$details` (string) | Create and send a notification |
| `create($input)` | `$input` (array) | Create a notification |
| `find($id)` | `$id` (int) | Find notification by ID |
| `findByUuid($uuid)` | `$uuid` (string) | Find notification by UUID |
| `update($id, $input)` | `$id` (int), `$input` (array) | Update a notification |
| `markAsRead($id)` | `$id` (int) | Mark notification as read |
| `destroy($id)` | `$id` (int) | Delete a notification |


### ActivityService Methods

| Method | Parameters | Description |
|--------|-----------|-------------|
| `getByUser($userId, $paginate)` | `$userId` (int), `$paginate` (int|null) | Get activities for a user |
| `log($description)` | `$description` (string) | Create an activity log entry |


### Thread Model Methods

| Method | Parameters | Description |
|--------|-----------|-------------|
| `getAllLatest()` | - | Get all threads ordered by latest activity |
| `forUser($userId)` | `$userId` (int) | Get threads for a specific user |
| `addParticipant($userId)` | `$userId` (int) | Add a participant to the thread |
| `addParticipants($userIds)` | `$userIds` (array) | Add multiple participants |
| `removeParticipant($userId)` | `$userId` (int) | Remove a participant |
| `markAsRead($userId)` | `$userId` (int) | Mark thread as read for user |


## Testing

The package includes comprehensive tests for all major features.

### Running Tests

```bash
# Run all tests
composer test

# Run tests with coverage
composer test-coverage
```

### Test Structure

```
tests/
├── Feature/
│   └── NotificationIntegrationTest.php
├── Unit/
│   └── NotificationServiceTest.php
└── ActivityServiceTest.php
```


## Changelog

Refer to the [Changelog](CHANGELOG.md) for a full history of the project.


## Support

The following support channels are available at your fingertips:

- [Chat on Slack](https://bit.ly/sierratecnologia-slack)
- [Help on Email](mailto:help@sierratecnologia.com.br)
- [Follow on Twitter](https://twitter.com/sierratecnologia)


## Contributing & Protocols

Thank you for considering contributing to this project! The contribution guide can be found in [CONTRIBUTING.md](CONTRIBUTING.md).

Bug reports, feature requests, and pull requests are very welcome.

- [Versioning](CONTRIBUTING.md#versioning)
- [Pull Requests](CONTRIBUTING.md#pull-requests)
- [Coding Standards](CONTRIBUTING.md#coding-standards)
- [Feature Requests](CONTRIBUTING.md#feature-requests)
- [Git Flow](CONTRIBUTING.md#git-flow)


## Security Vulnerabilities

If you discover a security vulnerability within this project, please send an e-mail to [help@sierratecnologia.com.br](help@sierratecnologia.com.br). All security vulnerabilities will be promptly addressed.


## About SierraTecnologia

SierraTecnologia is a software solutions startup, specialized in integrated enterprise solutions for SMEs established in Rio de Janeiro, Brazil since June 2008. We believe that our drive The Value, The Reach, and The Impact is what differentiates us and unleash the endless possibilities of our philosophy through the power of software. We like to call it Innovation At The Speed Of Life. That’s how we do our share of advancing humanity.

## License

This is opensource package [The MIT License (MIT)](LICENSE).

(c) 2008-2020 SierraTecnologia, Permitimos a cópia sem restrições.
