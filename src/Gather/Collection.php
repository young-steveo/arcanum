<?php

declare(strict_types=1);

namespace Arcanum\Gather;

/**
 * Collection represents a flexible key-value store.
 *
 * @extends \ArrayAccess<string, mixed>
 * @extends \IteratorAggregate<string, mixed>
 */
interface Collection extends
    \Psr\Container\ContainerInterface,
    \IteratorAggregate,
    \Countable,
    \ArrayAccess
{
}
