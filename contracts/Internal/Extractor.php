<?php

namespace Stratadox\EntityState\Internal;

use Stratadox\EntityState\RepresentsProperty;
use Stratadox\IdentityMap\NoSuchObject;

/**
 * Extractor mechanism, to be part of a chain-of-responsibility for extracting
 * entity states.
 *
 * @author Stratadox
 */
interface Extractor
{
    /**
     * @return RepresentsProperty[]
     * @throws NoSuchObject
     */
    public function extract(
        ExtractionRequest $request,
        Extractor $baseExtractor = null
    ): array;
}
