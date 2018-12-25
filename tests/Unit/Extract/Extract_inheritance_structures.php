<?php declare(strict_types=1);

namespace Stratadox\EntityState\Test\Unit\Extract;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Stratadox\EntityState\Extract;
use Stratadox\EntityState\Test\Fixture\AppleTree\Apple;
use Stratadox\EntityState\Test\Fixture\AppleTree\Branches;
use Stratadox\EntityState\Test\Fixture\AppleTree\ChildBranch;
use Stratadox\EntityState\Test\Fixture\AppleTree\ChildTree;
use Stratadox\EntityState\Test\Fixture\AppleTree\Ripeness;
use Stratadox\EntityState\Test\Fixture\Inheritance\Child;
use Stratadox\EntityState\Test\Fixture\Inheritance\ChildWithPrivateProperty;
use Stratadox\EntityState\Test\Support\PropertyAsserting;
use Stratadox\IdentityMap\IdentityMap;

/**
 * @covers \Stratadox\EntityState\Extract
 * @covers \Stratadox\EntityState\Internal\ReflectionProperties
 * @covers \Stratadox\EntityState\Internal\ReflectionProperty
 */
class Extract_inheritance_structures extends TestCase
{
    use PropertyAsserting;

    /** @test */
    function mapping_objects_with_parents()
    {
        $object = new Child('value');

        [$entity] = Extract::state()->from(IdentityMap::with([$object]));

        $this->assertProperty($entity, 'property{1}', 'value');
    }

    /** @test */
    function handling_nested_value_objects_with_inheritance()
    {
        $tree = ChildTree::withBranches(
            ChildBranch::withApplesOf(
                Ripeness::scored(3),
                Ripeness::scored(5),
                Ripeness::scored(1),
                Ripeness::scored(1)
            ),
            ChildBranch::withApplesOf(
                Ripeness::scored(3),
                Ripeness::scored(5),
                Ripeness::scored(1),
                Ripeness::scored(1)
            ),
            ChildBranch::withApplesOf(
                Ripeness::scored(5),
                Ripeness::scored(8)
            )
        );

        $branch = ChildBranch::class;
        $branches = Branches::class;
        $apple = Apple::class;
        $ripeness = Ripeness::class;
        $uuid = Uuid::class;

        [$entity] = Extract::stringifying(UuidInterface::class)
            ->from(IdentityMap::with([
                (string) $tree->id() => $tree
            ]));

        $this->assertProperty($entity, "$uuid:id{1}", (string) $tree->id());

        $this->assertProperty(
            $entity,
            "$apple:$branch:$branches:branches{1}[0][0].$ripeness:ripeness.score",
            3
        );
        $this->assertProperty(
            $entity,
            "$apple:$branch:$branches:branches{1}[1][0].$ripeness:ripeness.score",
            3
        );
        $this->assertProperty(
            $entity,
            "$apple:$branch:$branches:branches{1}[2][1].$ripeness:ripeness.score",
            8
        );
        $this->assertProperty(
            $entity,
            "$apple:$branch:$branches:branches{1}[0][2].$ripeness:ripeness.score",
            1
        );
        $this->assertProperty(
            $entity,
            "$apple:$branch:$branches:branches{1}[0][3].$ripeness:ripeness.score",
            1
        );
        $this->assertProperty(
            $entity,
            "$apple:$branch:$branches:branches{1}[0][2].branch",
            ["$branch:$branches:branches{1}[0]"]
        );
        $this->assertProperty(
            $entity,
            "$apple:$branch:$branches:branches{1}[1][2].branch",
            ["$branch:$branches:branches{1}[1]"]
        );
    }

    /** @test */
    function mapping_objects_with_properties_that_also_exist_in_the_parent()
    {
        $object = new ChildWithPrivateProperty('parent value', 'child value');

        [$entity] = Extract::state()->from(IdentityMap::with([$object]));

        $this->assertProperty($entity, 'property', 'child value');
        $this->assertProperty($entity, 'property{1}', 'parent value');
    }
}
