<?php declare(strict_types=1);

namespace Stratadox\EntityState\Test\Unit\Extract;

use ArrayObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;
use Stratadox\EntityState\Extract;
use Stratadox\EntityState\Test\Fixture\Map\Map;
use Stratadox\EntityState\Test\Support\PropertyAsserting;
use Stratadox\IdentityMap\IdentityMap;

/**
 * @covers \Stratadox\EntityState\Extract
 * @covers \Stratadox\EntityState\Internal\Name
 */
class Extract_objects_with_associative_arrays extends TestCase
{
    use PropertyAsserting;

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
}
