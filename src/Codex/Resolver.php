<?php

declare(strict_types=1);

namespace Arcanum\Codex;

use Arcanum\Codex\Event\CodexEvent;
use Psr\Container\ContainerInterface;

class Resolver implements ClassResolver
{
    /**
     * List of Codex\EventDispatcher instances.
     *
     * @var EventDispatcher[]
     */
    protected array $eventDispatchers = [];

    /**
     * Resolver uses a container to resolve dependencies.
     */
    private function __construct(
        private ContainerInterface $container
    ) {
    }

    /**
     * Create a new resolver.
     */
    public static function forContainer(ContainerInterface $container): self
    {
        return new self($container);
    }

    /**
     * Resolve a class
     *
     * @template T of object
     * @param class-string<T>|(callable(ContainerInterface): T) $className
     * @return T
     */
    public function resolve(string|callable $className, bool $isDependency = false): object
    {
        // To resolve a callable, we just call it with the container.
        if (is_callable($className)) {
            /** @var T */
            $instance = $className($this->container);
            $this->notify(new Event\ClassRequested(get_class($instance)));
            return $this->finalize($instance);
        }

        // notify listeners that a class was requested
        $this->notify(new Event\ClassRequested($className));

        // first try to get the class from the container
        if ($isDependency && $this->container->has($className)) {
            /** @var T */
            $instance = $this->container->get($className);
            return $this->finalize($instance);
        }

        $image = new \ReflectionClass($className);

        // If it is not instantiable, we cannot resolve it.
        if (!$image->isInstantiable()) {
            throw new Error\UnresolvableClass(message: $className);
        }

        $constructor = $image->getConstructor();

        // If it has no constructor, we can just instantiate it.
        if ($constructor === null) {
            return $this->finalize(new $className());
        }

        $parameters = $constructor->getParameters();

        // If it has a constructor, but no parameters, we can just instantiate it.
        if (count($parameters) === 0) {
            return $this->finalize(new $className());
        }

        // Otherwise, we need to resolve the parameters as dependencies.
        $dependencies = [];
        foreach ($parameters as $parameter) {
            $dependencyName = $this->getClassName($parameter);
            if ($dependencyName === null) {
                $type = $parameter->getType();
                if ($type !== null && $type instanceof \ReflectionUnionType) {
                    throw new Error\UnresolvableUnionType(message: $className);
                }
                $dependency = $this->resolvePrimitive($parameter);
            } else {
                $dependency = $this->resolveClass($parameter, $dependencyName);
            }

            // @todo: currently variadic constructors are not fully implemented, but we'll need
            // this check when it is.
            if ($parameter->isVariadic()) {
                $dependencies = array_merge($dependencies, (array)$dependency);
            } else {
                $dependencies[] = $dependency;
            }
        }

        /** @var T */
        $instance = $image->newInstanceArgs($dependencies);
        return $this->finalize($instance);
    }

    /**
     * Resolve a class with arguments.
     *
     * @template T of object
     * @param class-string<T> $className
     * @param class-string[] $arguments
     * @return T
     * @throws Error\UnresolvableClass
     */
    public function resolveWith(string $className, array $arguments): object
    {
        $image = new \ReflectionClass($className);

        // If it is not instantiable, we cannot resolve it.
        if (!$image->isInstantiable()) {
            throw new Error\UnresolvableClass(message: $className);
        }

        $constructor = $image->getConstructor();

        // If it has no constructor, we should just resolve it.
        if ($constructor === null) {
            return $this->resolve($className);
        }

        $parameters = $constructor->getParameters();

        // If it has a constructor, but no parameters, we should just resolve it.
        if (count($parameters) === 0) {
            return $this->resolve($className);
        }

        // Since we are going to resolve the dependencies, let's first
        // notify listeners that a class was requested.
        $this->notify(new Event\ClassRequested($className));

        // Now we can resolve the dependencies.
        $dependencies = [];
        foreach ($parameters as $index => $parameter) {
            $argument = $arguments[$index] ?? null;
            $dependencies[] = match (true) {
                $argument !== null => $this->resolve($argument, isDependency: true),
                $parameter->isDefaultValueAvailable() => $parameter->getDefaultValue(),
                default => throw new Error\UnresolvableClass(
                    "$className requires a parameter at index $index, but none was provided."
                )
            };
        }

        /** @var T */
        $instance = $image->newInstanceArgs($dependencies);
        return $this->finalize($instance);
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

    /**
     * @param class-string $name
     */
    protected function resolveClass(\ReflectionParameter $parameter, string $name): object
    {
        if ($parameter->isVariadic()) {
            throw new Error\UnresolvableClass(
                message: $parameter->getName() . " is variadic, and this is not implemented."
            );
        }

        try {
            return $this->resolve(
                className: $name,
                isDependency: true
            );
        } catch (Error\Unresolvable $e) {
            // handle the optional values on the parameter if it is not resolvable.
            if ($parameter->isDefaultValueAvailable()) {
                /** @var object */
                return $parameter->getDefaultValue();
            }
            throw $e;
        }
    }

    /**
     * Finalize an instance.
     *
     * if $instance is a Codex\EventDispatcher, it will be added to the list of event dispatchers.
     *
     * @template T of object
     * @param T $instance
     * @return T
     */
    protected function finalize(object $instance): object
    {
        if ($instance instanceof EventDispatcher) {
            $this->eventDispatchers[] = $instance;
        }

        foreach ($this->eventDispatchers as $dispatcher) {
            $dispatcher->dispatch(new Event\ClassResolved($instance));
        }

        return $instance;
    }

    protected function notify(CodexEvent $event): void
    {
        foreach ($this->eventDispatchers as $dispatcher) {
            $dispatcher->dispatch($event);
        }
    }
}
