<?php

namespace Stratadox\EntityState\Internal;

/**
 * Interface that allows mutation of the state extractor by adding objects to a
 * list of new entities.
 *
 * Only to be used by internal classes when recognising new entities during the
 * extraction process.
 *
 * @author Stratadox
 */
interface AcceptsNewEntities
{
    /**
     * Marks an object as new entity.
     *
     * @param object $newEntity The object to mark as entity.
     * @param null|string       The id of the entity.
     * @internal
     * @todo get rid of the bidirectional relationship and extractor mutability
     */
    public function addAsNewEntity(object $newEntity, ?string $id): void;
}
