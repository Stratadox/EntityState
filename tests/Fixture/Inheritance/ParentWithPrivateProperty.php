<?php declare(strict_types=1);

namespace Stratadox\EntityState\Test\Fixture\Inheritance;

abstract class ParentWithPrivateProperty
{
    private $property;

    public function __construct(string $property)
    {
        $this->property = $property;
    }

    public function property(): string
    {
        return $this->property;
    }
}
