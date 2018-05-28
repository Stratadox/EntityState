<?php
declare(strict_types=1);

namespace Stratadox\EntityState\Test\Fixture\Coin;

use Stratadox\ImmutableCollection\ImmutableCollection;

final class Coins extends ImmutableCollection
{
    public function __construct(Coin ...$coins)
    {
        parent::__construct(...$coins);
    }
}
