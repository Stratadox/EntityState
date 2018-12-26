<?php declare(strict_types=1);

namespace Stratadox\EntityState\Test\Fixture\SpyAgency;

use Stratadox\Collection\Appendable;
use Stratadox\ImmutableCollection\Appending;
use Stratadox\ImmutableCollection\ImmutableCollection;

final class Spies extends ImmutableCollection implements Appendable
{
    use Appending;

    protected function __construct(Spy ...$spies)
    {
        parent::__construct(...$spies);
    }

    public static function none(): self
    {
        return new self();
    }

    public function current(): Spy
    {
        return parent::current();
    }

    public function includesSpyNamed(string $name): bool
    {
        foreach ($this as $spy) {
            if ($spy->name() === $name) {
                return true;
            }
        }
        return false;
    }

    public function named(string $name): Spy
    {
        foreach ($this as $spy) {
            if ($spy->name() === $name) {
                return $spy;
            }
        }
        throw NoSuchSpy::withName($name);
    }
}
