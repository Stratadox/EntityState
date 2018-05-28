<?php
declare(strict_types=1);

namespace Stratadox\EntityState\Test\Fixture\FooBar;

final class Bar
{
    private $foos;

    public function __construct(Foo ...$foos)
    {
        $this->foos = $foos;
    }

    public function addFoo(Foo $foo): void
    {
        $this->foos[] = $foo;
    }
}
