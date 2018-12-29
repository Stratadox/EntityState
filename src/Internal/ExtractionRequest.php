<?php declare(strict_types=1);

namespace Stratadox\EntityState\Internal;

use function in_array;
use function is_object;
use Stratadox\IdentityMap\MapsObjectsByIdentity as Map;
use Stratadox\IdentityMap\NoSuchObject;

final class ExtractionRequest
{
    private $value;
    private $owner;
    private $name;
    private $map;
    private $visited;
    private $newEntities;

    private function __construct(
        $value,
        object $owner,
        Name $name,
        Map $map,
        Visited $visited,
        object ...$newEntities
    ) {
        $this->value = $value;
        $this->owner = $owner;
        $this->name = $name;
        $this->map = $map;
        $this->visited = $visited;
        $this->newEntities = $newEntities;
    }

    public static function for($entity, Map $identityMap, object ...$newEntities): self
    {
        return new self(
            $entity,
            $entity,
            Name::start(),
            $identityMap,
            Visited::noneYet(),
            ...$newEntities
        );
    }

    public function value()
    {
        return $this->value;
    }

    public function isTheOwner(): bool
    {
        return $this->value === $this->owner;
    }

    public function objectName(): Name
    {
        return $this->name->for($this->value);
    }

    public function nameForCounting(): Name
    {
        return $this->name->toCount($this->value);
    }

    public function name(): Name
    {
        return $this->name;
    }

    public function pointsToAnotherEntity(): bool
    {
        return $this->pointsToAKnownEntity() && !$this->isTheOwner();
    }

    public function pointsToAKnownEntity(): bool
    {
        return is_object($this->value) && (
            $this->map->hasThe($this->value) ||
            in_array($this->value, $this->newEntities, true)
        );
    }

    /** @throws NoSuchObject */
    public function otherEntityId(): string
    {
        return $this->map->idOf($this->value);
    }

    public function isRecursive(): bool
    {
        return $this->visited->already($this->value);
    }

    public function visitedName(): string
    {
        return $this->visited->name($this->value);
    }

    public function withVisitation(): self
    {
        $new = clone $this;
        $new->visited = $this->visited->add($this->value, $this->name);
        return $new;
    }

    public function forCollectionItem(
        iterable $collection,
        string $key,
        $value
    ): self {
        $new = clone $this;
        $new->value = $value;
        $new->name = $this->name->forItem($collection, $key);
        return $new;
    }

    public function forProperty(ReflectionProperty $property): self
    {
        $new = clone $this;
        $new->value = $property->getValue($this->value);
        $new->name = $this->isTheOwner() ?
            $this->name->forReflected($property) :
            $this->name->for($this->value)->forReflected($property);
        return $new;
    }
}
