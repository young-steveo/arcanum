<?php

declare(strict_types=1);

namespace Arcanum\Cabinet\Error;

use Psr\Container\ContainerExceptionInterface;

class Unresolvable extends \InvalidArgumentException implements ContainerExceptionInterface
{
}
