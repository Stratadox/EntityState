<?php
declare(strict_types=1);

namespace Stratadox\EntityState\Test\Fixture\AppleTree;

use function count;
use SplFixedArray;

class Branch extends SplFixedArray
{
    public static function withApplesOf(Ripeness ...$ripenessOfTheApples): self
    {
        $branch = new static(count($ripenessOfTheApples));
        foreach ($ripenessOfTheApples as $i => $ripeness) {
            $branch[$i] = Apple::onBranch($branch, $ripeness);
        }
        return $branch;
    }

    public function current(): Apple
    {
        return parent::current();
    }

    public function growNewApple(): self
    {
        $branch = new static($this->count() + 1);
        foreach ($this as $i => $apple) {
            $branch[$i] = $apple;
        }
        $branch[$this->count()] = Apple::onBranch($branch, Ripeness::scored(0));
        return $branch;
    }

    public function increaseRipeness(AppleFallObserver $fallObserver): self
    {
        $ripeness = [];
        foreach ($this as $apple) {
            if ($apple->isReadyToFall()) {
                $fallObserver->itFalls(Apple::onTheGround());
            } else {
                $ripeness[] = $apple->increaseRipeness();
            }
        }
        return static::withApplesOf(...$ripeness);
    }
}
