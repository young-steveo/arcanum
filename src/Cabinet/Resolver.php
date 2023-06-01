<?php

declare(strict_types=1);

namespace Arcanum\Cabinet;

class Resolver
{
    private function __construct(
        private Container $container
    ) {
    }

    public static function forContainer(Container $container): self
    {
        return new self($container);
    }

    /**
     * Resolve a service from the container.
     *
     * @template T of object
     * @param class-string<T>|callable(Container): T $serviceName
     * @return T
     */
    public function resolve(string|callable $serviceName): mixed
    {
        // To resolve a callable, we just call it with the container.
        if (is_callable($serviceName)) {
            return $serviceName($this->container);
        }

        $image = new \ReflectionClass($serviceName);

        // If it is not instantiable, we cannot resolve it.
        if (!$image->isInstantiable()) {
            throw new Error\UnresolvableClass(message: $serviceName);
        }

        $constructor = $image->getConstructor();

        // If it has no constructor, we can just instantiate it.
        if ($constructor === null) {
            return new $serviceName();
        }

        $parameters = $constructor->getParameters();

        // If it has a constructor, but no parameters, we can just instantiate it.
        if (count($parameters) === 0) {
            return new $serviceName();
        }

        // Otherwise, we need to resolve the parameters as dependencies.
        $dependencies = [];
        foreach ($parameters as $parameter) {
            $dependencyName = $this->getClassName($parameter);
            if ($dependencyName === null) {
                $type = $parameter->getType();
                if ($type !== null && $type instanceof \ReflectionUnionType) {
                    throw new Error\UnresolvableUnionType(message: $serviceName);
                }
                $dependency = $this->resolvePrimitive($parameter);
            } else {
                $dependency = $this->resolveClass($parameter);
            }

            // @todo: currently variadic constructors are not fully implemented, but we'll need
            // this check when it is.
            if ($parameter->isVariadic()) {
                $dependencies = array_merge($dependencies, (array)$dependency);
            } else {
                $dependencies[] = $dependency;
            }
        }

        return $image->newInstanceArgs($dependencies);
    }

    /**
     * Get the class name of the parameter, or null if it is not a class.
     *
     * @return class-string|null
     */
    protected function getClassName(\ReflectionParameter $parameter): string|null
    {
        $type = $parameter->getType();

        // if it has no type, we cannot get its name.
        if ($type === null) {
            return null;
        }

        // if it is not a named type, we cannot get its name.
        if (!$type instanceof \ReflectionNamedType) {
            return null;
        }

        // if it is a built-in type, we cannot get its name.
        if ($type->isBuiltin()) {
            return null;
        }

        $name = $type->getName();

        /**
         * $class here cannot be null because we already checked
         * that it is not a built-in type.
         *
         * @var \ReflectionClass<object> $class
         */
        $class = $parameter->getDeclaringClass();

        if ($name === 'parent') {

            /**
             * $parent here cannot be false because we already checked
             * if the parent keyword is used without extending anything,
             * it would be a fatal error.
             *
             * @var \ReflectionClass<object> $parent
             */
            $parent = $class->getParentClass();
            return $parent->getName();
        }

        /** @var class-string $name */
        return $name;
    }

    protected function resolvePrimitive(\ReflectionParameter $parameter): mixed
    {
        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        if ($parameter->isVariadic()) {
            return [];
        }

        throw new Error\UnresolvablePrimitive(message: $parameter->getName());
    }

    protected function resolveClass(\ReflectionParameter $parameter): mixed
    {
        if ($parameter->isVariadic()) {
            throw new Error\UnresolvableClass(
                message: $parameter->getName() . " is variadic, and this is not implemented."
            );
        }

        try {
            $name = $this->getClassName($parameter);
            if ($name === null) {
                throw new Error\UnknownClass(message: $parameter->getName());
            }
            return $this->resolve($name);
        } catch (Error\Unresolvable $e) {
            // handle the optional values on the parameter if it is not resolvable.
            if ($parameter->isDefaultValueAvailable()) {
                return $parameter->getDefaultValue();
            }
            throw $e;
        }
    }
}
