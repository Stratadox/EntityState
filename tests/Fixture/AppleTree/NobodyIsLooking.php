<?php
declare(strict_types=1);

namespace Stratadox\EntityState\Test\Fixture\AppleTree;

final class NobodyIsLooking implements AppleFallObserver
{
    public static function atTheApples(): AppleFallObserver
    {
        return new self;
    }

    public function itFalls(Apple $apple): void
    {
    }
}
