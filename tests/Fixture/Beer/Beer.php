<?php
declare(strict_types=1);

namespace Stratadox\EntityState\Test\Fixture\Beer;

final class Beer
{
    private $name;
    private $percentage;

    public function __construct(string $name, string $percentage)
    {
        $this->name = $name;
        $this->percentage = $percentage;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function percentage(): string
    {
        return $this->percentage;
    }

    public function rename(string $newName): void
    {
        $this->name = $newName;
    }
}
