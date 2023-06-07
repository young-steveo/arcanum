<?php

declare(strict_types=1);

namespace Arcanum\Gather;

/**
 * Serializable covers several interfaces for serializing objects.
 */
interface Serializable extends \JsonSerializable, \Stringable
{
    /**
     * @return array<string, mixed>
     */
    public function __serialize(): array;

    /**
     * @param array<string, mixed> $data
     */
    public function __unserialize(array $data): void;
}
