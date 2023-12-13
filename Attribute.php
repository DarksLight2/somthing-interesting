<?php

namespace DarksLight2\RouteDoc;

use ReflectionAttribute;

class Attribute
{
    private string $for;
    private array $attributes;

    public function __construct(string $class) {
        $this->for = $class;
    }
    public static function for(string $class): static
    {
        return new self($class);
    }

    public function attributes(array $attributes): static
    {
        $this->attributes = $attributes;
        return $this;
    }

    /**
     * @throws \ReflectionException
     */
    private function resolveAttributes(array $attributes): array
    {
        $resolved = [];
        array_walk( $attributes, function ($attr) use (&$resolved) {
            $resolved[] = $this->resolveAttribute($attr);
        });

            return $resolved;
    }

    /**
     * @throws \ReflectionException
     */
    private function resolveAttribute(ReflectionAttribute $attribute): array
    {
        $args = $attribute->getArguments();

        $resolved = [];
        $parameters = (new \ReflectionMethod($attribute->getName(), '__construct'))
            ->getParameters();

        foreach ($parameters as $index => $parameter) {

            $resolved[] = new class($parameter->getName(), $args[$index])
            {
                public function __construct(
                    public readonly string $name,
                    public readonly mixed $value,
                ) { }
            };
        }

        return $resolved;
    }

    /**
     * @throws \ReflectionException
     */
    public function find(): array
    {
        $found = [];
        $reflection = new \ReflectionClass($this->for);
        foreach ($this->attributes as $attribute) {
            if(!empty($attr = $reflection->getAttributes($attribute))) {
                $found[] = $this->resolveAttributes($attr);
            }
        }

        return $found;
    }


}
