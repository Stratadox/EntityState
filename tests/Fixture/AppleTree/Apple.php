<?php
declare(strict_types=1);

namespace Stratadox\EntityState\Test\Fixture\AppleTree;

final class Apple
{
    private $branch;
    private $ripeness;

    private function __construct(?Branch $branch, Ripeness $ripeness)
    {
        $this->branch = $branch;
        $this->ripeness = $ripeness;
    }

    public static function onBranch(Branch $branch, Ripeness $ripeness): Apple
    {
        return new Apple($branch, $ripeness);
    }

    public static function onTheGround(): Apple
    {
        return new Apple(null, Ripeness::fallen());
    }

    public function branch(): Branch
    {
        return $this->branch;
    }

    public function ripeness(): Ripeness
    {
        return $this->ripeness;
    }

    public function isReadyToFall(): bool
    {
        return $this->ripeness->ready();
    }

    public function increaseRipeness(): Ripeness
    {
        return $this->ripeness->increase();
    }
}
