<?php

namespace Stratadox\EntityState;

use Stratadox\IdentityMap\MapsObjectsByIdentity;
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
     * @param MapsObjectsByIdentity $identityMap The identity map to extract from.
     * @return ListsEntityStates                 The entity representations.
     * @throws NoSuchObject                      When the identity map breaks its
     *                                           promises.
     */
    public function from(MapsObjectsByIdentity $identityMap): ListsEntityStates;

    /**
     * Extract the state from the entities in the identity map.
     *
     * @param MapsObjectsByIdentity $identityMap The identity map to use.
     * @param object                ...$entities The entities whose state to get.
     * @return ListsEntityStates                 The entity representations.
     * @throws NoSuchObject                      When one of the objects does not
     *                                           appear in the identity map.
     */
    public function fromOnly(MapsObjectsByIdentity $identityMap, object ...$entities): ListsEntityStates;
}
