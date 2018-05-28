<?php
declare(strict_types=1);

namespace Stratadox\EntityState;

use InvalidArgumentException;
use Stratadox\ImmutableCollection\ImmutableCollection;

/**
 * Collection of property representations.
 *
 * @author Stratadox
 */
final class PropertyStates extends ImmutableCollection implements ListsPropertyStates
{
    private function __construct(RepresentsProperty ...$properties)
    {
        parent::__construct(...$properties);
    }

    /**
     * Packs a number of properties in a collection.
     *
     * @param RepresentsProperty ...$properties
     * @return ListsPropertyStates
     */
    public static function list(RepresentsProperty ...$properties): ListsPropertyStates
    {
        return new self(...$properties);
    }

    /** @inheritdoc */
    public function current(): RepresentsProperty
    {
        return parent::current();
    }

    /** @inheritdoc */
    public function offsetGet($position): RepresentsProperty
    {
        return parent::offsetGet($position);
    }

    /** @inheritdoc */
    public function contains(RepresentsProperty $property): bool
    {
        foreach ($this as $candidate) {
            if ($candidate->isSameAs($property)) {
                return true;
            }
        }
        return false;
    }

    /** @inheritdoc */
    public function areDifferentFrom(ListsPropertyStates $otherProperties): bool
    {
        foreach ($otherProperties as $property) {
            if (!$this->contains($property)) {
                return true;
            }
        }
        return false;
    }

    /** @inheritdoc */
    public function merge(ListsPropertyStates $newProperties): ListsPropertyStates
    {
        $properties = $this->items();
        foreach ($newProperties as $theNewProperty) {
            $properties[$this->positionOf($theNewProperty)] = $theNewProperty;
        }
        return PropertyStates::list(...$properties);
    }

    /** @inheritdoc */
    public function hasOneNamed(string $propertyName): bool
    {
        foreach ($this as $property) {
            if ($property->name() === $propertyName) {
                return true;
            }
        }
        return false;
    }

    /** @inheritdoc */
    public function theOneNamed(string $propertyName): RepresentsProperty
    {
        foreach ($this as $property) {
            if ($property->name() === $propertyName) {
                return $property;
            }
        }
        throw new InvalidArgumentException("No such property: `$propertyName`");
    }

    private function positionOf(RepresentsProperty $theNewProperty): int
    {
        foreach ($this as $i => $property) {
            if ($property->name() === $theNewProperty->name()) {
                return $i;
            }
        }
        return $this->count();
    }
}
