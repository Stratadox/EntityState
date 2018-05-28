<?php
declare(strict_types=1);

namespace Stratadox\EntityState\Test\Fixture\RentalCar;

use function sprintf;

final class Model
{
    private $branding;
    private $aspects;
    private $largerModel;

    public function __construct(
        Branding $branding,
        Aspects $aspects,
        Model $largerModel = null
    ) {
        $this->branding = $branding;
        $this->aspects = $aspects;
        $this->largerModel = $largerModel;
    }

    public function make(): Make
    {
        return $this->branding->make();
    }

    public function name(): string
    {
        return $this->branding->name();
    }

    public function releasedInYear(): int
    {
        return $this->branding->releaseYear();
    }

    public function updateReleaseYear(int $year): void
    {
        $this->branding = $this->branding->updateReleaseYear($year);
    }

    public function seats(): int
    {
        return $this->aspects->seats();
    }

    public function doors(): int
    {
        return $this->aspects->doors();
    }

    public function suitcases(): int
    {
        return $this->aspects->suitcases();
    }

    public function horsepower(): int
    {
        return $this->aspects->horsepower();
    }

    public function isAutomatic(): bool
    {
        return $this->aspects->isAutomatic();
    }

    public function isManual(): bool
    {
        return $this->aspects->isManual();
    }

    public function largerModel(): Model
    {
        return $this->largerModel;
    }

    public function __toString(): string
    {
        return sprintf('%s %s', $this->make(), $this->name());
    }
}
