<?php

use Illuminate\Support\Facades\Event;
use Sendio\Laravel\Events\ContactCreated;
use Sendio\Laravel\Events\ContactDeleted;
use Sendio\Laravel\Events\ContactTopicsUpdated;
use Sendio\Laravel\Events\ContactUpdated;
use Sendio\Laravel\Events\DomainCreated;
use Sendio\Laravel\Events\DomainDeleted;
use Sendio\Laravel\Events\DomainUpdated;
use Sendio\Laravel\Events\EmailBounced;
use Sendio\Laravel\Events\EmailClicked;
use Sendio\Laravel\Events\EmailComplained;
use Sendio\Laravel\Events\EmailDelivered;
use Sendio\Laravel\Events\EmailDeliveryDelayed;
use Sendio\Laravel\Events\EmailFailed;
use Sendio\Laravel\Events\EmailOpened;
use Sendio\Laravel\Events\EmailReceived;
use Sendio\Laravel\Events\EmailScheduled;
use Sendio\Laravel\Events\EmailSent;
use Sendio\Laravel\Events\EmailSuppressed;
use Sendio\Laravel\Events\SuppressionAdded;
use Sendio\Laravel\Events\SuppressionRemoved;
use Sendio\Laravel\Events\TopicCreated;
use Sendio\Laravel\Events\TopicDeleted;
use Sendio\Laravel\Events\TopicUpdated;
use Sendio\Laravel\Http\Controllers\WebhookController as Controller;

test('correct methods are called and handled based on sendio webhook event', function (string $name, string $event) {
    $request = webhookRequest($name);

    Event::fake([
        $event,
    ]);

    $response = (new Controller)->handleWebhook($request);

    Event::assertDispatched($event, function ($e) use ($request) {
        return $request->getContent() == json_encode($e->payload)
            && $e->headers === [
                'svix-id' => 'msg_123456789',
                'svix-timestamp' => '1614265330',
                'svix-signature' => 'v1,exampleSignature',
            ];
    });

    expect($response->getStatusCode())->toBe(200)
        ->and($response->getContent())->toBe('Webhook handled');
})->with([
    ['contact.created', ContactCreated::class],
    ['contact.deleted', ContactDeleted::class],
    ['contact.topics.updated', ContactTopicsUpdated::class],
    ['contact.updated', ContactUpdated::class],
    ['domain.created', DomainCreated::class],
    ['domain.deleted', DomainDeleted::class],
    ['domain.updated', DomainUpdated::class],
    ['email.bounced', EmailBounced::class],
    ['email.clicked', EmailClicked::class],
    ['email.complained', EmailComplained::class],
    ['email.delivered', EmailDelivered::class],
    ['email.delivery_delayed', EmailDeliveryDelayed::class],
    ['email.opened', EmailOpened::class],
    ['email.sent', EmailSent::class],
    ['email.failed', EmailFailed::class],
    ['email.suppressed', EmailSuppressed::class],
    ['email.scheduled', EmailScheduled::class],
    ['email.received', EmailReceived::class],
    ['suppression.added', SuppressionAdded::class],
    ['suppression.removed', SuppressionRemoved::class],
    ['topic.created', TopicCreated::class],
    ['topic.deleted', TopicDeleted::class],
    ['topic.updated', TopicUpdated::class],
]);

test('svix-id is forwarded to the event so it can be used as a dedup key', function () {
    $request = webhookRequest('email.sent');

    Event::fake([EmailSent::class]);

    (new Controller)->handleWebhook($request);

    Event::assertDispatched(EmailSent::class, function ($e) {
        return $e->headers['svix-id'] === 'msg_123456789';
    });
});

test('event receives empty headers when svix headers are absent', function () {
    $request = Illuminate\Http\Request::create('/', 'POST', [], [], [], [], json_encode([
        'id' => 'mx_evt_123456789',
        'type' => 'email.sent',
    ]));

    Event::fake([EmailSent::class]);

    (new Controller)->handleWebhook($request);

    Event::assertDispatched(EmailSent::class, function ($e) {
        return $e->headers === [];
    });
});

test('normal response is returned if method is missing', function () {
    $request = webhookRequest('email.foo');

    Event::fake();

    $response = (new Controller)->handleWebhook($request);

    Event::assertNothingDispatched();

    expect($response->getStatusCode())->toBe(200)
        ->and($response->getContent())->toBeEmpty();
});

test('verify webhook signature middleware is called when webhook secret is set', function () {
    config(['sendio.webhook.secret' => 'secret']);
    config(['sendio.webhook.tolerance' => 300]);

    Event::fake([
        EmailDelivered::class,
    ]);

    $this->postJson('/sendio/webhook', [
        'id' => 'mx_evt_123456789',
        'type' => 'email.delivered',
    ])->assertForbidden();

    Event::assertNothingDispatched();
});

// This function creates a new Request object with a JSON body that does not include the fields expected by your controller.
function invalidWebhookRequest()
{
    return new Illuminate\Http\Request([], [], [], [], [], ['CONTENT_TYPE' => 'application/json'], json_encode(['invalid' => 'data']));
}

test('it returns an error response for invalid payload', function () {
    $request = invalidWebhookRequest();

    $response = (new Controller)->handleWebhook($request);

    expect($response->getStatusCode())->toBe(400);
});
