<?php
declare(strict_types=1);

namespace Stratadox\EntityState\Test\Fixture\RentalCar;

final class Aspects
{
    private $space;
    private $horsepower;
    private $automatic;

    public function __construct(Space $space, int $horsepower, bool $automatic)
    {
        $this->space = $space;
        $this->horsepower = $horsepower;
        $this->automatic = $automatic;
    }

    public function seats(): int
    {
        return $this->space->seats();
    }

    public function doors(): int
    {
        return $this->space->doors();
    }

    public function suitcases(): int
    {
        return $this->space->suitcases();
    }

    public function horsepower(): int
    {
        return $this->horsepower;
    }

    public function isAutomatic(): bool
    {
        return $this->automatic;
    }

    public function isManual(): bool
    {
        return !$this->automatic;
    }
}
