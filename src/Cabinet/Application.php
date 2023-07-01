<?php

declare(strict_types=1);

namespace Arcanum\Cabinet;

use Psr\Container\ContainerInterface;

/**
 * @extends \ArrayAccess<class-string, mixed>
 */
interface Application extends
    \ArrayAccess,
    ContainerInterface,
    DecoratorRegistry,
    FactoryRegistry,
    InstanceRegistry,
    MiddlewareRegistry,
    PrototypeRegistry,
    ProviderRegistry,
    ServiceRegistry
{
}
