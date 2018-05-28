<?php
declare(strict_types=1);

namespace Stratadox\EntityState\Test\Fixture\Beer;

final class Brewery
{
    private $name;
    private $beers;

    public function __construct(string $name, Beers $beers)
    {
        $this->name = $name;
        $this->beers = $beers;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function beers(): Beers
    {
        return $this->beers;
    }

    public function introduce(Beer $beer): void
    {
        $this->beers = $this->beers->add($beer);
    }
}
