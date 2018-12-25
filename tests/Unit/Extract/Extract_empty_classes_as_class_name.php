<?php declare(strict_types=1);

namespace Stratadox\EntityState\Test\Unit\Extract;

use PHPUnit\Framework\TestCase;
use Stratadox\EntityState\Extract;
use Stratadox\EntityState\Test\Fixture\Coin\Coins;
use Stratadox\EntityState\Test\Fixture\Coin\Copper;
use Stratadox\EntityState\Test\Fixture\Coin\Gold;
use Stratadox\EntityState\Test\Fixture\Coin\Purse;
use Stratadox\EntityState\Test\Fixture\Coin\Silver;
use Stratadox\EntityState\Test\Support\PropertyAsserting;
use Stratadox\IdentityMap\IdentityMap;

/**
 * @covers \Stratadox\EntityState\Extract
 */
class Extract_empty_classes_as_class_name extends TestCase
{
    use PropertyAsserting;

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
}
