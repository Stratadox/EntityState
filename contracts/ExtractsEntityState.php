<?php

namespace Stratadox\EntityState;

use Stratadox\IdentityMap\MapsObjectsByIdentity as Map;
use Stratadox\IdentityMap\NoSuchObject;

/**
 * Extracts entity state.
 *
 * @author Stratadox
 */
interface ExtractsEntityState
{
    /**
     * Makes the extraction process consider one or more types as entities.
     *
     * Related objects that are not found in the identity map are, by default,
     * considered as value objects. When new entities are created within the
     * boundaries of existing entities, however, they are not in the identity
     * map yet. To prevent such new entities from being treated like value
     * objects, the types of those entities can be configured using this method.
     *
     * @param DefinesEntityType ...$asEntities The types that are entities.
     * @return ExtractsEntityState             An entity state extractor that
     *                                         considers the types as entities.
     */
    public function consideringIt(
        DefinesEntityType ...$asEntities
    ): ExtractsEntityState;

    /**
     * Extract the state from the entities in the identity map.
     *
     * @param Map $identityMap The identity map to extract from.
     * @return State           The representation of the current state.
     * @throws NoSuchObject    When the identity map breaks its promises.
     */
    public function from(Map $identityMap): State;

    /**
     * Extract the state from some of the entities in the identity map.
     *
     * @param Map    $identityMap The identity map to use.
     * @param object ...$entities The entities whose state to get.
     * @return State              The entity representations.
     * @throws NoSuchObject       When one of the objects does not appear in the
     *                            identity map.
     */
    public function fromOnly(Map $identityMap, object ...$entities): State;
}
