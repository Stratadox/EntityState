<?php
declare(strict_types=1);

namespace Stratadox\EntityState\Internal;

use Stratadox\Specification\Contract\Satisfiable;

/**
 * Constraint to check if the extractor should stringify the object.
 *
 * @internal
 * @author Stratadox
 */
final class ShouldStringify implements Satisfiable
{
    private $types;

    private function __construct(string ...$types)
    {
        $this->types = $types;
    }

    /**
     * Produces a constraint to stringify these classes.
     *
     * @param string ...$classes Fully qualified class or interface names.
     * @return ShouldStringify   The constraint.
     */
    public static function these(string ...$classes): self
    {
        return new self(...$classes);
    }

    /** @inheritdoc */
    public function isSatisfiedBy($object): bool
    {
        foreach ($this->types as $stringifyIt) {
            if ($object instanceof $stringifyIt) {
                return true;
            }
        }
        return false;
    }
}
