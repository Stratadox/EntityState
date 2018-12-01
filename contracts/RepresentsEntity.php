<?php

namespace Stratadox\EntityState;

use Stratadox\EntityState\RepresentsEntity as TheOther;

/**
 * Represents an entity.
 *
 * @author Stratadox
 */
interface RepresentsEntity
{
    /**
     * Retrieves the class of the entity.
     *
     * @return string The fully qualified class name.
     */
    public function class(): string;

    /**
     * Retrieves the identifier of the entity.
     *
     * @return string The identifier.
     */
    public function id(): string;

    /**
     * Checks whether the entity has the same class and id as the other entity.
     *
     * @param TheOther $entity The entity to compare identifiers with.
     * @return bool            Whether the entities have the same class and id.
     */
    public function hasTheSameIdentityAs(TheOther $entity): bool;

    /**
     * Retrieves the state of the properties.
     *
     * Includes all the (nested) properties of its value objects.
     * Value objects' content is included as flattened list in order to make
     * differentiating the states of the entities easier.
     *
     * @return ListsPropertyStates A list of the properties of the entity.
     */
    public function properties(): ListsPropertyStates;

    /**
     * Checks whether this entity state is different from the other.
     *
     * @param RepresentsEntity $entity The other entity state to compare with,
     * @return bool                    Whether the two representations differ.
     */
    public function isDifferentFrom(TheOther $entity): bool;

    /**
     * Retrieves the subset of the entity state that differs from the state in
     * the collection.
     *
     * @param ListsEntityStates $entityStates The collection of original states.
     * @return RepresentsEntity               The representation of the subset
     *                                        of the entity state.
     */
    public function subsetThatDiffersFrom(
        ListsEntityStates $entityStates
    ): RepresentsEntity;

    /**
     * Something something mergeWith.
     *
     * @param RepresentsEntity $entityState
     * @return RepresentsEntity
     */
    public function mergeWith(TheOther $entityState): RepresentsEntity;
}
