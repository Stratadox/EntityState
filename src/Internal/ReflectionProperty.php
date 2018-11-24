<?php declare(strict_types=1);

namespace Stratadox\EntityState\Internal;

use ReflectionProperty as BaseReflectionProperty;
use function sprintf;

/**
 * ReflectionProperty that is automatically made accessible and adds a level
 * suffix to the property name.
 *
 * @internal
 * @author Stratadox
 */
final class ReflectionProperty extends BaseReflectionProperty
{
    private $level;

    private function __construct($class, string $name, int $level)
    {
        parent::__construct($class, $name);
        $this->setAccessible(true);
        $this->level = $level;
    }

    /**
     * Transforms a base ReflectionProperty into a custom ReflectionProperty.
     *
     * @param BaseReflectionProperty $property The property to convert.
     * @param int                    $level    The inheritance deepness.
     * @return ReflectionProperty              The converted property.
     */
    public static function from(
        BaseReflectionProperty $property,
        int $level
    ): ReflectionProperty {
        return new ReflectionProperty(
            $property->class,
            $property->name,
            $level
        );
    }

    public function getName(): string
    {
        if ($this->level === 0) {
            return parent::getName();
        }
        return sprintf(
            '%s{%d}',
            parent::getName(),
            $this->level
        );
    }
}
