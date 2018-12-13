<?php declare(strict_types=1);

namespace Stratadox\EntityState\Internal;

use function array_merge as these;
use function assert;
use function is_object;
use Stratadox\EntityState\PropertyState;
use Stratadox\IdentityMap\MapsObjectsByIdentity as Map;
use Stratadox\IdentityMap\NoSuchObject;
use Stratadox\Specification\Contract\Satisfiable;

final class ObjectExtractor implements Extractor
{
    private $next;
    private $stringifier;

    private function __construct(Extractor $next, Satisfiable $constraint)
    {
        $this->next = $next;
        $this->stringifier = $constraint;
    }

    public static function withAlternative(Extractor $next): Extractor
    {
        return new self($next, Unsatisfiable::constraint());
    }

    public static function stringifyingWithAlternative(
        Satisfiable $constraint,
        Extractor $next
    ): Extractor {
        return new self($next, $constraint);
    }

    public function extract(
        Name $name,
        $object,
        Map $map,
        Visited $visited,
        Extractor $base = null
    ): array {
        if (!is_object($object)) {
            return $this->next->extract($name, $object, $map, $visited, $base);
        }
        assert($base !== null);
        return $this->objectState($name->for($object), $object, $map, $visited, $base);
    }

    /** @throws NoSuchObject */
    private function objectState(
        Name $name,
        $object,
        Map $map,
        Visited $visited,
        Extractor $base
    ): array {
        if ($this->stringifier->isSatisfiedBy($object)) {
            return [PropertyState::with((string) $name, (string) $object)];
        }
        if ($map->hasThe($object)) {
            return [PropertyState::with((string) $name, $map->idOf($object))];
        }
        $properties = [];
        foreach (ReflectionProperties::ofThe($object) as $property) {
            $properties[] = $base->extract(
                $name->forNested($property),
                $property->getValue($object),
                $map,
                $visited
            );
        }
        if (empty($properties)) {
            return [PropertyState::with((string) $name, null)];
        }
        return these(...$properties);
    }
}
