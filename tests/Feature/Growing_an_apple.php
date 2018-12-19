<?php declare(strict_types=1);

namespace Stratadox\EntityState\Test\Feature;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;
use Stratadox\EntityState\Extract;
use Stratadox\EntityState\Test\Fixture\AppleTree\Apple;
use Stratadox\EntityState\Test\Fixture\AppleTree\Branch;
use Stratadox\EntityState\Test\Fixture\AppleTree\Branches;
use Stratadox\EntityState\Test\Fixture\AppleTree\Ripeness;
use Stratadox\EntityState\Test\Fixture\AppleTree\Tree;
use Stratadox\EntityState\Test\Support\PropertyAsserting;
use Stratadox\IdentityMap\IdentityMap;

/**
 * @coversNothing
 */
class Growing_an_apple extends TestCase
{
    use PropertyAsserting;

    /** @test */
    function recognising_the_growth_of_a_new_apple_as_value_object()
    {
        $tree = Tree::withBranches(
            Branch::withApplesOf(Ripeness::scored(1), Ripeness::scored(4)),
            Branch::withApplesOf(Ripeness::scored(5), Ripeness::scored(2)),
            Branch::withApplesOf(Ripeness::scored(3))
        );

        $map = IdentityMap::with([(string) $tree->id() => $tree]);
        $initialState = Extract::stringifying(UuidInterface::class)->from($map);

        $tree->growNewAppleOnBranch(1);

        $newState = Extract::stringifying(UuidInterface::class)->from($map);

        $changes = $newState->changesSince($initialState);
        $treeRepresentation = $changes->altered()[0];

        $this->assertSame((string) $tree->id(), $treeRepresentation->id());

        $this->assertProperty(
            $treeRepresentation,
            'count(' . Branch::class . ':' . Branches::class . ':branches[1])',
            3
        );
        $this->assertProperty(
            $treeRepresentation,
            Apple::class . ':' . Branch::class . ':' . Branches::class .
            ':branches[1][2].' . Ripeness::class . ':ripeness.score',
            0
        );
    }
}
