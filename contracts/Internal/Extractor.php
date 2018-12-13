<?php

namespace Stratadox\EntityState\Internal;

use Stratadox\EntityState\RepresentsProperty;
use Stratadox\IdentityMap\MapsObjectsByIdentity as Map;
use Stratadox\IdentityMap\NoSuchObject;

interface Extractor
{
    /**
     * @return RepresentsProperty[]
     * @throws NoSuchObject
     */
    public function extract(
        Name $name,
        $value,
        Map $map,
        Visited $visited,
        Extractor $baseExtractor = null
    ): array;
}
