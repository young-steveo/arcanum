<?php

declare(strict_types=1);

namespace Arcanum\Cabinet;

use Psr\Container\NotFoundExceptionInterface;

final class OutOfBounds extends \OutOfRangeException implements NotFoundExceptionInterface
{
    public function __construct(string $message = '', int $code = 0, \Throwable $previous = null)
    {
        parent::__construct("Out of Bounds: $message", $code, $previous);
    }
}
