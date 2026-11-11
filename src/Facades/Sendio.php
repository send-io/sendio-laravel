<?php

namespace Sendio\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Provides send.io integration for Laravel and Symfony Mailer.
 *
 * @method static \Sendio\Service\ApiKey apiKeys()
 * @method static \Sendio\Service\Audience audiences()
 * @method static \Sendio\Service\Automation automations()
 * @method static \Sendio\Service\Batch batch()
 * @method static \Sendio\Service\Broadcast broadcasts()
 * @method static \Sendio\Service\Contact contacts()
 * @method static \Sendio\Service\ContactProperty contactProperties()
 * @method static \Sendio\Service\Domain domains()
 * @method static \Sendio\Service\Email emails()
 * @method static \Sendio\Service\Mailbox mailboxes()
 * @method static \Sendio\Service\Segment segments()
 * @method static \Sendio\Service\Template templates()
 * @method static \Sendio\Service\Topic topics()
 * @method static \Sendio\Service\Usage usage()
 * @method static \Sendio\Service\Webhook webhooks()
 *
 * @see \Sendio\Client
 */
class Sendio extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'sendio';
    }
}
