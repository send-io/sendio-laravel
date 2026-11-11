<?php

namespace Sendio\Laravel\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmailFailed
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new email failed event instance.
     */
    public function __construct(
        public array $payload,
        public array $headers = []
    ) {
        //
    }
}
