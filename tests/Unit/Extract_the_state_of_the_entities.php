<?php
declare(strict_types=1);

namespace Stratadox\EntityState\Test\Unit;

use ArrayObject;
use function implode;
use const PHP_EOL;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Stratadox\EntityState\Extract;
use Stratadox\EntityState\RepresentsEntity;
use Stratadox\EntityState\Test\Fixture\AppleTree\Apple;
use Stratadox\EntityState\Test\Fixture\AppleTree\Branch;
use Stratadox\EntityState\Test\Fixture\AppleTree\Branches;
use Stratadox\EntityState\Test\Fixture\AppleTree\ChildBranch;
use Stratadox\EntityState\Test\Fixture\AppleTree\ChildTree;
use Stratadox\EntityState\Test\Fixture\AppleTree\Ripeness;
use Stratadox\EntityState\Test\Fixture\AppleTree\Tree;
use Stratadox\EntityState\Test\Fixture\Beer\Beer;
use Stratadox\EntityState\Test\Fixture\Beer\Beers;
use Stratadox\EntityState\Test\Fixture\Beer\Brewery;
use Stratadox\EntityState\Test\Fixture\Coin\Coins;
use Stratadox\EntityState\Test\Fixture\Coin\Copper;
use Stratadox\EntityState\Test\Fixture\Coin\Gold;
use Stratadox\EntityState\Test\Fixture\Coin\Purse;
use Stratadox\EntityState\Test\Fixture\Coin\Silver;
use Stratadox\EntityState\Test\Fixture\FooBar\Bar;
use Stratadox\EntityState\Test\Fixture\FooBar\Baz;
use Stratadox\EntityState\Test\Fixture\FooBar\Id;
use Stratadox\EntityState\Test\Fixture\FooBar\Foo;
use Stratadox\EntityState\Test\Fixture\Inheritance\Child;
use Stratadox\EntityState\Test\Fixture\Inheritance\ChildWithPrivateProperty;
use Stratadox\EntityState\Test\Fixture\ListOfStrings\ListOfStrings;
use Stratadox\EntityState\Test\Fixture\Map\Map;
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
 * @covers \Stratadox\EntityState\Internal\ReflectionProperties
 * @covers \Stratadox\EntityState\Internal\ReflectionProperty
 * @covers \Stratadox\EntityState\Internal\ShouldStringify
 * @covers \Stratadox\EntityState\Internal\Visited
 * @covers \Stratadox\EntityState\Internal\CollectionExtractor
 * @covers \Stratadox\EntityState\Internal\ObjectExtractor
 * @covers \Stratadox\EntityState\Internal\PropertyExtractor
 * @covers \Stratadox\EntityState\Internal\ScalarExtractor
 * @covers \Stratadox\EntityState\Internal\ExtractionRequest
 * @covers \Stratadox\EntityState\Internal\Stringifier
 * @covers \Stratadox\EntityState\Internal\EntityReferenceExtractor
 * @todo Split into several test classes
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
    function extracting_a_list_of_strings()
    {
        $list = new ListOfStrings('foo', 'bar', 'baz');

        [$entity] = Extract::state()->from(IdentityMap::with([$list]));
        $listOfStrings = ListOfStrings::class;

        $this->assertProperty($entity, "count($listOfStrings)", 3);
        $this->assertProperty($entity, $listOfStrings . '[0]', 'foo');
        $this->assertProperty($entity, $listOfStrings . '[1]', 'bar');
        $this->assertProperty($entity, $listOfStrings . '[2]', 'baz');
    }

    /** @test */
    function extracting_only_a_subset_of_the_identity_map()
    {
        $foos = [
            new Foo(0, 'Foo 0'),
            new Foo(1, 'Foo 1'),
            new Foo(2, 'Foo 2')
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
    function mapping_other_entities_as_their_identifier()
    {
        $opelCar = new Model(
            new Branding(new Make('Opel'), 'Corsa E', 2015),
            new Aspects(new Space(4, 4, 2), 115, false)
        );

        $fiatCar = new Model(new Branding(
            new Make('Fiat'), '500', 2007),
            new Aspects(new Space(4, 2, 1), 69, false),
            $opelCar
        );

        [$fiat] = Extract::state()->from(IdentityMap::with([
            'Fiat 500' => $fiatCar,
            'Opel Corsa' => $opelCar,
        ]));

        $this->assertSame($opelCar, $fiatCar->largerModel());
        $this->assertProperty($fiat, Model::class . ':largerModel', 'Opel Corsa');
    }

    /** @test */
    function extracting_the_state_of_entities_in_an_array()
    {
        $bar = new Bar(
            new Foo(1000, 'Foo 0'),
            new Foo(1001, 'Foo 1')
        );

        $foo = Foo::class;

        [$entity] = Extract::state()->from(IdentityMap::with([
            'Bar' => $bar,
        ]));

        $this->assertSame('Bar', $entity->id());

        $this->assertProperty($entity, 'count(array:foos)', 2);

        $this->assertProperty($entity, "$foo:array:foos[0].id", 1000);
        $this->assertProperty($entity, "$foo:array:foos[0].name", 'Foo 0');

        $this->assertProperty($entity, "$foo:array:foos[1].id", 1001);
        $this->assertProperty($entity, "$foo:array:foos[1].name", 'Foo 1');
    }

    /** @test */
    function converting_specific_classes_to_string()
    {
        $uuid4 = Uuid::uuid4();
        $baz = new Baz(new Id($uuid4));

        $id = Id::class;
        $uuid = Uuid::class;

        [$entity] = Extract::stringifying(UuidInterface::class)
            ->from(IdentityMap::with([$baz]));

        $this->assertProperty($entity, "$id:id.$uuid:uuid", (string) $uuid4);
    }

    /** @test */
    function extracting_the_state_of_entities_in_a_collection()
    {
        $brewery = new Brewery(
            "Brouwerij 't IJ",
            new Beers(
                new Beer('ZATTE', '8%'),
                new Beer('NATTE', '6.5%'),
                new Beer('IJWIT', '6.5%'),
                new Beer('I.P.A', '7%'),
                new Beer('FLINK', '4.7%'),
                new Beer('PAASIJ', '7%')
            )
        );

        $beers = Beers::class;
        $beer = Beer::class;

        [$ij] = Extract::state()->from(IdentityMap::with([$brewery]));

        $this->assertProperty($ij, "$beer:$beers:beers[0].name", 'ZATTE');
        $this->assertProperty($ij, "$beer:$beers:beers[0].percentage", '8%');

        $this->assertProperty($ij, "$beer:$beers:beers[1].name", 'NATTE');
        $this->assertProperty($ij, "$beer:$beers:beers[1].percentage", '6.5%');

        $this->assertProperty($ij, "$beer:$beers:beers[2].name", 'IJWIT');
        $this->assertProperty($ij, "$beer:$beers:beers[2].percentage", '6.5%');
    }

    /** @test */
    function mapping_objects_without_properties_as_class_name_only()
    {
        $purse = new Purse('xyz', new Coins(
            new Copper(),
            new Silver(),
            new Silver(),
            new Gold()
        ));

        [$entity] = Extract::state()->from(IdentityMap::with([$purse]));

        $this->assertProperty($entity, Copper::class . ':' . Coins::class . ':coins[0]', null);
        $this->assertProperty($entity, Silver::class . ':' . Coins::class . ':coins[1]', null);
        $this->assertProperty($entity, Silver::class . ':' . Coins::class . ':coins[2]', null);
        $this->assertProperty($entity, Gold::class . ':' . Coins::class . ':coins[3]', null);
    }

    /** @test */
    function mapping_entities_without_properties_as_class_name_only()
    {
        $copper = new Copper();

        [$entity] = Extract::state()->from(IdentityMap::with([$copper]));

        $this->assertEmpty($entity->properties());
    }

    /** @test */
    function handling_nested_value_objects()
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

    /** @test */
    function extracting_the_state_of_an_object_with_a_map()
    {
        $object = new Map();
        $object->map = [
            '1].foo:bar.baz[0' => 1,
            '1].foo:bar.\\baz{1}[1' => 1,
        ];

        [$entity] = Extract::stringifying(UuidInterface::class)
            ->from(IdentityMap::with([$object]));

        $this->assertProperty($entity, 'array:map[1\\].foo:bar.baz\\[0]', 1);
        $this->assertProperty($entity, 'array:map[1\\].foo:bar.\\\\baz{1}\\[1]', 1);
    }

    /** @test */
    function extracting_the_state_of_an_object_with_map_access()
    {
        $object = new ArrayObject();
        $object['1].foo:bar.baz[0'] = 1;
        $object['1].foo:bar.\\baz{1}[1'] = 1;

        [$entity] = Extract::stringifying(UuidInterface::class)
            ->from(IdentityMap::with([$object]));

        $this->assertProperty($entity, 'ArrayObject[1\\].foo:bar.baz\\[0]', 1);
        $this->assertProperty($entity, 'ArrayObject[1\\].foo:bar.\\\\baz{1}\\[1]', 1);
    }

    /** @test */
    function retaining_the_identity_map_in_the_result()
    {
        $map = IdentityMap::with([new Copper()]);

        $state = Extract::state()->from($map);

        $this->assertSame($map, $state->identityMap());
    }
}
