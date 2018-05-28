<?php
declare(strict_types=1);

namespace Stratadox\EntityState\Test\Fixture\Coin;

final class Purse
{
    private $id;
    private $coins;

    public function __construct(string $id, Coins $coins)
    {
        $this->id = $id;
        $this->coins = $coins;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function coins(): Coins
    {
        return $this->coins;
    }
}
