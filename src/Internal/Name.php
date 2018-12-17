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

    public static function start(): self
    {
        return new self('', '');
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
                '%s%s[%s]',
                is_object($collection) ? get_class($collection) : gettype($collection),
                $this->nameWithColon(),
                $this->escape($key)
            )
        );
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
            sprintf('%s%s', get_class($object), $this->nameWithColon())
        );
    }

    /**
     * Returns a name for the nested property.
     *
     * @param ReflectionProperty $property The nested property.
     * @return Name                        The prefixed property name.
     */
    public function forReflected(ReflectionProperty $property): Name
    {
        return new Name($this->asPrefix(), $property->getName());
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
                'count(%s%s)',
                is_object($collection) ? get_class($collection) : gettype($collection),
                $this->nameWithColon()
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

    private function escape(string $key): string
    {
        return str_replace(self::PROBLEMS, self::SOLUTIONS, $key);
    }

    private function asPrefix(): string
    {
        if ($this->prefix === '' && $this->name === '') {
            return '';
        }
        return sprintf('%s.', $this);
    }

    private function nameWithColon(): string
    {
        return $this->name ? sprintf(':%s', $this->name) : '';
    }
}
