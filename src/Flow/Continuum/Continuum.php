<?php

declare(strict_types=1);

namespace Arcanum\Flow\Continuum;

class Continuum implements Continuation
{
    /**
     * @var array<int, Progression>
     */
    protected $stages = [];

    public function __construct(protected Advancer $advancer = new StandardAdvancer())
    {
    }

    /**
     * Add a progression to the continuum.
     */
    public function add(Progression $stage): Continuation
    {
        $this->stages[] = $stage;
        return $this;
    }

    /**
     * Send a payload through the advancer.
     */
    public function send(object $payload): object
    {
        return $this->advancer->advance($payload, ...$this->stages);
    }

    /**
     * Single stage in a pipeline.
     */
    public function __invoke(object $payload): object
    {
        return $this->send($payload);
    }
}
