<?php
declare(strict_types=1);

namespace Stratadox\EntityState;

use function array_key_exists as alreadyAdded;
use function array_shift;
use function count;
use function get_class as classOfThe;
use Stratadox\EntityState\Internal\AcceptsNewEntities;
use Stratadox\EntityState\Internal\CollectionExtractor;
use Stratadox\EntityState\Internal\EntityReferenceExtractor;
use Stratadox\EntityState\Internal\ExtractionRequest;
use Stratadox\EntityState\Internal\Extractor;
use Stratadox\EntityState\Internal\NewEntityDetector;
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
final class Extract implements ExtractsEntityState, AcceptsNewEntities
{
    private $extractor;
    private $newEntities = [];

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
    public function consideringIt(
        DefinesEntityType ...$asEntities
    ): ExtractsEntityState {
        $new = clone $this;
        $new->extractor = NewEntityDetector::with($new, $this->extractor, ...$asEntities);
        return $new;
    }

    /** @inheritdoc */
    public function from(Map $map): State
    {
        return $this->fromOnly($map, ...$map->objects());
    }

    /** @inheritdoc */
    public function fromOnly(Map $map, object ...$objects): State
    {
        $states = [];
        foreach ($objects as $object) {
            $states[] = $this->stateOfThe($object, $map, $map->idOf($object));
        }
        while (count($this->newEntities)) {
            $newEntity = array_shift($this->newEntities);
            [$object, $id] = $newEntity;
            $map = $map->add($id, $object);
            $states[] = $this->stateOfThe($object, $map, $id);
        }
        return StateRepresentation::with(EntityStates::list(...$states), $map);
    }

    /**
     * @inheritdoc
     * @internal
     */
    public function addAsNewEntity(object $newEntity, string $id): void
    {
        $identifier = classOfThe($newEntity) . '~' . $id;
        if (!alreadyAdded($identifier, $this->newEntities)) {
            $this->newEntities[$identifier] = [$newEntity, $id];
        }
    }

    /** @throws NoSuchObject */
    private function stateOfThe(
        object $entity,
        Map $map,
        string $id
    ): RepresentsEntity {
        return EntityState::ofThe(classOfThe($entity), $id, PropertyStates::list(
            ...$this->extractor->extract(
                ExtractionRequest::for($entity, $map)
            )
        ));
    }
}
