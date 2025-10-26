<?php

declare(strict_types=1);

namespace App;

use ReflectionClass;

class Container
{
    private array $instances = [];

    public function set(string $key, callable $resolver): void
    {
        $this->instances[$key] = $resolver;
    }

    /**
     * @template T
     * @param class-string<T> $key
     * @return T
     */
    public function get(string $key): object
    {
        if (isset($this->instances[$key])) {
            return $this->instances[$key]();
        }

        $reflection = new ReflectionClass($key);
        $constructor = $reflection->getConstructor();
        $parameters = $constructor?->getParameters() ?? [];
        $dependencies = [];

        foreach ($parameters as $param) {
            $dependency = $param->getType()?->getName();
            if (!is_null($dependency)) {
                $dependencies[] = $this->get($dependency);
            }
        }

        $instance = fn() => $reflection->newInstanceArgs($dependencies);

        $this->set($key, $instance);

        return $instance();
    }
}