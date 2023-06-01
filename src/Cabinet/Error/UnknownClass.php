<?php

declare(strict_types=1);

namespace Arcanum\Cabinet\Error;

final class UnknownClass extends Unresolvable
{
    public function __construct(string $message = '', int $code = 0, \Throwable $previous = null)
    {
        parent::__construct("Unknown Class: $message", $code, $previous);
    }
}
