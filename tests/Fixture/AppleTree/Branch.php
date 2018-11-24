<?php
declare(strict_types=1);

namespace Stratadox\EntityState\Test\Fixture\AppleTree;

use Stratadox\ImmutableCollection\ImmutableCollection;

class Branch extends ImmutableCollection
{
    public function __construct(Ripeness ...$ripenessOfTheApples)
    {
        $apples = [];
        foreach ($ripenessOfTheApples as $ripeness) {
            $apples[] = Apple::onBranch($this, $ripeness);
        }
        parent::__construct(...$apples);
    }

    public static function withApplesOf(Ripeness ...$apples): self
    {
        return new static(...$apples);
    }

    public function current(): Apple
    {
        return parent::current();
    }

    public function growNewApple(): self
    {
        $ripeness = [];
        foreach ($this as $apple) {
            $ripeness[] = $apple->ripeness();
        }
        $ripeness[] = Ripeness::scored(0);
        return new static(...$ripeness);
    }

    public function increaseRipeness(AppleFallObserver $fallObserver): self
    {
        $remainingApples = [];
        foreach ($this as $apple) {
            if ($apple->isReadyToFall()) {
                $fallObserver->itFalls(Apple::onTheGround());
            } else {
                $remainingApples[] = $apple->increaseRipeness();
            }
        }
        return new static(...$remainingApples);
    }
}
