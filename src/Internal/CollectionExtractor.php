<?php declare(strict_types=1);

namespace Stratadox\EntityState\Internal;

use function array_merge as these;
use function assert;
use function is_iterable;
use Stratadox\EntityState\PropertyState;
use Stratadox\IdentityMap\MapsObjectsByIdentity as Map;

final class CollectionExtractor implements Extractor
{
    private $next;

    private function __construct(Extractor $next)
    {
        $this->next = $next;
    }

    public static function withAlternative(Extractor $next): Extractor
    {
        return new self($next);
    }

    public function extract(
        Name $name,
        $collection,
        Map $map,
        Visited $visited,
        Extractor $base = null
    ): array {
        if (!is_iterable($collection)) {
            return $this->next->extract($name, $collection, $map, $visited, $base);
        }
        assert($base !== null);
        $properties = [];
        $count = 0;
        foreach ($collection as $key => $value) {
            $properties[] = $base->extract(
                $name->forItem($collection, (string) $key),
                $value,
                $map,
                $visited,
                $base
            );
            $count++;
        }
        return these(
            [PropertyState::with((string) $name->toCount($collection), $count)],
            ...$properties
        );
    }
}
