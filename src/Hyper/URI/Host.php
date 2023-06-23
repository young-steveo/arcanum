<?php

declare(strict_types=1);

namespace Arcanum\Hyper\URI;

final class Host implements \Stringable
{
    public const DEFAULT_HTTP_HOST = 'localhost';

    public function __construct(private string $value)
    {
        $this->value = strtolower($value);
    }

    public static function localhost(): self
    {
        return new self(self::DEFAULT_HTTP_HOST);
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function isEmpty(): bool
    {
        return $this->value === '';
    }
}
