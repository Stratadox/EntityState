<?php

namespace Stratadox\EntityState\Internal;

use Stratadox\EntityState\RepresentsProperty;
use Stratadox\IdentityMap\NoSuchObject;

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
