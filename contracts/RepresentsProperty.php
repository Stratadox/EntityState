<?php

namespace Stratadox\EntityState;

/**
 * Represents a property.
 *
 * @author Stratadox
 */
interface RepresentsProperty
{
    /**
     * Retrieves the property name.
     *
     * @todo update doc
     *
     * For properties of properties, ie. associated value object, the property
     * name is separated by dots.
     * For example, when the Customer entity gets a new Balance value object,
     * which still has the same currency but a different value, the change is in
     * `Balance:balance.value` of the Customer.
     * Values enclosed in arrays or other collections are represented with
     * brackets, for instance `messages[1]`.
     * These notations can be combined into results like: `messages[1].title`.
     *
     * @return string The property path.
     */
    public function name(): string;

    /**
     * Contains the value for the property.
     *
     * When the property refers to another entity, the string representation of
     * that entity's id is returned.
     *
     * If the value is recursive, an array with a single string value is
     * returned. This string value contains the property name it points to.
     *
     * In other cases, the (primitive) value of the property is returned.
     *
     * @return null|string|int|float|bool|array The value of the property.
     */
    public function value();

    /**
     * Checks whether the property is the same as the other property.
     *
     * @param RepresentsProperty $otherProperty The property to check against.
     * @return bool                             Whether the properties are the
     *                                          same.
     */
    public function isSameAs(RepresentsProperty $otherProperty): bool;

    /**
     * Checks whether the property is different in the collection.
     *
     * @param ListsPropertyStates $otherProperties The collection to check
     *                                             against.
     * @return bool                                Whether the property is
     *                                             different in the collection.
     */
    public function isDifferentInThe(ListsPropertyStates $otherProperties): bool;
}
