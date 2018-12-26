<?php declare(strict_types=1);

namespace Stratadox\EntityState\Test\Fixture\SpyAgency;

use RuntimeException;

final class CannotDoubleHire extends RuntimeException
{
    public static function alreadyHired(Agency $agency, string $spy): self
    {
        return new self(sprintf(
            'Cannot hire `%s` at `%s` because they\'re already employed there.',
            $spy,
            $agency->name()
        ));
    }
}
