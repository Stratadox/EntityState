<?php declare(strict_types=1);

namespace Stratadox\EntityState;

use Stratadox\IdentityMap\MapsObjectsByIdentity;

final class StateRepresentation implements State
{
    private $entityStates;
    private $map;

    public function __construct(
        ListsEntityStates $entities,
        MapsObjectsByIdentity $map
    ) {
        $this->entityStates = $entities;
        $this->map = $map;
    }

    public static function with(
        ListsEntityStates $entities,
        MapsObjectsByIdentity $map
    ): State {
        return new self($entities, $map);
    }

    public function entityStates(): ListsEntityStates
    {
        return $this->entityStates;
    }

    public function identityMap(): MapsObjectsByIdentity
    {
        return $this->map;
    }

    public function add(State $additional): State
    {
        $entityStates = $this->entityStates;
        $map = $this->map;
        foreach ($additional->entityStates() as $entityState) {
            $entityStates = $entityStates->add($entityState);
            $map = $this->addToMapIfNew(
                $map,
                $additional->identityMap(),
                $entityState->class(),
                $entityState->id()
            );
        }
        return new self($entityStates, $map);
    }

    public function changesSince(State $previous): TellsWhatChanged
    {
        return $this->entityStates->changesSince($previous->entityStates());
    }

    public function offsetExists($offset): bool
    {
        return $this->entityStates->offsetExists($offset);
    }

    public function offsetGet($offset): RepresentsEntity
    {
        return $this->entityStates->offsetGet($offset);
    }

    public function offsetSet($offset, $value): void
    {
        $this->entityStates->offsetSet($offset, $value);
    }

    public function offsetUnset($offset): void
    {
        $this->entityStates->offsetUnset($offset);
    }

    public function next(): void
    {
        $this->entityStates->next();
    }

    public function key(): int
    {
        return $this->entityStates->key();
    }

    public function valid(): bool
    {
        return $this->entityStates->valid();
    }

    public function rewind(): void
    {
        $this->entityStates->rewind();
    }

    public function current(): RepresentsEntity
    {
        return $this->entityStates->current();
    }

    public function count(): int
    {
        return $this->entityStates->count();
    }

    private function addToMapIfNew(
        MapsObjectsByIdentity $map,
        MapsObjectsByIdentity $additionalMap,
        string $class,
        string $id
    ): MapsObjectsByIdentity {
        if ($map->has($class, $id)) {
            return $map;
        }
        return $map->add(
            $id,
            $additionalMap->get($class, $id)
        );
    }
}
