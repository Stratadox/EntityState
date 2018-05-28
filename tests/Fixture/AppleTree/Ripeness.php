<?php
declare(strict_types=1);

namespace Stratadox\EntityState\Test\Fixture\AppleTree;

use InvalidArgumentException;

final class Ripeness
{
    private const READY_AT = 10;

    private $score;

    private function __construct(int $score)
    {
        if ($score > Ripeness::READY_AT || $score < 0) {
            throw new InvalidArgumentException("Invalid ripeness $score.");
        }
        $this->score = $score;
    }

    public static function scored(int $score): self
    {
        return new self($score);
    }

    public static function fallen(): Ripeness
    {
        return new Ripeness(Ripeness::READY_AT);
    }

    public function increase(): Ripeness
    {
        return new Ripeness($this->score + 1);
    }

    public function ready(): bool
    {
        return Ripeness::READY_AT === $this->score;
    }
}
