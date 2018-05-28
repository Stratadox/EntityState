<?php
declare(strict_types=1);

namespace Stratadox\EntityState\Test\Fixture\Coin;

final class Silver implements Coin
{
    public function value(): int
    {
        return 2;
    }
}
