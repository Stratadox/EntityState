<?php
declare(strict_types=1);

namespace Stratadox\EntityState;

use function count;
use Stratadox\ImmutableCollection\ImmutableCollection;

/**
 * Collection of entity representations.
 *
 * @author Stratadox
 */
final class EntityStates extends ImmutableCollection implements ListsEntityStates
{
    private function __construct(RepresentsEntity ...$entities)
    {
        parent::__construct(...$entities);
    }

    /**
     * Packs a number of entities in a collection.
     *
     * @param RepresentsEntity ...$entities The entities to collect.
     * @return ListsEntityStates            The collection of entities.
     */
    public static function list(RepresentsEntity ...$entities): ListsEntityStates
    {
        return new EntityStates(...$entities);
    }

    /** @inheritdoc */
    public function current(): RepresentsEntity
    {
        return parent::current();
    }

    /** @inheritdoc */
    public function offsetGet($position): RepresentsEntity
    {
        return parent::offsetGet($position);
    }

    /** @inheritdoc */
    public function add(RepresentsEntity $entityState): ListsEntityStates
    {
        if ($this->hasThe($entityState)) {
            return $this->mergeThe($entityState);
        }

        $entityStates = $this->items();
        $entityStates[] = $entityState;
        return EntityStates::list(...$entityStates);
    }

    /** @inheritdoc */
    public function hasThe(RepresentsEntity $entity): bool
    {
        foreach ($this as $candidate) {
            if ($candidate->hasTheSameIdentityAs($entity)) {
                return true;
            }
        }
        return false;
    }

    /** @inheritdoc */
    public function hasADifferent(RepresentsEntity $entity): bool
    {
        foreach ($this as $candidate) {
            if ($candidate->hasTheSameIdentityAs($entity)) {
                return $candidate->isDifferentFrom($entity);
            }
        }
        return false;
    }

    /** @inheritdoc */
    public function entitiesThatAreNotIn(ListsEntityStates $otherEntities): ListsEntityStates
    {
        $entities = [];
        foreach ($this as $entity) {
            if (!$otherEntities->hasThe($entity)) {
                $entities[] = $entity;
            }
        }
        return EntityStates::list(...$entities);
    }

    /** @inheritdoc */
    public function entityStateThatDiffersFrom(
        ListsEntityStates $otherState
    ): ListsEntityStates {
        $entities = [];
        foreach ($this as $state) {
            if ($otherState->hasADifferent($state)) {
                $entities[] = $state->subsetThatDiffersFrom($otherState);
            }
        }
        return EntityStates::list(...$entities);
    }

    /** @inheritdoc */
    public function changesSince(ListsEntityStates $previousState): TellsWhatChanged
    {
        return Changes::wereMade(
            $this->entitiesThatAreNotIn($previousState),
            $this->entityStateThatDiffersFrom($previousState),
            $previousState->entitiesThatAreNotIn($this)
        );
    }

    private function mergeThe(RepresentsEntity $newState): ListsEntityStates
    {
        $entities = $this->items();
        foreach ($this as $i => $entityState) {
            if ($entityState->hasTheSameIdentityAs($newState)) {
                $entities[$i] = $entityState->mergeWith($newState);
            }
        }
        return EntityStates::list(...$entities);
    }
}
