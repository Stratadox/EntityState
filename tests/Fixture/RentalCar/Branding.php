<?php
declare(strict_types=1);

namespace Stratadox\EntityState\Test\Fixture\RentalCar;

final class Branding
{
    private $make;
    private $name;
    private $releaseYear;

    public function __construct(Make $make, string $name, int $releaseYear)
    {
        $this->make = $make;
        $this->name = $name;
        $this->releaseYear = $releaseYear;
    }

    public function make(): Make
    {
        return $this->make;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function releaseYear(): int
    {
        return $this->releaseYear;
    }

    public function updateReleaseYear(int $year): Branding
    {
        return new Branding(
            $this->make,
            $this->name,
            $year
        );
    }
}
