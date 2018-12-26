<?php declare(strict_types=1);

namespace Stratadox\EntityState\Internal;

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

    private function __construct(
        $value,
        object $owner,
        Name $name,
        Map $map,
        Visited $visited
    ) {
        $this->value = $value;
        $this->owner = $owner;
        $this->name = $name;
        $this->map = $map;
        $this->visited = $visited;
    }

    public static function for($entity, Map $identityMap): self
    {
        return new self(
            $entity,
            $entity,
            Name::start(),
            $identityMap,
            Visited::noneYet()
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
        return is_object($this->value) && $this->map->hasThe($this->value);
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
        return new self(
            $this->value,
            $this->owner,
            $this->name,
            $this->map,
            $this->visited->add($this->value, $this->name)
        );
    }

    public function forCollectionItem(
        iterable $collection,
        string $key,
        $value
    ): self {
        return new self(
            $value,
            $this->owner,
            $this->name->forItem($collection, $key),
            $this->map,
            $this->visited
        );
    }

    public function forProperty(ReflectionProperty $property): self
    {
        $name = $this->isTheOwner() ?
            $this->name :
            $this->name->for($this->value);

        return new self(
            $property->getValue($this->value),
            $this->owner,
            $name->forReflected($property),
            $this->map,
            $this->visited
        );
    }
}
