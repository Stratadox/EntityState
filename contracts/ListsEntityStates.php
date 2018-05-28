<?php

namespace Stratadox\EntityState;

use Stratadox\Collection\Collection;

/**
 * Collection of entity representations.
 *
 * @author Stratadox
 */
interface ListsEntityStates extends Collection
{
    /**
     * Returns the current entity state representation.
     *
     * @return RepresentsEntity The current entity state.
     */
    public function current(): RepresentsEntity;

    /**
     * Returns the entity state representation at a certain position.
     *
     * @param int $position     The offset at which the entity state is located.
     * @return RepresentsEntity The entity state at that position.
     */
    public function offsetGet($position): RepresentsEntity;

    /**
     * Adds the entity state to the collection of entity states.
     *
     * @param RepresentsEntity $entityState The entity state to add.
     * @return ListsEntityStates            The updated entity states.
     */
    public function add(RepresentsEntity $entityState): ListsEntityStates;

    /**
     * Checks whether the collection has the entity with this class and id.
     *
     * @param RepresentsEntity $entity The entity representation to check for.
     * @return bool                    Whether the entity is in the collection.
     */
    public function hasThe(RepresentsEntity $entity): bool;

    /**
     * Checks whether the entity is in the collection with a different state.
     *
     * @param RepresentsEntity $state  The entity to check for differences.
     * @return bool                    Whether the entity is present and its
     *                                 state is different in the collection.
     */
    public function hasADifferent(RepresentsEntity $state): bool;

    /**
     * Collects the entities that are not in the other collection.
     *
     * @param ListsEntityStates $otherEntities The collection to check against.
     * @return ListsEntityStates               The entity representations that
     *                                         are not in the other collection.
     */
    public function entitiesThatAreNotIn(
        ListsEntityStates $otherEntities
    ): ListsEntityStates;

    /**
     * Collects the entity subsets that are different in the other collection.
     *
     * @param ListsEntityStates $otherEntities The collection to check against.
     * @return ListsEntityStates               The entity representations that
     *                                         differ in the other collection.
     */
    public function entityStateThatDiffersFrom(
        ListsEntityStates $otherEntities
    ): ListsEntityStates;

    /**
     * Collects the changes since the previous state.
     *
     * @param ListsEntityStates $previousState The collection to check against.
     * @return TellsWhatChanged                The changes since the previous
     *                                         state.
     */
    public function changesSince(
        ListsEntityStates $previousState
    ): TellsWhatChanged;
}
