<?php declare(strict_types=1);

namespace Stratadox\EntityState\Test\Fixture\Inheritance;

final class ChildWithPrivateProperty extends ParentWithPrivateProperty
{
    private $property;

    public function __construct(string $parentProperty, string $myProperty)
    {
        $this->property = $myProperty;
        parent::__construct($parentProperty);
    }

    public function property(): string
    {
        return $this->property . ' / ' . parent::property();
    }
}
