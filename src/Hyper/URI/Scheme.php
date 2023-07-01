<?php

declare(strict_types=1);

namespace Arcanum\Hyper\URI;

final class Scheme implements \Stringable
{
    /**
     * Scheme
     */
    public function __construct(private string $value)
    {
        $this->value = strtolower($value);
    }

    /**
     * Scheme as a string.
     */
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * Check if the scheme is a web scheme.
     */
    public function isWebScheme(): bool
    {
        return $this->value === 'http' || $this->value === 'https';
    }

    /**
     * Check if the scheme is empty.
     */
    public function isEmpty(): bool
    {
        return $this->value === '';
    }
}
