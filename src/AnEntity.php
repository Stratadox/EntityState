<?php declare(strict_types=1);

namespace Stratadox\EntityState;

use ReflectionProperty;

/**
 * Definition for new entities.
 *
 * @todo Provide a better way to extract the identifier
 *       Current limitations:
 *       - Identity must be in a single property (no compounds)
 *       - Identity property cannot be located in a parent class
 * @author Stratadox
 */
final class AnEntity implements DefinesEntityType
{
    private $class;
    private $identifierProperty;

    private function __construct(
        string $class,
        ?ReflectionProperty $identifierProperty
    ) {
        $this->class = $class;
        $this->identifierProperty = $identifierProperty;
    }

    /**
     * @param string      $class              The class to recognise.
     * @param string|null $identifierProperty The property to get the id from.
     * @return DefinesEntityType              The new entity type definition.
     * @throws \ReflectionException           When the property is inaccessible.
     */
    public static function whenEncountering(
        string $class,
        string $identifierProperty = null
    ): DefinesEntityType {
        if (null === $identifierProperty) {
            return new self($class, null);
        }
        $property = new ReflectionProperty($class, $identifierProperty);
        $property->setAccessible(true);
        return new self($class, $property);
    }

    /** @inheritdoc */
    public function recognises(object $potentialEntity): bool
    {
        return $potentialEntity instanceof $this->class;
    }

    /** @inheritdoc */
    public function idFor(object $recognisedEntity): ?string
    {
        if (null === $this->identifierProperty) {
            return null;
        }
        return $this->identifierProperty->getValue($recognisedEntity);
    }
}
