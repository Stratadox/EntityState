<?php declare(strict_types=1);

namespace Stratadox\EntityState\Test\Fixture\Guests;

final class Referrer
{
    private $name;
    private $referrals;

    private function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function fromFirstReferral(
        string $name,
        string $ipAddress
    ): self {
        $referrer = new self($name);
        $referrer->refer($ipAddress);
        return $referrer;
    }

    public function refer(string $ipAddress): void
    {
        $this->referrals[] = Guest::withIp($ipAddress);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function referrals(): iterable
    {
        return $this->referrals;
    }
}
