<?php
declare(strict_types=1);

namespace Stratadox\EntityState\Test\Fixture\FooBar;

final class Baz
{
    private $id;

    public function __construct(Id $id)
    {
        $this->id = $id;
    }

    public function id(): Id
    {
        return $this->id;
    }
}
