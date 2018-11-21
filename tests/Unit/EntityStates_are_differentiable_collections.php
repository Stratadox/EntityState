<?php
declare(strict_types=1);

namespace Stratadox\EntityState\Test\Unit;

use PHPUnit\Framework\TestCase;
use Stratadox\EntityState\Changes;
use Stratadox\EntityState\EntityStates;
use Stratadox\EntityState\EntityState;
use Stratadox\EntityState\ListsEntityStates;
use Stratadox\EntityState\PropertyStates;
use Stratadox\EntityState\PropertyState;
use Stratadox\EntityState\TellsWhatChanged;
use Stratadox\EntityState\Test\Fixture\Beer\Beer;
use Stratadox\EntityState\Test\Fixture\Beer\Beers;
use Stratadox\EntityState\Test\Fixture\Beer\Brewery;
use Stratadox\EntityState\Test\Fixture\FooBar\Baz;
use Stratadox\EntityState\Test\Fixture\FooBar\Foo;

/**
 * @covers \Stratadox\EntityState\EntityStates
 */
class EntityStates_are_differentiable_collections extends TestCase
{
    /** @test */
    function getting_properties_from_the_list()
    {
        $foo123 = EntityState::ofThe(Foo::class, '123', PropertyStates::list(
            PropertyState::with('id', 123),
            PropertyState::with('name', 'Foo!')
        ));
        $foo456 = EntityState::ofThe(Foo::class, '456', PropertyStates::list(
            PropertyState::with('id', 456),
            PropertyState::with('name', 'To Foo or not to Foo')
        ));

        $entities = EntityStates::list($foo123, $foo456);

        $this->assertSame($foo123, $entities[0]);
        $this->assertSame($foo456, $entities[1]);
    }

    /** @test */
    function looping_through_properties()
    {
        $foo123 = EntityState::ofThe(Foo::class, '123', PropertyStates::list(
            PropertyState::with('id', 123),
            PropertyState::with('name', 'Foo!')
        ));
        $foo456 = EntityState::ofThe(Foo::class, '456', PropertyStates::list(
            PropertyState::with('id', 456),
            PropertyState::with('name', 'To Foo or not to Foo')
        ));

        $entities = EntityStates::list($foo123, $foo456);

        $this->assertCount(2, $entities);
        foreach ($entities as $entity) {
            $this->assertContains($entity, [$foo123, $foo456]);
        }
    }

    /** @test */
    function having_these_entities()
    {
        $foo123 = EntityState::ofThe(Foo::class, '123', PropertyStates::list(
            PropertyState::with('id', 123),
            PropertyState::with('name', 'Foo!')
        ));
        $foo456 = EntityState::ofThe(Foo::class, '456', PropertyStates::list(
            PropertyState::with('id', 456),
            PropertyState::with('name', 'To Foo or not to Foo')
        ));

        $collection = EntityStates::list($foo123, $foo456);

        $this->assertTrue($collection->hasThe($foo123));
        $this->assertTrue($collection->hasThe($foo456));
    }

    /** @test */
    function lacking_those_entities()
    {
        $foo123 = EntityState::ofThe(Foo::class, '123', PropertyStates::list(
            PropertyState::with('id', 123),
            PropertyState::with('name', 'Foo!')
        ));
        $foo456 = EntityState::ofThe(Foo::class, '456', PropertyStates::list(
            PropertyState::with('id', 456),
            PropertyState::with('name', 'Foo!')
        ));
        $baz456 = EntityState::ofThe(Baz::class, '456', PropertyStates::list(
            PropertyState::with('id', 456)
        ));
        $foo789 = EntityState::ofThe(Foo::class, '789', PropertyStates::list(
            PropertyState::with('id', 456),
            PropertyState::with('name', 'Foo!')
        ));

        $collection = EntityStates::list($foo123, $baz456);

        $this->assertFalse($collection->hasThe($foo456));
        $this->assertFalse($collection->hasThe($foo789));
    }

    /** @test */
    function having_the_entities_with_these_ids()
    {
        $foo123 = EntityState::ofThe(Foo::class, '123', PropertyStates::list(
            PropertyState::with('id', 123),
            PropertyState::with('name', 'Foo!')
        ));
        $foo456 = EntityState::ofThe(Foo::class, '456', PropertyStates::list(
            PropertyState::with('id', 456),
            PropertyState::with('name', 'To Foo or not to Foo')
        ));

        $collection = EntityStates::list($foo123, $foo456);

        $alsoFoo123 = EntityState::ofThe(Foo::class, '123', PropertyStates::list(
            PropertyState::with('id', 123),
            PropertyState::with('name', 'Different Foo?')
        ));
        $alsoFoo456 = EntityState::ofThe(Foo::class, '456', PropertyStates::list(
            PropertyState::with('id', 456),
            PropertyState::with('name', 'Another different Foo?')
        ));

        $this->assertTrue($collection->hasThe($alsoFoo123));
        $this->assertTrue($collection->hasThe($alsoFoo456));
    }

    /** @test */
    function finding_the_entities_that_are_not_in_another_collection()
    {
        $foo123 = EntityState::ofThe(Foo::class, '123', PropertyStates::list(
            PropertyState::with('id', 123),
            PropertyState::with('name', 'Foo!')
        ));
        $foo456 = EntityState::ofThe(Foo::class, '456', PropertyStates::list(
            PropertyState::with('id', 456),
            PropertyState::with('name', 'To Foo or not to Foo')
        ));

        $collection = EntityStates::list($foo123, $foo456);
        $otherCollection = EntityStates::list($foo123);

        $this->assertEquals(
            EntityStates::list($foo456),
            $collection->entitiesThatAreNotIn($otherCollection)
        );
    }

    /** @test */
    function determining_based_on_id_whether_the_entity_is_in_the_other_collection()
    {
        $fooABC = EntityState::ofThe(Foo::class, 'ABC', PropertyStates::list(
            PropertyState::with('id', 'ABC'),
            PropertyState::with('name', 'Foo ABC')
        ));
        $fooDEF = EntityState::ofThe(Foo::class, 'DEF', PropertyStates::list(
            PropertyState::with('id', 'DEF'),
            PropertyState::with('name', 'Foo DEF')
        ));

        $collection = EntityStates::list($fooABC, $fooDEF);
        $otherCollection = EntityStates::list(
            EntityState::ofThe(Foo::class, 'DEF', PropertyStates::list(
                PropertyState::with('id', 'DEF'),
                PropertyState::with('name', 'To Foo or not to Foo')
            ))
        );

        $this->assertEquals(
            EntityStates::list($fooABC),
            $collection->entitiesThatAreNotIn($otherCollection)
        );
    }

    /** @test */
    function determining_based_on_id_and_class_whether_the_entity_is_in_the_other_collection()
    {
        $fooABC = EntityState::ofThe(Foo::class, 'ABC', PropertyStates::list(
            PropertyState::with('id', 'ABC'),
            PropertyState::with('name', 'Foo ABC')
        ));
        $fooDEF = EntityState::ofThe(Foo::class, 'DEF', PropertyStates::list(
            PropertyState::with('id', 'DEF'),
            PropertyState::with('name', 'Foo DEF')
        ));

        $collection = EntityStates::list($fooABC, $fooDEF);
        $otherCollection = EntityStates::list(
            EntityState::ofThe(Baz::class, 'DEF', PropertyStates::list(
                PropertyState::with('id', 'DEF')
            ))
        );

        $this->assertEquals(
            EntityStates::list($fooABC, $fooDEF),
            $collection->entitiesThatAreNotIn($otherCollection)
        );
    }

    /** @test */
    function noticing_that_the_entity_changed()
    {
        $fooABC = EntityState::ofThe(Foo::class, 'ABC', PropertyStates::list(
            PropertyState::with('id', 'ABC'),
            PropertyState::with('name', 'Foo ABC')
        ));
        $fooDEF = EntityState::ofThe(Foo::class, 'DEF', PropertyStates::list(
            PropertyState::with('id', 'DEF'),
            PropertyState::with('name', 'Foo DEF')
        ));

        $original = EntityStates::list($fooABC, $fooDEF);

        $fooABC = EntityState::ofThe(Foo::class, 'ABC', PropertyStates::list(
            PropertyState::with('id', 'ABC'),
            PropertyState::with('name', 'ABC de la Foo')
        ));

        $this->assertTrue(
            $original->hasADifferent($fooABC)
        );
    }

    /** @test */
    function noticing_that_the_entity_did_not_change()
    {
        $fooABC = EntityState::ofThe(Foo::class, 'ABC', PropertyStates::list(
            PropertyState::with('id', 'ABC'),
            PropertyState::with('name', 'Foo ABC')
        ));
        $fooDEF = EntityState::ofThe(Foo::class, 'DEF', PropertyStates::list(
            PropertyState::with('id', 'DEF'),
            PropertyState::with('name', 'Foo DEF')
        ));

        $original = EntityStates::list($fooABC, $fooDEF);

        $fooABC = EntityState::ofThe(Foo::class, 'ABC', PropertyStates::list(
            PropertyState::with('id', 'ABC'),
            PropertyState::with('name', 'Foo ABC')
        ));

        $this->assertFalse(
            $original->hasADifferent($fooABC)
        );
    }

    /** @test */
    function checking_which_properties_changed()
    {
        $original = EntityStates::list(
            EntityState::ofThe(Foo::class, 'ABC', PropertyStates::list(
                PropertyState::with('id', 'ABC'),
                PropertyState::with('name', 'Foo ABC')
            )),
            EntityState::ofThe(Foo::class, 'DEF', PropertyStates::list(
                PropertyState::with('id', 'DEF'),
                PropertyState::with('name', 'Foo DEF')
            ))
        );
        $updated = EntityStates::list(
            EntityState::ofThe(Foo::class, 'ABC', PropertyStates::list(
                PropertyState::with('id', 'ABC'),
                PropertyState::with('name', 'Foo ABC')
            )),
            EntityState::ofThe(Foo::class, 'DEF', PropertyStates::list(
                PropertyState::with('id', 'DEF'),
                PropertyState::with('name', 'New name!')
            ))
        );

        $this->assertEquals(
            EntityStates::list(
                EntityState::ofThe(Foo::class, 'DEF', PropertyStates::list(
                    PropertyState::with('name', 'New name!')
                ))
            ),
            $updated->entityStateThatDiffersFrom($original)
        );
    }

    /** @test */
    function adding_entity_states_to_the_collection()
    {
        $fooState = EntityState::ofThe(Foo::class, 'DEF', PropertyStates::list(
            PropertyState::with('id', 'DEF'),
            PropertyState::with('name', 'Foo DEF')
        ));
        $entityStates = EntityStates::list(
            EntityState::ofThe(Foo::class, 'ABC', PropertyStates::list(
                PropertyState::with('id', 'ABC'),
                PropertyState::with('name', 'Foo ABC')
            ))
        )->add($fooState);

        $this->assertCount(2, $entityStates);
        $this->assertTrue($entityStates->hasThe($fooState));
    }

    /** @test */
    function adding_entity_states_overwrites_previous_values()
    {
        $entityStates = EntityStates::list(
            EntityState::ofThe(Foo::class, 'ABC', PropertyStates::list(
                PropertyState::with('id', 'ABC'),
                PropertyState::with('name', 'Foo ABC')
            )),
            EntityState::ofThe(Foo::class, 'DEF', PropertyStates::list(
                PropertyState::with('id', 'DEF'),
                PropertyState::with('name', 'Foo DEF')
            ))
        )->add(EntityState::ofThe(Foo::class, 'DEF', PropertyStates::list(
            PropertyState::with('id', 'DEF'),
            PropertyState::with('name', 'Updated name')
        )));

        $this->assertCount(2, $entityStates);
        $this->assertSame(
            'Updated name',
            $entityStates[1]->properties()[1]->value()
        );
    }

    /** @test */
    function adding_entity_states_retains_previous_properties_that_are_not_in_the_new_state()
    {
        $entityStates = EntityStates::list(
            EntityState::ofThe(Beer::class, '0', PropertyStates::list(
                PropertyState::with('name', 'Some beer'),
                PropertyState::with('percentage', '5%')
            )),
            EntityState::ofThe(Beer::class, '1', PropertyStates::list(
                PropertyState::with('name', 'Original name'),
                PropertyState::with('percentage', '5.5%')
            ))
        )->add(EntityState::ofThe(Beer::class, '1', PropertyStates::list(
            PropertyState::with('name', 'Updated name')
        )));

        $this->assertCount(2, $entityStates);
        $this->assertCount(2, $entityStates[1]->properties());
        $this->assertTrue(
            $entityStates[1]->properties()[1]->isSameAs(
                PropertyState::with('percentage', '5.5%')
            )
        );
    }

    /**
     * @test
     * @dataProvider differences
     */
    function computing_the_difference_between_entity_states(
        ListsEntityStates $oldState,
        ListsEntityStates $newState,
        TellsWhatChanged $expectedChanges
    ) {
        $this->assertEquals(
            $expectedChanges,
            $newState->changesSince($oldState)
        );
    }

    public function differences(): array
    {
        $beers = Beers::class;
        $beer = Beer::class;
        return [
            'Foo' => [
                EntityStates::list(
                    EntityState::ofThe(Foo::class, '123', PropertyStates::list(
                        PropertyState::with('id', 123),
                        PropertyState::with('name', 'Foo!')
                    )),
                    EntityState::ofThe(Foo::class, '456', PropertyStates::list(
                        PropertyState::with('id', 456),
                        PropertyState::with('name', 'To Foo or not to Foo')
                    ))
                ),
                EntityStates::list(
                    EntityState::ofThe(Foo::class, '456', PropertyStates::list(
                        PropertyState::with('id', 456),
                        PropertyState::with('name', 'To Foo or not to Foo?')
                    )),
                    EntityState::ofThe(Foo::class, '789', PropertyStates::list(
                        PropertyState::with('id', 789),
                        PropertyState::with('name', 'Foo de la Foo')
                    ))
                ),
                Changes::wereMade(
                    EntityStates::list(
                        EntityState::ofThe(Foo::class, '789', PropertyStates::list(
                            PropertyState::with('id', 789),
                            PropertyState::with('name', 'Foo de la Foo')
                        ))
                    ),
                    EntityStates::list(
                        EntityState::ofThe(Foo::class, '456', PropertyStates::list(
                            PropertyState::with('name', 'To Foo or not to Foo?')
                        ))
                    ),
                    EntityStates::list(
                        EntityState::ofThe(Foo::class, '123', PropertyStates::list(
                            PropertyState::with('id', 123),
                            PropertyState::with('name', 'Foo!')
                        ))
                    )
                )
            ],
            'Brewery' => [
                EntityStates::list(
                    EntityState::ofThe(Brewery::class, "Brouwerij 't IJ", PropertyStates::list(
                        PropertyState::with('name', "Brouwerij 't IJ"),
                        PropertyState::with("$beer:$beers:beers[0].name", 'ZATTE'),
                        PropertyState::with("$beer:$beers:beers[0].percentage", '8%')
                    ))
                ),
                EntityStates::list(
                    EntityState::ofThe(Brewery::class, "Brouwerij 't IJ", PropertyStates::list(
                        PropertyState::with('name', "Brouwerij 't IJ"),
                        PropertyState::with("$beer:$beers:beers[0].name", 'ZATTE'),
                        PropertyState::with("$beer:$beers:beers[0].percentage", '8%'),
                        PropertyState::with("$beer:$beers:beers[1].name", 'NATTE'),
                        PropertyState::with("$beer:$beers:beers[1].percentage", '6.5%'),
                        PropertyState::with("$beer:$beers:beers[1].name", 'IJWIT'),
                        PropertyState::with("$beer:$beers:beers[1].percentage", '6.5%')
                    ))
                ),
                Changes::wereMade(
                    EntityStates::list(),
                    EntityStates::list(
                        EntityState::ofThe(Brewery::class, "Brouwerij 't IJ", PropertyStates::list(
                            PropertyState::with("$beer:$beers:beers[1].name", 'NATTE'),
                            PropertyState::with("$beer:$beers:beers[1].percentage", '6.5%'),
                            PropertyState::with("$beer:$beers:beers[1].name", 'IJWIT'),
                            PropertyState::with("$beer:$beers:beers[1].percentage", '6.5%')
                        ))
                    ),
                    EntityStates::list()
                )
            ],
        ];
    }
}
