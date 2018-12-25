<?php
declare(strict_types=1);

namespace Stratadox\EntityState\Test\Unit\Extract;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Stratadox\EntityState\Extract;
use Stratadox\EntityState\Test\Fixture\AppleTree\Apple;
use Stratadox\EntityState\Test\Fixture\AppleTree\Branch;
use Stratadox\EntityState\Test\Fixture\AppleTree\Branches;
use Stratadox\EntityState\Test\Fixture\AppleTree\Ripeness;
use Stratadox\EntityState\Test\Fixture\AppleTree\Tree;
use Stratadox\EntityState\Test\Fixture\Coin\Copper;
use Stratadox\EntityState\Test\Fixture\FooBar\Foo;
use Stratadox\EntityState\Test\Fixture\RentalCar\Aspects;
use Stratadox\EntityState\Test\Fixture\RentalCar\Branding;
use Stratadox\EntityState\Test\Fixture\RentalCar\Make;
use Stratadox\EntityState\Test\Fixture\RentalCar\Model;
use Stratadox\EntityState\Test\Fixture\RentalCar\Space;
use Stratadox\EntityState\Test\Support\PropertyAsserting;
use Stratadox\IdentityMap\IdentityMap;

/**
 * @covers \Stratadox\EntityState\Extract
 * @covers \Stratadox\EntityState\Internal\Name
 * @covers \Stratadox\EntityState\Internal\Visited
 * @covers \Stratadox\EntityState\Internal\ObjectExtractor
 * @covers \Stratadox\EntityState\Internal\PropertyExtractor
 * @covers \Stratadox\EntityState\Internal\ScalarExtractor
 * @covers \Stratadox\EntityState\Internal\ExtractionRequest
 */
class Extract_the_state_of_the_entities extends TestCase
{
    use PropertyAsserting;

    /** @test */
    function extracting_the_state_of_nested_entities()
    {
        $opelCar = new Model(
            new Branding(new Make('Opel'), 'Corsa E', 2015),
            new Aspects(new Space(4, 4, 2), 115, false)
        );

        $fiatCar = new Model(
            new Branding(new Make('Fiat'), '500', 2007),
            new Aspects(new Space(4, 2, 1), 69, false)
        );

        $branding = Branding::class;
        $make = Make::class;
        $aspects = Aspects::class;
        $space = Space::class;

        [$opel, $fiat] = Extract::state()->from(IdentityMap::with([
            'Opel Corsa' => $opelCar,
            'Fiat 500' => $fiatCar,
        ]));

        $this->assertSame('Opel Corsa', $opel->id());
        $this->assertProperty($opel, "$branding:branding.name", 'Corsa E');
        $this->assertProperty($opel, "$branding:branding.$make:make.brand", 'Opel');
        $this->assertProperty($opel, "$branding:branding.releaseYear", 2015);
        $this->assertProperty($opel, "$aspects:aspects.$space:space.seats", 4);
        $this->assertProperty($opel, "$aspects:aspects.$space:space.doors", 4);
        $this->assertProperty($opel, "$aspects:aspects.$space:space.suitcases", 2);
        $this->assertProperty($opel, "$aspects:aspects.horsepower", 115);
        $this->assertProperty($opel, "$aspects:aspects.automatic", false);

        $this->assertSame('Fiat 500', $fiat->id());
        $this->assertProperty($fiat, "$branding:branding.name", '500');
        $this->assertProperty($fiat, "$branding:branding.$make:make.brand", 'Fiat');
        $this->assertProperty($fiat, "$branding:branding.releaseYear", 2007);
        $this->assertProperty($fiat, "$aspects:aspects.$space:space.seats", 4);
        $this->assertProperty($fiat, "$aspects:aspects.$space:space.doors", 2);
        $this->assertProperty($fiat, "$aspects:aspects.$space:space.suitcases", 1);
        $this->assertProperty($fiat, "$aspects:aspects.horsepower", 69);
        $this->assertProperty($fiat, "$aspects:aspects.automatic", false);
    }

    /** @test */
    function extracting_only_a_subset_of_the_identity_map()
    {
        $foos = [
            new Foo(0, 'Foo 0'),
            new Foo(1, 'Foo 1'),
            new Foo(2, 'Foo 2'),
        ];

        $entities = Extract::state()->fromOnly(
            IdentityMap::with($foos),
            $foos[1],
            $foos[2]
        );

        $this->assertCount(2, $entities);

        $this->assertProperty($entities[0], 'name', 'Foo 1');
        $this->assertProperty($entities[1], 'name', 'Foo 2');
    }

    /** @test */
    function handling_recursive_value_objects()
    {
        $tree = Tree::withBranches(
            Branch::withApplesOf(
                Ripeness::scored(3),
                Ripeness::scored(5),
                Ripeness::scored(1),
                Ripeness::scored(1)
            ),
            Branch::withApplesOf(
                Ripeness::scored(3),
                Ripeness::scored(5),
                Ripeness::scored(1),
                Ripeness::scored(1)
            ),
            Branch::withApplesOf(
                Ripeness::scored(5),
                Ripeness::scored(8)
            )
        );

        $branch = Branch::class;
        $branches = Branches::class;
        $apple = Apple::class;
        $ripeness = Ripeness::class;
        $uuid = Uuid::class;

        [$entity] = Extract::stringifying(UuidInterface::class)
            ->from(IdentityMap::with([
                (string) $tree->id() => $tree
            ]));

        $this->assertProperty($entity, "$uuid:id", (string) $tree->id());

        $this->assertProperty(
            $entity,
            "$apple:$branch:$branches:branches[0][0].$ripeness:ripeness.score",
            3
        );
        $this->assertProperty(
            $entity,
            "$apple:$branch:$branches:branches[1][0].$ripeness:ripeness.score",
            3
        );
        $this->assertProperty(
            $entity,
            "$apple:$branch:$branches:branches[2][1].$ripeness:ripeness.score",
            8
        );
        $this->assertProperty(
            $entity,
            "$apple:$branch:$branches:branches[0][2].$ripeness:ripeness.score",
            1
        );
        $this->assertProperty(
            $entity,
            "$apple:$branch:$branches:branches[0][3].$ripeness:ripeness.score",
            1
        );
        $this->assertProperty(
            $entity,
            "$apple:$branch:$branches:branches[0][2].branch",
            ["$branch:$branches:branches[0]"],
            'The branch object that is pointed to is already represented. ' .
            "If we'd follow the pointers, we'd end up in an infinite loop; " .
            'Instead, the already processed object is pointed to by storing ' .
            "the offset for the object in the entity's state representation."
        );
        $this->assertProperty(
            $entity,
            "$apple:$branch:$branches:branches[1][2].branch",
            ["$branch:$branches:branches[1]"]
        );
    }

    /** @test */
    function retaining_the_identity_map_in_the_result()
    {
        $map = IdentityMap::with([new Copper()]);

        $state = Extract::state()->from($map);

        $this->assertSame($map, $state->identityMap());
    }
}
