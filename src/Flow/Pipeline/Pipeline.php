<?php

declare(strict_types=1);

namespace Arcanum\Flow\Pipeline;

use Arcanum\Flow\Stage;

class Pipeline implements Pipelayer
{
    /**
     * @var array<int, Stage|callable(object): (object|null)>
     */
    protected $stages = [];

    public function __construct(protected Processor $processor = new StandardProcessor())
    {
    }

    /**
     * Add a stage to the pipeline.
     *
     * @param Stage|callable(object): (object|null) $stage
     */
    public function pipe(callable|Stage $stage): Pipelayer
    {
        $this->stages[] = $stage;
        return $this;
    }

    /**
     * Send a payload through the pipeline.
     */
    public function send(object $payload): object
    {
        return $this->processor->process($payload, ...$this->stages);
    }

    /**
     * Single stage in a pipeline.
     */
    public function __invoke(object $payload): object|null
    {
        return $this->send($payload);
    }
}
