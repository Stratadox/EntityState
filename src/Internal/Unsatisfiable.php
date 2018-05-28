<?php
declare(strict_types=1);

namespace Stratadox\EntityState\Internal;

use Stratadox\Specification\Contract\Satisfiable;

/**
 * Constraint that is never satisfied.
 *
 * @internal
 * @author Stratadox
 */
final class Unsatisfiable implements Satisfiable
{
    private function __construct()
    {
    }

    /**
     * Produces an unsatisfiable constraint.
     *
     * @return Satisfiable The (unsatisfiable) constraint.
     */
    public static function constraint(): Satisfiable
    {
        return new Unsatisfiable();
    }

    /** @inheritdoc */
    public function isSatisfiedBy($object): bool
    {
        return false;
    }
}
