<?php

use Sendio\Client;
use Sendio\Laravel\Facades\Sendio;
use Sendio\Service\ApiKey;
use Sendio\Service\Audience;
use Sendio\Service\Automation;
use Sendio\Service\Batch;
use Sendio\Service\Broadcast;
use Sendio\Service\Contact;
use Sendio\Service\ContactProperty;
use Sendio\Service\Domain;
use Sendio\Service\Email;
use Sendio\Service\Mailbox;
use Sendio\Service\Segment;
use Sendio\Service\Template;
use Sendio\Service\Topic;
use Sendio\Service\Webhook;

it('resolves Sendio client', function () {
    $app = app();

    $app->get('config')->set('sendio', [
        'api_key' => 'test',
    ]);

    expect(Sendio::getFacadeRoot())->toBeInstanceOf(Client::class);
});

it('can get an API service', function () {
    $app = app();

    $app->get('config')->set('sendio', [
        'api_key' => 'test',
    ]);

    expect(Sendio::apiKeys())->toBeInstanceOf(ApiKey::class)
        ->and(Sendio::audiences())->toBeInstanceOf(Audience::class)
        ->and(Sendio::automations())->toBeInstanceOf(Automation::class)
        ->and(Sendio::batch())->toBeInstanceOf(Batch::class)
        ->and(Sendio::broadcasts())->toBeInstanceOf(Broadcast::class)
        ->and(Sendio::contacts())->toBeInstanceOf(Contact::class)
        ->and(Sendio::contactProperties())->toBeInstanceOf(ContactProperty::class)
        ->and(Sendio::domains())->toBeInstanceOf(Domain::class)
        ->and(Sendio::emails())->toBeInstanceOf(Email::class)
        ->and(Sendio::mailboxes())->toBeInstanceOf(Mailbox::class)
        ->and(Sendio::segments())->toBeInstanceOf(Segment::class)
        ->and(Sendio::templates())->toBeInstanceOf(Template::class)
        ->and(Sendio::topics())->toBeInstanceOf(Topic::class)
        ->and(Sendio::webhooks())->toBeInstanceOf(Webhook::class);
});

it('exposes the broadcast versions and clicked links services', function () {
    $app = app();

    $app->get('config')->set('sendio', [
        'api_key' => 'test',
    ]);

    expect(Sendio::broadcasts()->versions)->toBeInstanceOf(\Sendio\Service\Broadcasts\Version::class)
        ->and(Sendio::broadcasts()->clickedLinks)->toBeInstanceOf(\Sendio\Service\Broadcasts\ClickedLink::class)
        ->and(method_exists(Sendio::broadcasts(), 'estimate'))->toBeTrue()
        ->and(method_exists(Sendio::broadcasts(), 'test'))->toBeTrue()
        ->and(method_exists(Sendio::broadcasts(), 'stats'))->toBeTrue();
});
