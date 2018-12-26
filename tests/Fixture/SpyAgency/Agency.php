<?php declare(strict_types=1);

namespace Stratadox\EntityState\Test\Fixture\SpyAgency;

final class Agency
{
    private $name;
    private $spies;

    private function __construct(string $name)
    {
        $this->name = $name;
        $this->spies = Spies::none();
    }

    public static function named(string $name): self
    {
        return new self($name);
    }

    public function hireSpyNamed(string $name): void
    {
        if ($this->spies->includesSpyNamed($name)) {
            throw CannotDoubleHire::alreadyHired($this, $name);
        }
        $this->spies = $this->spies->add(Spy::named($name));
    }

    public function name(): string
    {
        return $this->name;
    }

    public function giveCoverIdentityTo(string $spyName, string $fakeIdentity): void
    {
        $this->assignCoverTo($this->spies->named($spyName), $fakeIdentity);
    }

    private function assignCoverTo(Spy $spy, string $fakeIdentity): void
    {
        $spy->takeUp($fakeIdentity);
    }
}
