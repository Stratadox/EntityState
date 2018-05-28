<?php
declare(strict_types=1);

namespace Stratadox\EntityState\Test\Fixture\RentalCar;

final class Make
{
    private $brand;

    public function __construct(string $brand)
    {
        $this->brand = $brand;
    }

    public function __toString(): string
    {
        return $this->brand;
    }
}
