<?php

declare(strict_types=1);

namespace Arcanum\Hyper\URI;

final class Host implements \Stringable
{
    /**
     * Host
     */
    public function __construct(private string $value)
    {
        $this->value = strtolower($value);
    }

    /**
     * Create a localhost host.
     */
    public static function localhost(): self
    {
        return new self('localhost');
    }

    /**
     * Host is a string.
     */
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * Check if the host is empty.
     */
    public function isEmpty(): bool
    {
        return $this->value === '';
    }
}
