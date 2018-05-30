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
     * Properties of value objects contained by the entity are considered part
     * of the state of the entity.
     *
     * When a property is contained in a value object, the property name of the
     * final value in the value object is used, prepended by the class name and
     * the property name in the original entity. For example, when a User entity
     * has a property `userName` that points to Name value object with a string
     * value in the property `name`, the result would look like this:
     * `Vendor\Users\Name:userName.name`
     *
     * Values enclosed in arrays or other collections are represented with
     * brackets and prepended with the collection type, for instance
     * `array:messages[1]`.
     * When values are contained in value objects which are in turn contained in
     * a collection of some sort, the value object's class name is prepended to
     * the the collection type, for example:
     * `Message:array:messages[0].title`
     * Or, if the collection is in a Messages class:
     * `Message:Messages:messages[0].title`
     *
     * The above rules can be combined, for instance:
     * `Message:array:messages[0].Recipient:array:recipients[1].name`
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
