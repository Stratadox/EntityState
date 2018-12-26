<?php declare(strict_types=1);

namespace Stratadox\EntityState\Test\Fixture\SpyAgency;

use RuntimeException;
use function sprintf;

final class NoSuchSpy extends RuntimeException
{
    public static function withName(string $name): self
    {
        return new self(sprintf(
            'Could not find the spy `%s`. Maybe they\'re just *that* good...',
            $name
        ));
    }
}
