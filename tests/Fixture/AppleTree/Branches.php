<?php
declare(strict_types=1);

namespace Stratadox\EntityState\Test\Fixture\AppleTree;

use Stratadox\ImmutableCollection\ImmutableCollection;

final class Branches extends ImmutableCollection
{
    public function __construct(Branch ...$branches)
    {
        parent::__construct(...$branches);
    }

    public static function onTheTree(Branch ...$branches): self
    {
        return new self(...$branches);
    }

    public function current(): Branch
    {
        return parent::current();
    }

    public function offsetGet($index): Branch
    {
        return parent::offsetGet($index);
    }

    public function growNewAppleOnBranch(int $branchNumber): self
    {
        /** @var Branch[] $branches */
        $branches = $this->items();
        $branches[$branchNumber] = $branches[$branchNumber]->growNewApple();
        return Branches::onTheTree(...$branches);
    }

    public function increaseRipeness(AppleFallObserver $fallObserver): self
    {
        $branches = [];
        foreach ($this as $branch) {
            $branches[] = $branch->increaseRipeness($fallObserver);
        }
        return Branches::onTheTree(...$branches);
    }
}
