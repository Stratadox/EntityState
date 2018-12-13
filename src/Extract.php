<?php
declare(strict_types=1);

namespace Stratadox\EntityState;

use function array_map as extractWith;
use function array_merge as these;
use function get_class as classOfThe;
use function is_iterable as itIsACollection;
use function sprintf;
use Stratadox\EntityState\Internal\CollectionExtractor;
use Stratadox\EntityState\Internal\Extractor;
use Stratadox\EntityState\Internal\Name;
use Stratadox\EntityState\Internal\ObjectExtractor;
use Stratadox\EntityState\Internal\PropertyExtractor;
use Stratadox\EntityState\Internal\ReflectionProperties;
use Stratadox\EntityState\Internal\ScalarExtractor;
use Stratadox\EntityState\Internal\ShouldStringify;
use Stratadox\EntityState\Internal\Visited;
use Stratadox\IdentityMap\MapsObjectsByIdentity as Map;
use Stratadox\IdentityMap\NoSuchObject;

/**
 * Extract the state of the entities.
 *
 * @author Stratadox
 */
final class Extract implements ExtractsEntityState
{
    private $extractor;

    private function __construct(Extractor $extractor)
    {
        $this->extractor = $extractor;
    }

    /**
     * Produces a state extractor.
     *
     * @return ExtractsEntityState
     */
    public static function state(): ExtractsEntityState
    {
        return new Extract(
            PropertyExtractor::using(CollectionExtractor::withAlternative(
                ObjectExtractor::withAlternative(ScalarExtractor::asLastResort())
            ))
        );
    }

    /**
     * Produces a state extractor that converts certain types to string.
     *
     * @param mixed ...$classes
     * @return ExtractsEntityState
     */
    public static function stringifying(...$classes): ExtractsEntityState
    {
        return new Extract(
            PropertyExtractor::using(CollectionExtractor::withAlternative(
                ObjectExtractor::stringifyingWithAlternative(
                    ShouldStringify::these(...$classes),
                    ScalarExtractor::asLastResort()
                )
            ))
        );
    }

    /** @inheritdoc */
    public function from(Map $map): State
    {
        return $this->fromOnly($map, ...$map->objects());
    }

    /** @inheritdoc */
    public function fromOnly(Map $map, object ...$objects): State
    {
        return StateRepresentation::with(
            EntityStates::list(...extractWith(
                function (object $entity) use ($map): RepresentsEntity {
                    return $this->stateOfThe($entity, $map);
                }, $objects
            )),
            $map
        );
    }

    /** @throws NoSuchObject */
    private function stateOfThe(object $entity, Map $map): RepresentsEntity
    {
        $properties = [];
        if (itIsACollection($entity)) {
            $count = 0;
            foreach ($entity as $key => $item) {
                $properties[] = $this->extractor->extract(
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
            $properties[] = $this->extractor->extract(
                Name::fromReflection($property),
                $property->getValue($entity),
                $map,
                Visited::noneYet()
            );
        }
        return EntityState::ofThe(
            classOfThe($entity),
            $map->idOf($entity),
            empty($properties) ? PropertyStates::list() : PropertyStates::list(
                ...these(...$properties)
            )
        );
    }
}
