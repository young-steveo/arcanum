<?php

declare(strict_types=1);

namespace Arcanum\Flow;

class Pipeline implements Pipelayer
{
    /**
     * @var array<int, Stage|callable(object, callable): object>
     */
    protected $stages = [];

    public function __construct(protected Processor $processor = new StandardProcessor())
    {
    }

    /**
     * Add a stage to the pipeline.
     *
     * @param Stage|callable(object, callable): object $stage
     */
    public function pipe(callable|Stage $stage): Pipeline
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
    public function __invoke(object $payload, callable $next = null): object|null
    {
        $result = $this->send($payload);
        if ($next) {
            return $next($result);
        }
        return $result;
    }
}
