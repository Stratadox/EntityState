<?php
declare(strict_types=1);

namespace Stratadox\EntityState;

use Stratadox\EntityState\RepresentsEntity as TheOther;

/**
 * Represents the state of an entity.
 *
 * @author Stratadox
 */
final class EntityState implements RepresentsEntity
{
    private $class;
    private $id;
    private $properties;

    private function __construct(
        string $class,
        ?string $id,
        ListsPropertyStates $properties
    ) {
        $this->class = $class;
        $this->id = $id;
        $this->properties = $properties;
    }

    /**
     * Produces an entity representation.
     *
     * @param string              $class      The fully qualified class name.
     * @param null|string         $id         The id of the entity.
     * @param ListsPropertyStates $properties The property state representations.
     * @return RepresentsEntity               The entity state representation.
     */
    public static function ofThe(
        string $class,
        ?string $id,
        ListsPropertyStates $properties
    ): RepresentsEntity {
        return new self($class, $id, $properties);
    }

    /** @inheritdoc */
    public function class(): string
    {
        return $this->class;
    }

    /** @inheritdoc */
    public function hasTheSameIdentityAs(RepresentsEntity $entity): bool
    {
        return $this->class === $entity->class()
            && $this->id === $entity->id();
    }

    /** @inheritdoc */
    public function id(): ?string
    {
        return $this->id;
    }

    /** @inheritdoc */
    public function properties(): ListsPropertyStates
    {
        return $this->properties;
    }

    /** @inheritdoc */
    public function isDifferentFrom(TheOther $entity): bool
    {
        return !$this->hasTheSameIdentityAs($entity)
            || $this->properties->areDifferentFrom($entity->properties());
    }

    /** @inheritdoc */
    public function subsetThatDiffersFrom(
        ListsEntityStates $entityStates
    ): RepresentsEntity {
        foreach ($entityStates as $entity) {
            if ($this->hasTheSameIdentityAs($entity)) {
                return $this->subsetThatDiffersFromThe($entity);
            }
        }
        return $this;
    }

    /** @inheritdoc */
    public function mergeWith(TheOther $entityState): RepresentsEntity
    {
        return $this->with(
            $this->properties->merge($entityState->properties())
        );
    }

    private function subsetThatDiffersFromThe(
        RepresentsEntity $otherEntity
    ): RepresentsEntity {
        $properties = [];
        foreach ($this->properties as $myProperty) {
            if ($myProperty->isDifferentInThe($otherEntity->properties())) {
                $properties[] = $myProperty;
            }
        }
        return $this->with(PropertyStates::list(...$properties));
    }

    private function with(ListsPropertyStates $properties): RepresentsEntity
    {
        return EntityState::ofThe($this->class, $this->id, $properties);
    }
}
