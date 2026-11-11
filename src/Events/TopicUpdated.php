<?php

namespace Sendio\Laravel\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TopicUpdated
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new topic updated event instance.
     */
    public function __construct(
        public array $payload,
        public array $headers = []
    ) {
        //
    }
}
