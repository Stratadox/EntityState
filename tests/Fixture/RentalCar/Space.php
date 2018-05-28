<?php
declare(strict_types=1);

namespace Stratadox\EntityState\Test\Fixture\RentalCar;

use InvalidArgumentException;

final class Space
{
    private $seats;
    private $doors;
    private $suitcases;

    public function __construct(int $seats, int $doors, int $suitcases)
    {
        if ($doors > 6 || $doors < 1) {
            throw new InvalidArgumentException("$doors doors? Really?");
        }
        if ($seats > 1000 || $seats < 1) {
            throw new InvalidArgumentException("$seats seats? Really?");
        }
        $this->seats = $seats;
        $this->doors = $doors;
        $this->suitcases = $suitcases;
    }

    public function seats(): int
    {
        return $this->seats;
    }

    public function doors(): int
    {
        return $this->doors;
    }

    public function suitcases(): int
    {
        return $this->suitcases;
    }
}
