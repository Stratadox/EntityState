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
