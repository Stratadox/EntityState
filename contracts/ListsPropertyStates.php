<?php

namespace Stratadox\EntityState;

use InvalidArgumentException;
use Stratadox\Collection\Collection;

/**
 * Collection of property representations.
 *
 * Contains all properties of an entity, and all properties of all of its value
 * objects.
 *
 * @author Stratadox
 */
interface ListsPropertyStates extends Collection
{
    /**
     * Returns the current property state representation.
     *
     * @return RepresentsProperty The current property state.
     */
    public function current(): RepresentsProperty;

    /**
     * Returns the property state representation at a certain position.
     *
     * @param int $position       The offset at which the property is located.
     * @return RepresentsProperty The property state at that position.
     */
    public function offsetGet($position): RepresentsProperty;

    /**
     * Checks whether the collection contains the property.
     *
     * @param RepresentsProperty $property The property to check for.
     * @return bool                        Whether the property is contained in
     *                                     the collection.
     */
    public function contains(RepresentsProperty $property): bool;

    /**
     * Checks whether these properties are different from the other properties.
     *
     * @param ListsPropertyStates $otherProperties The properties to check
     *                                             against.
     * @return bool                                Whether the properties are
     *                                             different.
     */
    public function areDifferentFrom(ListsPropertyStates $otherProperties): bool;

    /**
     * Overwrites or appends the new properties into the current properties.
     *
     * @param ListsPropertyStates $newProperties The property states to add.
     * @return ListsPropertyStates               The combination of the states.
     */
    public function merge(ListsPropertyStates $newProperties): ListsPropertyStates;

    /**
     * Checks whether the collection has a property with this name.
     *
     * @param string $propertyName The name to check for.
     * @return bool                Whether there is a property with this name.
     */
    public function hasOneNamed(string $propertyName): bool;

    /**
     * Finds the property state representation with this name.
     *
     * @param string $propertyName      The name to find the property state for.
     * @return RepresentsProperty       The property state representation.
     * @throws InvalidArgumentException When there is no such property.
     */
    public function theOneNamed(string $propertyName): RepresentsProperty;
}
