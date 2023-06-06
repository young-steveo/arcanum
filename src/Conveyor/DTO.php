<?php

declare(strict_types=1);

namespace Arcanum\Conveyor;

/**
 * DTO validates that an object is a simple DTO.
 */
final class DTO implements Validator
{
    /**
     * Validate a DTO.
     */
    public function validate(object $object): void
    {
        $image = new \ReflectionClass($object);
        $name = $image->getName();

        if (!$image->isInstantiable()) {
            throw new InvalidDTO("$name is not instantiable.");
        }

        if (!$image->isFinal()) {
            throw new InvalidDTO("$name is not final.");
        }

        $properties = $image->getProperties();

        foreach ($properties as $property) {
            if (!$property->isPublic()) {
                $propertyName = $property->getName();
                throw new InvalidDTO("$name has non-public property $propertyName.");
            }

            if ($property->isStatic()) {
                $propertyName = $property->getName();
                throw new InvalidDTO("$name has static property $propertyName.");
            }

            if (!$property->isReadOnly()) {
                $propertyName = $property->getName();
                throw new InvalidDTO("$name has non-read-only property $propertyName.");
            }
        }

        $methods = $image->getMethods();

        foreach ($methods as $method) {
            if ($method->isPublic()) {
                $methodName = $method->getName();
                throw new InvalidDTO("$name has public method $methodName.");
            }
        }
    }
}
