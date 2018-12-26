<?php

namespace Stratadox\EntityState\Internal;

interface AcceptsNewEntities
{
    /**
     * Marks an object as new entity.
     *
     * @param object $newEntity The object to mark as entity.
     * @param string            The (potentially temporary) id of the entity.
     * @internal
     * @todo get rid of the bidirectional relationship and extractor mutability
     */
    public function addAsNewEntity(object $newEntity, string $id): void;
}
