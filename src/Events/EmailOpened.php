<?php

namespace Sendio\Laravel\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmailOpened
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new email opened event instance.
     */
    public function __construct(
        public array $payload,
        public array $headers = []
    ) {
        //
    }
}
