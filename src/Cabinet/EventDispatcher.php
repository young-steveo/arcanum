<?php

declare(strict_types=1);

namespace Arcanum\Cabinet;

/**
 * If Arcanum\Cabinet\EventDispatcher is registered in the container, the
 * container will dispatch resolving events to it.
 */
interface EventDispatcher extends \Psr\EventDispatcher\EventDispatcherInterface
{
}
