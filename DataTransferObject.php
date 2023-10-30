<?php

namespace App\Supports;

use ReflectionClass;
use Illuminate\Contracts\Support\Arrayable;

abstract class DataTransferObject implements Arrayable
{
    public function toArray(): array
    {
        $ref = new ReflectionClass(static::class);
        $data = [];

        foreach ($ref->getProperties() as $property) {
            if($this->{$property->getName()} instanceof DataTransferObject) {
                $data[$property->getName()] = $this->{$property->getName()}->toArray();
                continue;
            }
            $data[$property->getName()] = $this->{$property->getName()};
        }

        return $data;
    }

    public static function fromArray(array $array): static
    {
        $ref = new ReflectionClass(static::class);

        foreach ($ref->getProperties() as $property) {
            $type_name = $property->getType()->getName();
            if(class_exists($type_name) && method_exists($type_name, 'fromArray')) {
                $array[$property->getName()] = $type_name::fromArray($array[$property->getName()]);
            }
        }

        return new static(...$array);
    }
}
