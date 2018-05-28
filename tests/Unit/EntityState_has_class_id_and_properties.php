<?php
declare(strict_types=1);

namespace Stratadox\EntityState\Test\Unit;

use PHPUnit\Framework\TestCase;
use Stratadox\EntityState\EntityState;
use Stratadox\EntityState\EntityStates;
use Stratadox\EntityState\PropertyStates;
use Stratadox\EntityState\PropertyState;
use Stratadox\EntityState\Test\Fixture\Beer\Beer;
use Stratadox\EntityState\Test\Fixture\FooBar\Bar;
use Stratadox\EntityState\Test\Fixture\FooBar\Foo;

/**
 * @covers \Stratadox\EntityState\EntityState
 */
class EntityState_has_class_id_and_properties extends TestCase
{
    /**
     * @test
     * @dataProvider entityData
     */
    function having_a_class($class, $id, $properties)
    {
        $this->assertSame(
            $class,
            EntityState::ofThe($class, $id, $properties)->class()
        );
    }

    /**
     * @test
     * @dataProvider entityData
     */
    function having_an_id($class, $id, $properties)
    {
        $this->assertSame(
            $id,
            EntityState::ofThe($class, $id, $properties)
                ->id()
        );
    }

    /**
     * @test
     * @dataProvider entityData
     */
    function having_the_own_class_and_id($class, $id, $properties)
    {
        $this->assertTrue(
            EntityState::ofThe($class, $id, $properties)
                ->hasTheSameIdentityAs(
                    EntityState::ofThe($class, $id, $properties)
                )
        );
    }

    /**
     * @test
     * @dataProvider entityData
     */
    function not_having_another_class($class, $id, $properties)
    {
        $this->assertFalse(
            EntityState::ofThe($class, $id, $properties)
                ->hasTheSameIdentityAs(
                    EntityState::ofThe('Foo\\' . $class, $id, $properties)
                )
        );
    }

    /**
     * @test
     * @dataProvider entityData
     */
    function not_having_a_different_id($class, $id, $properties)
    {
        $this->assertFalse(
            EntityState::ofThe($class, $id, $properties)
                ->hasTheSameIdentityAs(
                    EntityState::ofThe($class, $id . '+', $properties)
                )
        );
    }

    /**
     * @test
     * @dataProvider entityData
     */
    function having_property_representations($class, $id, $properties)
    {
        $this->assertSame(
            $properties,
            EntityState::ofThe($class, $id, $properties)
                ->properties()
        );
    }

    /** @test */
    function being_different_from_a_representation_of_another_class()
    {
        $foo = EntityState::ofThe(Foo::class, '1', PropertyStates::list());
        $bar = EntityState::ofThe(Bar::class, '1', PropertyStates::list());

        $this->assertTrue($foo->isDifferentFrom($bar));
    }

    /** @test */
    function being_different_from_a_representation_with_another_id()
    {
        $foo1 = EntityState::ofThe(Foo::class, '1', PropertyStates::list());
        $foo2 = EntityState::ofThe(Foo::class, '2', PropertyStates::list());

        $this->assertTrue($foo1->isDifferentFrom($foo2));
    }

    /** @test */
    function being_different_from_a_representation_with_other_property_values()
    {
        $foo1 = EntityState::ofThe(Foo::class, '123', PropertyStates::list(
            PropertyState::with('id', 123),
            PropertyState::with('name', 'Foo!')
        ));
        $foo2 = EntityState::ofThe(Foo::class, '123', PropertyStates::list(
            PropertyState::with('id', 123),
            PropertyState::with('name', 'New name!')
        ));

        $this->assertTrue($foo1->isDifferentFrom($foo2));
    }

    /** @test */
    function being_the_same_as_another_representation()
    {
        $foo1 = EntityState::ofThe(Foo::class, '123', PropertyStates::list(
            PropertyState::with('id', 123),
            PropertyState::with('name', 'Foo!')
        ));
        $foo2 = EntityState::ofThe(Foo::class, '123', PropertyStates::list(
            PropertyState::with('id', 123),
            PropertyState::with('name', 'Foo!')
        ));

        $this->assertFalse($foo1->isDifferentFrom($foo2));
    }

    /** @test */
    function extracting_the_subset_that_differs_from_the_entity_in_a_collection()
    {
        $foo = EntityState::ofThe(Foo::class, '1', PropertyStates::list(
            PropertyState::with('id', 1),
            PropertyState::with('name', 'New name!')
        ));
        $collection = EntityStates::list(
            EntityState::ofThe(Foo::class, '1', PropertyStates::list(
                PropertyState::with('id', 1),
                PropertyState::with('name', 'Foo!')
            ))
        );

        $this->assertEquals(
            EntityState::ofThe(Foo::class, '1', PropertyStates::list(
                PropertyState::with('name', 'New name!')
            )),
            $foo->subsetThatDiffersFrom($collection)
        );
    }

    /** @test */
    function the_subset_is_also_the_full_set_for_entities_not_in_the_collection()
    {
        $foo = EntityState::ofThe(Foo::class, '1', PropertyStates::list(
            PropertyState::with('id', 1),
            PropertyState::with('name', 'New name!')
        ));
        $collection = EntityStates::list(
            EntityState::ofThe(Foo::class, '123', PropertyStates::list(
                PropertyState::with('id', 123),
                PropertyState::with('name', 'Foo!')
            ))
        );

        $this->assertEquals(
            $foo,
            $foo->subsetThatDiffersFrom($collection)
        );
    }

    /** @test */
    function merging_with_another_entity_state()
    {
        $originalState = EntityState::ofThe(Beer::class, '1', PropertyStates::list(
            PropertyState::with('name', 'Original name'),
            PropertyState::with('percentage', '5.5%')
        ));
        $newState = $originalState->mergeWith(
            EntityState::ofThe(Beer::class, '1', PropertyStates::list(
                PropertyState::with('name', 'Updated name')
            ))
        );

        $this->assertTrue(
            $newState->properties()[0]->isSameAs(
                PropertyState::with('name', 'Updated name')
            )
        );
        $this->assertTrue(
            $newState->properties()[1]->isSameAs(
                PropertyState::with('percentage', '5.5%')
            )
        );
    }

    public function entityData(): array
    {
        return [
            'Foo 123' => [Foo::class, '123', PropertyStates::list(
                PropertyState::with('id', 123),
                PropertyState::with('name', 'Foo!')
            )],
            'Foo 456' => [Foo::class, '456', PropertyStates::list(
                PropertyState::with('id', 456),
                PropertyState::with('name', 'To Foo or not to Foo')
            )],
        ];
    }
}
