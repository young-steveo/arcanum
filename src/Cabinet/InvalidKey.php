<?php

declare(strict_types=1);

namespace Arcanum\Cabinet;

use Psr\Container\ContainerExceptionInterface;

final class InvalidKey extends \InvalidArgumentException implements ContainerExceptionInterface
{
    public function __construct(string $message = '', int $code = 0, \Throwable $previous = null)
    {
        parent::__construct("Invalid Key: $message", $code, $previous);
    }
}
