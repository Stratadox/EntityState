<?php
declare(strict_types=1);

namespace Stratadox\EntityState\Internal;

use function get_class;
use function gettype;
use function is_object;
use function sprintf;
use function str_replace;

/**
 * Potentially prefixed property name.
 *
 * @internal
 * @author Stratadox
 */
final class Name
{
    private const PROBLEMS = ['\\', '[', ']'];
    private const SOLUTIONS = ['\\\\', '\\[', '\\]'];
    private $prefix;
    private $name;

    private function __construct(string $prefix, string $name)
    {
        $this->prefix = $prefix;
        $this->name = $name;
    }

    /**
     * Produces a name based on a reflection property.
     *
     * @param ReflectionProperty $property The reflection property.
     * @return Name                        The name for the property.
     */
    public static function fromReflection(ReflectionProperty $property): Name
    {
        return new Name('', $property->getName());
    }

    /**
     * Produces a name based on a collection entry.
     *
     * @param object $collection The collection object.
     * @param string $key        The position in the collection.
     * @return Name              The name for the collection entry.
     */
    public static function fromCollectionEntry(object $collection, string $key): Name
    {
        return new Name('', sprintf(
            '%s[%s]',
            get_class($collection),
            self::escape($key)
        ));
    }

    /**
     * Adds the class of the object to the property name definition.
     *
     * @param object $object The object whose class to add.
     * @return Name          The name with added class.
     */
    public function for(object $object): Name
    {
        return new Name(
            $this->prefix,
            sprintf('%s:%s', get_class($object), $this->name)
        );
    }

    /**
     * Returns a name for the nested property.
     *
     * @param ReflectionProperty $property The nested property.
     * @return Name                        The prefixed property name.
     */
    public function forNested(ReflectionProperty $property): Name
    {
        return new Name(sprintf('%s.', $this), $property->getName());
    }

    /**
     * Returns a name for the collection key.
     *
     * @param iterable $collection The collection where the item is in.
     * @param string   $key        The position of the item in the collection.
     * @return Name                The array key property name.
     */
    public function forItem(iterable $collection, string $key): Name
    {
        return new Name(
            $this->prefix,
            sprintf(
                '%s:%s[%s]',
                is_object($collection) ? get_class($collection) : gettype($collection),
                $this->name,
                self::escape($key)
            )
        );
    }

    /**
     * Returns a name for the semi-magic property to count the collection.
     *
     * @param iterable $collection The collection to store the count of.
     * @return Name                The collection counting name.
     */
    public function toCount(iterable $collection): Name
    {
        return new Name(
            $this->prefix,
            sprintf(
                'count(%s:%s)',
                is_object($collection) ? get_class($collection) : gettype($collection),
                $this->name
            )
        );
    }

    /**
     * Returns the string representation of the name.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->prefix . $this->name;
    }

    private static function escape(string $key): string
    {
        return str_replace(self::PROBLEMS, self::SOLUTIONS, $key);
    }
}
