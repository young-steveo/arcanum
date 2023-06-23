<?php

declare(strict_types=1);

namespace Arcanum\Hyper\URI;

final class Scheme implements \Stringable
{
    public const HTTP = 'http';
    public const HTTPS = 'https';

    public function __construct(private string $value)
    {
        $this->value = strtolower($value);
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function isWebScheme(): bool
    {
        return $this->value === self::HTTP || $this->value === self::HTTPS;
    }

    public function isEmpty(): bool
    {
        return $this->value === '';
    }
}
