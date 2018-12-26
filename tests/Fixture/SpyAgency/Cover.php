<?php declare(strict_types=1);

namespace Stratadox\EntityState\Test\Fixture\SpyAgency;

final class Cover
{
    private $name;
    private $blown = false;

    private function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function named(string $name): self
    {
        return new self($name);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function isBlown(): bool
    {
        return $this->blown;
    }

    public function getBlown(): void
    {
        $this->blown = true;
    }
}
