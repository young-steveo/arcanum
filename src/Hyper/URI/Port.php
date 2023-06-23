<?php

declare(strict_types=1);

namespace Arcanum\Hyper\URI;

final class Port implements \Stringable
{
    /**
     * Default ports for schemes.
     */
    public const DEFAULT_PORTS = [
        'http' => 80,
        'https' => 443,
        'ftp' => 21,
        'gopher' => 70,
        'nntp' => 119,
        'news' => 119,
        'telnet' => 23,
        'tn3270' => 23,
        'imap' => 143,
        'pop' => 110,
        'ldap' => 389,
    ];

    /**
     * Port.
     */
    public function __construct(private int|string|null $value)
    {
        if (is_string($value)) {
            $value = (int)$value;
        }
        if ($value !== null && ($value < 0 || $value > 65535)) {
            throw new \InvalidArgumentException('Port must be between 1 and 65535, or null.');
        }
    }

    /**
     * Port as a string.
     */
    public function __toString(): string
    {
        return $this->value === null ? '' : (string) $this->value;
    }
}
