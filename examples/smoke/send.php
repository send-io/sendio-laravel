<?php

// Run with: php artisan tinker examples/smoke/send.php (inside the Laravel app).

use Illuminate\Support\Facades\Mail;
use Sendio\Laravel\Facades\Sendio;

echo 'config cached: ' . (app()->configurationIsCached() ? 'yes' : 'no') . PHP_EOL;
echo 'mail.default: ' . config('mail.default') . PHP_EOL;
echo 'sendio.base_url: ' . (config('sendio.base_url') ?: '(default https://api.send.io)') . PHP_EOL;

$sent = Mail::raw('Sent by the send.io Laravel smoke test.', function ($message) {
    $message->to(getenv('SMOKE_TO') ?: 'delivered@send.io')->subject('sendio-laravel smoke test');
});

$id = $sent?->getOriginalMessage()->getHeaders()->get('X-Sendio-Email-ID')?->getBodyAsString();
echo 'Mail::raw: ' . ($id ? "OK id={$id}" : 'FAIL (no X-Sendio-Email-ID)') . PHP_EOL;

if ($id) {
    $email = Sendio::emails()->get($id);
    echo "Sendio::emails()->get: OK id={$email->id} from={$email->from} last_event=" . ($email->last_event ?? '?') . PHP_EOL;
}
