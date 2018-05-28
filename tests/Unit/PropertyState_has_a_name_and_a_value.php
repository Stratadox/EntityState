<?php
declare(strict_types=1);

namespace Stratadox\EntityState\Test\Unit;

use PHPUnit\Framework\TestCase;
use Stratadox\EntityState\PropertyState;
use Stratadox\EntityState\PropertyStates;

/**
 * @covers \Stratadox\EntityState\PropertyState
 */
class PropertyState_has_a_name_and_a_value extends TestCase
{
    /**
     * @test
     * @dataProvider nameValuePairs
     */
    function property_has_a_name($name, $value)
    {
        $this->assertSame($name, PropertyState::with($name, $value)->name());
    }

    /**
     * @test
     * @dataProvider nameValuePairs
     */
    function property_has_a_value($name, $value)
    {
        $this->assertSame($value, PropertyState::with($name, $value)->value());
    }

    /** @test */
    function checking_that_two_properties_are_the_same()
    {
        $this->assertTrue(PropertyState::with('foo', 'bar')
            ->isSameAs(PropertyState::with('foo', 'bar')));
    }

    /** @test */
    function checking_that_two_properties_are_different()
    {
        $this->assertFalse(PropertyState::with('foo', 'bar')
            ->isSameAs(PropertyState::with('foo', 'baz')));
    }

    /** @test */
    function checking_that_the_property_is_the_same_in_the_collection()
    {
        $this->assertFalse(PropertyState::with('foo', 'bar')
            ->isDifferentInThe(PropertyStates::list(
                PropertyState::with('bar', 'baz'),
                PropertyState::with('foo', 'bar')
            )));
    }

    /** @test */
    function checking_that_the_property_is_different_in_the_collection()
    {
        $this->assertTrue(PropertyState::with('foo', 'bar')
            ->isDifferentInThe(PropertyStates::list(
                PropertyState::with('bar', 'baz'),
                PropertyState::with('foo', 'qux')
            )));
    }

    public function nameValuePairs(): array
    {
        return [
            'name = value' => ['name', 'value'],
            'foo = bar'    => ['foo', 'bar'],
            'x = 3'        => ['x', 3],
        ];
    }
}
