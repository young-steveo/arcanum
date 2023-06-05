<?php

declare(strict_types=1);

namespace Arcanum\Flow;

interface Sender extends Stage
{
    /**
     * Send a payload.
     */
    public function send(object $payload): object;
}
