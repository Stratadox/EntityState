<?php
declare(strict_types=1);

namespace Stratadox\EntityState;

use function array_map as extractWith;
use function get_class as classOfThe;
use Stratadox\EntityState\Internal\CollectionExtractor;
use Stratadox\EntityState\Internal\EntityReferenceExtractor;
use Stratadox\EntityState\Internal\ExtractionRequest;
use Stratadox\EntityState\Internal\Extractor;
use Stratadox\EntityState\Internal\ObjectExtractor;
use Stratadox\EntityState\Internal\PropertyExtractor;
use Stratadox\EntityState\Internal\ScalarExtractor;
use Stratadox\EntityState\Internal\ShouldStringify;
use Stratadox\EntityState\Internal\Stringifier;
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
            PropertyExtractor::using(
                CollectionExtractor::withAlternative(
                    EntityReferenceExtractor::withAlternative(
                        ObjectExtractor::withAlternative(
                            ScalarExtractor::asLastResort()
                        )
                    )
                )
            )
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
            PropertyExtractor::using(
                CollectionExtractor::withAlternative(
                    Stringifier::withCondition(
                        ShouldStringify::these(...$classes),
                        EntityReferenceExtractor::withAlternative(
                            ObjectExtractor::withAlternative(
                                ScalarExtractor::asLastResort()
                            )
                        )
                    )
                )
            )
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
        return EntityState::ofThe(
            classOfThe($entity),
            $map->idOf($entity),
            PropertyStates::list(...$this->extractor->extract(
                ExtractionRequest::for($entity, $map)
            ))
        );
    }
}
