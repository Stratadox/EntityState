<?php
declare(strict_types=1);

namespace Stratadox\EntityState\Test\Fixture\FooBar;

use Ramsey\Uuid\UuidInterface;

final class Id
{
    private $uuid;

    public function __construct(UuidInterface $id)
    {
        $this->uuid = $id;
    }

    public function id(): UuidInterface
    {
        return $this->uuid;
    }

    public function __toString(): string
    {
        return (string) $this->uuid;
    }
}
