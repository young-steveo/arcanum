<?php

declare(strict_types=1);

namespace Arcanum\Flow\Pipeline;

use Arcanum\Flow\Sender;
use Arcanum\Flow\Stage;

interface Pipelayer extends Sender
{
    /**
     * Add a stage to the pipeline.
     *
     * @param Stage|callable(object): object $stage
     */
    public function pipe(callable|Stage $stage): Pipelayer;
}
