<?php declare(strict_types=1);

namespace Stratadox\EntityState\Test\Unit\Extract;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Stratadox\EntityState\Extract;
use Stratadox\EntityState\Test\Fixture\FooBar\Baz;
use Stratadox\EntityState\Test\Fixture\FooBar\Id;
use Stratadox\EntityState\Test\Support\PropertyAsserting;
use Stratadox\IdentityMap\IdentityMap;

/**
 * @covers \Stratadox\EntityState\Extract
 * @covers \Stratadox\EntityState\Internal\Stringifier
 * @covers \Stratadox\EntityState\Internal\ShouldStringify
 */
class Extract_some_classes_as_string extends TestCase
{
    use PropertyAsserting;

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
}
