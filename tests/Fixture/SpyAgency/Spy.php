<?php declare(strict_types=1);

namespace Stratadox\EntityState\Test\Fixture\SpyAgency;

use function array_filter;
use function count;

final class Spy
{
    /** @var Cover[] */
    private $covers = [];
    private $name;

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

    public function takeUp(string $fakeIdentityName): void
    {
        $this->covers[] = Cover::named($fakeIdentityName);
    }

    public function drop(string $fakeIdentityToDrop): void
    {
        $this->covers = array_filter($this->covers,
            function (Cover $cover) use ($fakeIdentityToDrop): bool {
                return $cover->name() !== $fakeIdentityToDrop;
            }
        );
    }

    public function isDiscovered(): bool
    {
        if (!$this->hasTakenUpACoverIdentity()) {
            return false; // Cannot get discovered when not hidden.
        }
        foreach ($this->covers as $cover) {
            if ($cover->isBlown()) {
                return true;
            }
        }
        return false;
    }

    public function hasTakenUpACoverIdentity(): bool
    {
        return count($this->covers) > 0;
    }
}
