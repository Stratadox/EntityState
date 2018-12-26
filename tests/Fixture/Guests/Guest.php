<?php declare(strict_types=1);

namespace Stratadox\EntityState\Test\Fixture\Guests;

final class Guest
{
    private $ipAddress;

    private function __construct(string $ipAddress)
    {
        $this->ipAddress = $ipAddress;
    }

    public static function withIp(string $ipAddress): self
    {
        return new self($ipAddress);
    }

    public function ipAddress(): string
    {
        return $this->ipAddress;
    }
}
