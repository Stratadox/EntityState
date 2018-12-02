<?php

namespace Stratadox\EntityState;

use ArrayAccess;
use Countable;
use Iterator;
use Stratadox\IdentityMap\MapsObjectsByIdentity;

/**
 * Represents the state of the entities at a given moment.
 *
 * @author Stratadox
 */
interface State extends ArrayAccess, Countable, Iterator
{
    /**
     * Retrieves the identityMap of this state.
     *
     * @return MapsObjectsByIdentity The map of identity to object.
     */
    public function identityMap(): MapsObjectsByIdentity;

    /**
     * Retrieves the individual entity states.
     *
     * @return ListsEntityStates The collection of entity states.
     */
    public function entityStates(): ListsEntityStates;

    /**
     * Adds the additional state to the current state representation.
     *
     * @param State $additional The additional state to add.
     * @return State            The resulting (combined) state.
     */
    public function add(State $additional): State;

    /**
     * Collects the changes since the previous state.
     *
     * @param State $previous The collection to check against.
     * @return TellsWhatChanged    The changes since the previous state.
     */
    public function changesSince(State $previous): TellsWhatChanged;

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
}
