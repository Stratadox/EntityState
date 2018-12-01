<?php
declare(strict_types=1);

namespace Stratadox\EntityState\Internal;

use function array_key_exists as alreadyCached;
use function get_class as theClassOfThe;
use ReflectionClass;
use ReflectionException;
use ReflectionObject;
use Stratadox\ImmutableCollection\ImmutableCollection;

/**
 * ReflectionProperties collection.
 *
 * @internal
 * @author Stratadox
 */
final class ReflectionProperties extends ImmutableCollection
{
    private static $cache = [];

    private function __construct(ReflectionProperty ...$reflectionProperties)
    {
        parent::__construct(...$reflectionProperties);
    }

    /**
     * Retrieves the collection of ReflectionProperties for the object.
     *
     * @param object $object        The object to retrieve the properties for.
     * @return ReflectionProperties The collection of reflection properties.
     */
    public static function ofThe(object $object): self
    {
        $theClass = theClassOfThe($object);
        if (!alreadyCached($theClass, ReflectionProperties::$cache)) {
            $reflection = new ReflectionObject($object);
            $properties = [];
            $level = 0;
            do {
                $properties = self::addTo($properties, $level, $reflection);
                ++$level;
                $reflection = $reflection->getParentClass();
            } while($reflection);
            ReflectionProperties::$cache[$theClass] = new self(...$properties);
        }
        return ReflectionProperties::$cache[$theClass];
    }

    /** @inheritdoc */
    public function current(): ReflectionProperty
    {
        return parent::current();
    }

    private static function addTo(
        array $properties,
        int $level,
        ReflectionClass $reflection
    ): array {
        foreach ($reflection->getProperties() as $property) {
            try {
                $properties[] = ReflectionProperty::from($property, $level);
            } catch (ReflectionException $exception) {
                // skip inaccessible properties
            }
        }
        return $properties;
    }
}
