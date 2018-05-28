<?php
declare(strict_types=1);

namespace Stratadox\EntityState\Test\Unit;

use PHPUnit\Framework\TestCase;
use Stratadox\EntityState\Changes;
use Stratadox\EntityState\EntityStates;
use Stratadox\EntityState\EntityState;
use Stratadox\EntityState\PropertyStates;
use Stratadox\EntityState\PropertyState;
use Stratadox\EntityState\Test\Fixture\FooBar\Foo;

/**
 * @covers \Stratadox\EntityState\Changes
 */
class Changes_contain_added_altered_and_removed_entities extends TestCase
{
    /**
     * @test
     * @dataProvider changes
     */
    function retrieving_the_added_entities($added, $altered, $removed)
    {
        $this->assertSame(
            $added,
            Changes::wereMade($added, $altered, $removed)->added()
        );
    }

    /**
     * @test
     * @dataProvider changes
     */
    function retrieving_the_altered_entities($added, $altered, $removed)
    {
        $this->assertSame(
            $altered,
            Changes::wereMade($added, $altered, $removed)->altered()
        );
    }

    /**
     * @test
     * @dataProvider changes
     */
    function retrieving_the_removed_entities($added, $altered, $removed)
    {
        $this->assertSame(
            $removed,
            Changes::wereMade($added, $altered, $removed)->removed()
        );
    }

    public function changes(): array
    {
        return [
            'Foo entities' => [
                'Added' => EntityStates::list(
                    EntityState::ofThe(Foo::class, '123', PropertyStates::list(
                        PropertyState::with('id', 123),
                        PropertyState::with('name', 'Foo!')
                    )),
                    EntityState::ofThe(Foo::class, '456', PropertyStates::list(
                        PropertyState::with('id', 456),
                        PropertyState::with('name', 'To Foo or not to Foo')
                    ))
                ),
                'Altered' => EntityStates::list(
                    EntityState::ofThe(Foo::class, '789', PropertyStates::list(
                        PropertyState::with('name', 'Foo de la Foo')
                    ))
                ),
                'Removed' => EntityStates::list(
                    EntityState::ofThe(Foo::class, '321', PropertyStates::list())
                )
            ]
        ];
    }
}
