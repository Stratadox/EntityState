<?php declare(strict_types=1);

namespace Stratadox\EntityState\Internal;

use Stratadox\EntityState\PropertyState;

final class ScalarExtractor implements Extractor
{
    private function __construct()
    {
    }

    public static function asLastResort(): Extractor
    {
        return new self();
    }

    public function extract(
        ExtractionRequest $request,
        Extractor $baseExtractor = null
    ): array {
        return [PropertyState::with(
            (string) $request->name(),
            $request->value()
        )];
    }
}
