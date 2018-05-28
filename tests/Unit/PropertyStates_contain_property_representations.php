<?php
declare(strict_types=1);

namespace Stratadox\EntityState\Test\Unit;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Stratadox\EntityState\PropertyStates;
use Stratadox\EntityState\PropertyState;

/**
 * @covers \Stratadox\EntityState\PropertyStates
 */
class PropertyStates_contain_property_representations extends TestCase
{
    /** @test */
    function getting_properties_from_the_list()
    {
        $fooBar = PropertyState::with('foo', 'bar');
        $barBaz = PropertyState::with('bar', 'baz');

        $properties = PropertyStates::list($fooBar, $barBaz);

        $this->assertSame($fooBar, $properties[0]);
        $this->assertSame($barBaz, $properties[1]);
    }

    /** @test */
    function looping_through_properties()
    {
        $fooBar = PropertyState::with('foo', 'bar');
        $barBaz = PropertyState::with('bar', 'baz');

        $properties = PropertyStates::list($fooBar, $barBaz);

        $this->assertCount(2, $properties);
        foreach ($properties as $property) {
            $this->assertContains($property, [$fooBar, $barBaz]);
        }
    }

    /** @test */
    function checking_that_the_property_is_contained_in_the_collection()
    {
        $properties = PropertyStates::list(
            PropertyState::with('foo', 'bar'),
            PropertyState::with('bar', 'baz')
        );
        $this->assertTrue($properties->contains(PropertyState::with('foo', 'bar')));
    }

    /** @test */
    function checking_that_the_property_is_not_contained_in_the_collection()
    {
        $properties = PropertyStates::list(
            PropertyState::with('foo', 'bar'),
            PropertyState::with('bar', 'baz')
        );
        $this->assertFalse($properties->contains(PropertyState::with('foo', 'baz')));
    }

    /** @test */
    function checking_that_the_properties_are_different()
    {
        $properties1 = PropertyStates::list(
            PropertyState::with('foo', 'bar'),
            PropertyState::with('bar', 'baz')
        );
        $properties2 = PropertyStates::list(
            PropertyState::with('foo', 'bar'),
            PropertyState::with('bar', 'qux')
        );

        $this->assertTrue($properties1->areDifferentFrom($properties2));
    }

    /** @test */
    function checking_that_the_properties_are_the_same()
    {
        $properties1 = PropertyStates::list(
            PropertyState::with('foo', 'bar'),
            PropertyState::with('bar', 'baz')
        );
        $properties2 = PropertyStates::list(
            PropertyState::with('foo', 'bar'),
            PropertyState::with('bar', 'baz')
        );

        $this->assertFalse($properties1->areDifferentFrom($properties2));
    }

    /** @test */
    function merging_with_other_properties()
    {
        $originalState = PropertyStates::list(
            PropertyState::with('foo', 'Original foo'),
            PropertyState::with('bar', 'Original bar')
        );
        $newState = $originalState->merge(
            PropertyStates::list(
                PropertyState::with('foo', 'Updated foo'),
                PropertyState::with('baz', 'Added baz')
            )
        );

        $this->assertCount(3, $newState);
        $this->assertTrue(
            $newState[0]->isSameAs(
                PropertyState::with('foo', 'Updated foo')
            )
        );
        $this->assertTrue(
            $newState[1]->isSameAs(
                PropertyState::with('bar', 'Original bar')
            )
        );
        $this->assertTrue(
            $newState[2]->isSameAs(
                PropertyState::with('baz', 'Added baz')
            )
        );
    }

    /** @test */
    function checking_that_there_is_a_property_with_this_name_in_the_collection()
    {
        $properties = PropertyStates::list(
            PropertyState::with('foo', 'bar'),
            PropertyState::with('bar', 'baz')
        );

        $this->assertTrue($properties->hasOneNamed('foo'));
        $this->assertTrue($properties->hasOneNamed('bar'));
    }

    /** @test */
    function checking_that_there_is_no_property_with_this_name_in_the_collection()
    {
        $properties = PropertyStates::list(
            PropertyState::with('foo', 'bar'),
            PropertyState::with('bar', 'baz')
        );

        $this->assertFalse($properties->hasOneNamed('baz'));
        $this->assertFalse($properties->hasOneNamed('qux'));
    }

    /** @test */
    function getting_the_property_with_this_name()
    {
        $fooBar = PropertyState::with('foo', 'bar');
        $barBaz = PropertyState::with('bar', 'baz');

        $properties = PropertyStates::list($fooBar, $barBaz);

        $this->assertSame($fooBar, $properties->theOneNamed('foo'));
        $this->assertSame($barBaz, $properties->theOneNamed('bar'));
    }

    /** @test */
    function trying_to_get_a_non_existing_property()
    {
        $properties = PropertyStates::list(
            PropertyState::with('foo', 'bar'),
            PropertyState::with('bar', 'baz')
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage(
            'No such property: `qux`'
        );

        $properties->theOneNamed('qux');
    }
}
