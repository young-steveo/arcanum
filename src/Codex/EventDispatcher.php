<?php

declare(strict_types=1);

namespace Arcanum\Codex;

/**
 * If Arcanum\Codex\EventDispatcher is resolved by Codex,
 * it will retain a reference to it and dispatch events to it.
 */
interface EventDispatcher extends \Psr\EventDispatcher\EventDispatcherInterface
{
}
