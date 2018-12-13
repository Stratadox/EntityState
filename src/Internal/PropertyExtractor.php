<?php declare(strict_types=1);

namespace Stratadox\EntityState\Internal;

use Stratadox\EntityState\PropertyState;
use Stratadox\IdentityMap\MapsObjectsByIdentity as Map;

final class PropertyExtractor implements Extractor
{
    private $next;

    private function __construct(Extractor $next)
    {
        $this->next = $next;
    }

    public static function using(Extractor $next): self
    {
        return new self($next);
    }

    public function extract(
        Name $name,
        $value,
        Map $map,
        Visited $visited,
        Extractor $baseExtractor = null
    ): array {
        if ($visited->alreadyThe($value)) {
            return [PropertyState::with((string) $name, [$visited->name($value)])];
        }
        return $this->next->extract(
            $name,
            $value,
            $map,
            $visited->the($value, $name),
            $this
        );
    }
}
