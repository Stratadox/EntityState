<?php
declare(strict_types=1);

namespace Stratadox\EntityState;

use function array_map as extractWith;
use function array_merge as these;
use function get_class as classOfThe;
use function is_iterable as itIsACollection;
use function is_object as itIsAnObject;
use function sprintf;
use Stratadox\EntityState\Internal\Name;
use Stratadox\EntityState\Internal\ReflectionProperties;
use Stratadox\EntityState\Internal\ShouldStringify;
use Stratadox\EntityState\Internal\Unsatisfiable;
use Stratadox\EntityState\Internal\Visited;
use Stratadox\IdentityMap\MapsObjectsByIdentity as Map;
use Stratadox\IdentityMap\NoSuchObject;
use Stratadox\Specification\Contract\Satisfiable;

/**
 * Extract the state of the entities.
 *
 * @author Stratadox
 */
final class Extract implements ExtractsEntityState
{
    private $stringifier;

    private function __construct(Satisfiable $constraint)
    {
        $this->stringifier = $constraint;
    }

    /**
     * Produces a state extractor.
     *
     * @return ExtractsEntityState
     */
    public static function state(): ExtractsEntityState
    {
        return new Extract(Unsatisfiable::constraint());
    }

    /**
     * Produces a state extractor that converts certain types to string.
     *
     * @param mixed ...$classes
     * @return ExtractsEntityState
     */
    public static function stringifying(...$classes): ExtractsEntityState
    {
        return new Extract(ShouldStringify::these(...$classes));
    }

    /** @inheritdoc */
    public function from(Map $map): ListsEntityStates
    {
        return $this->fromOnly($map, ...$map->objects());
    }

    /** @inheritdoc */
    public function fromOnly(Map $map, object ...$objects): ListsEntityStates
    {
        return EntityStates::list(...extractWith(
            function (object $entity) use ($map): RepresentsEntity {
                return $this->stateOfThe($entity, $map);
            }, $objects
        ));
    }

    /** @throws NoSuchObject */
    private function stateOfThe(object $entity, Map $map): RepresentsEntity
    {
        $properties = [];
        if (itIsACollection($entity)) {
            $count = 0;
            foreach ($entity as $key => $item) {
                $properties[] = $this->extract(
                    Name::fromCollectionEntry($entity, (string) $key),
                    $item,
                    $map,
                    Visited::noneYet()
                );
                $count++;
            }
            $properties[] = [PropertyState::with(sprintf(
                'count(%s)',
                classOfThe($entity)
            ), $count)];
        }
        foreach (ReflectionProperties::ofThe($entity) as $property) {
            $properties[] = $this->extract(
                Name::fromReflection($property),
                $property->getValue($entity),
                $map,
                Visited::noneYet()
            );
        }
        if (empty($properties)) {
            return EntityState::ofThe(
                classOfThe($entity),
                $map->idOf($entity),
                PropertyStates::list()
            );
        }
        return EntityState::ofThe(
            classOfThe($entity),
            $map->idOf($entity),
            PropertyStates::list(...these(...$properties))
        );
    }

    /**
     * @return RepresentsProperty[]
     * @throws NoSuchObject
     */
    private function extract(
        Name $name,
        $value,
        Map $map,
        Visited $visited
    ): array {
        if ($visited->alreadyThe($value)) {
            return [PropertyState::with((string) $name, [$visited->name($value)])];
        }
        $visited = $visited->the($value, $name);
        if (itIsACollection($value)) {
            return $this->collectionState($name, $value, $map, $visited);
        }
        if (itIsAnObject($value)) {
            return $this->objectState($name->for($value), $value, $map, $visited);
        }
        return [PropertyState::with((string) $name, $value)];
    }

    /**
     * @return RepresentsProperty[]
     * @throws NoSuchObject
     */
    private function collectionState(
        Name $name,
        iterable $collection,
        Map $identityMap,
        Visited $visited
    ): array {
        $properties = [];
        $count = 0;
        foreach ($collection as $key => $value) {
            $properties[] = $this->extract(
                $name->forItem($collection, (string) $key),
                $value,
                $identityMap,
                $visited
            );
            $count++;
        }
        return these(
            [PropertyState::with((string) $name->toCount($collection), $count)],
            ...$properties
        );
    }

    /**
     * @return RepresentsProperty[]
     * @throws NoSuchObject
     */
    private function objectState(
        Name $name,
        object $object,
        Map $map,
        Visited $visited
    ): array {
        if ($this->stringifier->isSatisfiedBy($object)) {
            return [PropertyState::with((string) $name, (string) $object)];
        }
        if ($map->hasThe($object)) {
            return [PropertyState::with((string) $name, $map->idOf($object))];
        }
        return $this->properties($name, $object, $map, $visited);
    }

    /**
     * @return RepresentsProperty[]
     * @throws NoSuchObject
     */
    private function properties(
        Name $name,
        object $object,
        Map $map,
        Visited $visited
    ): array {
        $properties = [];
        foreach (ReflectionProperties::ofThe($object) as $property) {
            $properties[] = $this->extract(
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
