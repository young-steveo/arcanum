<?php

declare(strict_types=1);

namespace Arcanum\Hyper;

enum Version: string
{
    case v09 = '0.9';
    case v10 = '1.0';
    case v11 = '1.1';
    case v20 = '2.0';

    public static function fromString(string $version): self
    {
        return match ($version) {
            '0.9' => self::v09,
            '1.0' => self::v10,
            '1.1' => self::v11,
            '2.0' => self::v20,
            default => throw new \InvalidArgumentException('Invalid HTTP version'),
        };
    }
}
