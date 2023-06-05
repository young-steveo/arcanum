<?php

declare(strict_types=1);

namespace Arcanum\Flow;

interface Pipelayer extends Stage
{
    /**
     * Add a stage to the pipeline.
     *
     * @param Stage|callable(object, (null|callable)): object $stage
     */
    public function pipe(callable|Stage $stage): Pipelayer;

    /**
     * Send a payload through the pipeline.
     */
    public function send(object $payload): object;
}
