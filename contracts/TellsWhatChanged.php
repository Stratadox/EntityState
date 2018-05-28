<?php

namespace Stratadox\EntityState;

/**
 * Lists the changes made to the entities.
 *
 * @author Stratadox
 */
interface TellsWhatChanged
{
    /**
     * Retrieves the entities that have been added.
     *
     * @return ListsEntityStates The added entities.
     */
    public function added(): ListsEntityStates;

    /**
     * Retrieves the entities that have been altered.
     *
     * @return ListsEntityStates The altered entities.
     */
    public function altered(): ListsEntityStates;

    /**
     * Retrieves the entities that have been removed.
     *
     * @return ListsEntityStates The removed entities.
     */
    public function removed(): ListsEntityStates;
}
