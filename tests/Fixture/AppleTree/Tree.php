<?php
declare(strict_types=1);

namespace Stratadox\EntityState\Test\Fixture\AppleTree;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Tree
{
    private $id;
    private $branches;

    private function __construct(UuidInterface $id, Branches $branches)
    {
        $this->id = $id;
        $this->branches = $branches;
    }

    public static function withBranches(Branch ...$branches): self
    {
        return new static(Uuid::uuid4(), Branches::onTheTree(...$branches));
    }

    public function id(): UuidInterface
    {
        return $this->id;
    }

    public function branches(): Branches
    {
        return $this->branches;
    }

    public function growNewAppleOnBranch(int $branchNumber): void
    {
        $this->branches = $this->branches->growNewAppleOnBranch($branchNumber);
    }

    public function increaseRipeness(AppleFallObserver $fallObserver = null): void
    {
        $this->branches = $this->branches->increaseRipeness(
            $fallObserver ?: NobodyIsLooking::atTheApples()
        );
    }
}
