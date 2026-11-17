# send.io for Laravel

The official Laravel integration for the [send.io](https://send.io) email API: a `sendio` mail
transport, a `Sendio` facade for the full API, and a webhook handler that dispatches Laravel events.
Built on [`sendio/sendio-php`](https://packagist.org/packages/sendio/sendio-php).

> Requires PHP 8.1+ and Laravel 10, 11, 12 or 13

Full API reference: https://developer.send.io/docs/api

## Install

```bash
composer require sendio/sendio-laravel
```

The service provider is registered automatically. Add your API key (keys start with `mx_`) to `.env`:

```ini
SENDIO_API_KEY=mx_your_api_key
```

## Send mail with the `sendio` mailer

Add a mailer to the `mailers` array in `config/mail.php`:

```php
'sendio' => [
    'transport' => 'sendio',
],
```

Then select it in `.env`:

```ini
MAIL_MAILER=sendio
MAIL_FROM_ADDRESS=onboarding@yourdomain.com
MAIL_FROM_NAME=Acme
```

Every Laravel mail API now goes through send.io:

```php
use Illuminate\Support\Facades\Mail;

Mail::raw('It works!', function ($message) {
    $message->to('delivered@send.io')->subject('Hello from send.io');
});

Mail::to('delivered@send.io')->send(new App\Mail\OrderShipped($order));
```

The send.io email id is added to the sent message as the `X-Sendio-Email-ID` header. To make a send
idempotent, add a `Sendio-Idempotency-Key` header to the message; it is passed to send.io as the
idempotency key and not sent as an email header. Mailable metadata becomes send.io tags.

## Use the API with the facade

```php
use Sendio\Laravel\Facades\Sendio;

$email = Sendio::emails()->send([
    'from' => 'Acme <onboarding@yourdomain.com>',
    'to' => ['delivered@send.io'],
    'subject' => 'Hello from send.io',
    'html' => '<p>It works!</p>',
]);

// Batch
Sendio::batch()->send([
    ['from' => 'Acme <onboarding@yourdomain.com>', 'to' => ['delivered@send.io'], 'subject' => 'One', 'text' => '1'],
    ['from' => 'Acme <onboarding@yourdomain.com>', 'to' => ['delivered@send.io'], 'subject' => 'Two', 'text' => '2'],
]);

// Retrieve
$email = Sendio::emails()->get($email->id);

// Domains
$domains = Sendio::domains()->list();

// Add a domain that also receives email, then turn receiving off later
// (send.io stops accepting its email; remove the MX record pointing at send.io).
$domain = Sendio::domains()->create([
    'name' => 'yourdomain.com',
    'capabilities' => ['receiving' => 'enabled'],
]);
Sendio::domains()->update($domain->id, ['capabilities' => ['receiving' => 'disabled']]);

// Mailboxes (IMAP/SMTP email accounts)
$mailbox = Sendio::mailboxes()->create(['address' => 'ada@yourdomain.com']);
Sendio::mailboxes()->update($mailbox->id, ['warmup_enabled' => false]);

// Transactional emails: save the envelope of a published template once, then
// send it by id with only the recipient and the variables.
$tx = Sendio::transactional()->create([
    'name' => 'Sign-in code',
    'template_id' => 'signin-code',
    'from' => 'Acme <security@yourdomain.com>',
    'subject' => 'Your code is {{CODE}}',
]);
Sendio::emails()->send([
    'transactional_id' => $tx->id,
    'to' => 'ada@example.com',
    'variables' => ['CODE' => '123456'],
]);
```

The `Sendio\Contracts\Client` (and `Sendio\Client`) binding is also available for dependency injection.

## Configuration

Publish the config file to change the defaults:

```bash
php artisan vendor:publish --tag=sendio-config
```

| Key in `config/sendio.php` | Env | Default |
|---|---|---|
| `api_key` | `SENDIO_API_KEY` | none (required) |
| `base_url` | `SENDIO_BASE_URL` | `https://api.send.io` |
| `routes` | `SENDIO_ROUTES` | `true` |
| `domain` | `SENDIO_DOMAIN` | `null` |
| `path` | `SENDIO_PATH` | `sendio` |
| `webhook.secret` | `SENDIO_WEBHOOK_SECRET` | none |
| `webhook.tolerance` | `SENDIO_WEBHOOK_TOLERANCE` | `300` |

The API key and base URL are read from config, never directly from the environment, so
`php artisan config:cache` works. Use `SENDIO_BASE_URL` to point at another endpoint, for example
a test environment:

```ini
SENDIO_BASE_URL=https://api.send.io
```

`services.sendio.key` and `services.sendio.base_url` in `config/services.php` are used as fallbacks.

## Webhooks

The package registers `POST /sendio/webhook` (change the prefix with `SENDIO_PATH`). Point a send.io
webhook at it and set the signing secret so requests are verified:

```ini
SENDIO_WEBHOOK_SECRET=whsec_...
```

Exclude the route from CSRF protection (for example `sendio/*`). Each webhook dispatches a Laravel
event you can listen for, such as `Sendio\Laravel\Events\EmailDelivered`, `EmailBounced`,
`EmailOpened`, `EmailClicked`, `EmailComplained`, `EmailReceived`, `ContactCreated` or
`DomainUpdated` (see `src/Events`).

```php
use Illuminate\Support\Facades\Event;
use Sendio\Laravel\Events\EmailBounced;

Event::listen(function (EmailBounced $event) {
    logger()->warning('Bounce', $event->payload);
});
```

## Error handling

The mail transport throws `Symfony\Component\Mailer\Exception\TransportException` with the send.io
error message when a send fails. The facade throws `Sendio\Exceptions\ErrorException` for API errors
(`getErrorCode()`, `getErrorType()`, `getErrorMessage()`) and
`Sendio\Exceptions\TransporterException` for network failures.

## Development

```bash
composer install   # resolves sendio/sendio-php from ../php through a path repository
vendor/bin/pest

# Live test in a fresh Laravel app in Docker (sends one email):
SENDIO_API_KEY=mx_... SENDIO_BASE_URL=https://api.send.io examples/smoke.sh
```

## License

MIT. See [LICENSE](LICENSE) and [NOTICE](NOTICE).
