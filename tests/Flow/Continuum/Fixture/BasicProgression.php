<?php

declare(strict_types=1);

namespace Arcanum\Test\Flow\Continuum\Fixture;

use Arcanum\Flow\Continuum\Progression;

final class BasicProgression implements Progression
{
    private \Closure $stage;
    public function __construct(\Closure $stage)
    {
        $this->stage = $stage;
    }

    public static function fromClosure(\Closure $stage): self
    {
        return new self($stage);
    }

    /**
     * Single stage in a continuum.
     *
     * @param callable(): void $next
     */
    public function __invoke(object $payload, callable $next): void
    {
        ($this->stage)($payload, $next);
    }
}
