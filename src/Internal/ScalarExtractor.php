<?php declare(strict_types=1);

namespace Stratadox\EntityState\Internal;

use Stratadox\EntityState\PropertyState;
use Stratadox\IdentityMap\MapsObjectsByIdentity as Map;

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
        Name $name,
        $value,
        Map $map,
        Visited $visited,
        Extractor $baseExtractor = null
    ): array {
        return [PropertyState::with((string) $name, $value)];
    }
}
