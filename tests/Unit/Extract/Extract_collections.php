<?php declare(strict_types=1);

namespace Stratadox\EntityState\Test\Unit\Extract;

use PHPUnit\Framework\TestCase;
use Stratadox\EntityState\Extract;
use Stratadox\EntityState\Test\Fixture\Beer\Beer;
use Stratadox\EntityState\Test\Fixture\Beer\Beers;
use Stratadox\EntityState\Test\Fixture\Beer\Brewery;
use Stratadox\EntityState\Test\Fixture\FooBar\Bar;
use Stratadox\EntityState\Test\Fixture\FooBar\Foo;
use Stratadox\EntityState\Test\Fixture\ListOfStrings\ListOfStrings;
use Stratadox\EntityState\Test\Support\PropertyAsserting;
use Stratadox\IdentityMap\IdentityMap;

/**
 * @covers \Stratadox\EntityState\Extract
 * @covers \Stratadox\EntityState\Internal\CollectionExtractor
 */
class Extract_collections extends TestCase
{
    use PropertyAsserting;

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
}
