<?php

namespace Stratadox\EntityState;

/**
 * Defines a type as entity.
 *
 * @author Stratadox
 */
interface DefinesEntityType
{
    /**
     * Checks whether this definition recognises the object as entity.
     *
     * @param object $potentialEntity The object to check.
     * @return bool                   Whether the object is recognised.
     */
    public function recognises(object $potentialEntity): bool;

    /**
     * Finds the identifier for the (new) entity.
     *
     * Returns null in case the identity is only assigned when saving the data
     * to the data store.
     *
     * @param object $recognisedEntity The object for which to find the id.
     * @return string                  The id as string or null when none found.
     */
    public function idFor(object $recognisedEntity): string;
}
