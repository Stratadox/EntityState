<?php declare(strict_types=1);

namespace Stratadox\EntityState\Test\Unit;

use BadMethodCallException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Stratadox\EntityState\EntityStates;
use Stratadox\EntityState\Extract;
use Stratadox\EntityState\ListsEntityStates;
use Stratadox\EntityState\RepresentsEntity;
use Stratadox\EntityState\StateRepresentation;
use Stratadox\EntityState\Test\Fixture\Coin\Copper;
use Stratadox\EntityState\Test\Fixture\Coin\Silver;
use Stratadox\IdentityMap\IdentityMap;

/**
 * @covers \Stratadox\EntityState\StateRepresentation
 */
class StateRepresentation_contains_entities_and_a_map extends TestCase
{
    /** @test */
    function retrieving_the_entity_states_from_the_result()
    {
        $entities = EntityStates::list();
        $map = IdentityMap::startEmpty();

        $state = StateRepresentation::with($entities, $map);

        $this->assertSame($entities, $state->entityStates());
    }

    /** @test */
    function retrieving_the_identity_map_from_the_result()
    {
        $entities = EntityStates::list();
        $map = IdentityMap::startEmpty();

        $state = StateRepresentation::with($entities, $map);

        $this->assertSame($map, $state->identityMap());
    }

    /** @test */
    function retrieving_an_entity_state_from_the_result()
    {
        $map = IdentityMap::with([new Copper()]);

        $state = Extract::state()->from($map);

        $this->assertTrue(isset($state[0]));
        $this->assertInstanceOf(RepresentsEntity::class, $state[0]);
        $this->assertSame(Copper::class, $state[0]->class());
    }

    /** @test */
    function looping_through_the_entity_states_of_the_result()
    {
        $map = IdentityMap::with([
            'coin 1' => new Copper(),
            'coin 2' => new Copper(),
            'coin 3' => new Silver()
        ]);

        $state = Extract::state()->from($map);

        $count = 0;
        foreach ($state as $i => $entity) {
            $this->assertContains($entity->class(), [Copper::class, Silver::class]);
            ++$count;
        }
        $this->assertSame(3, $count);
    }

    /** @test */
    function cannot_remove_elements_from_the_result()
    {
        $map = IdentityMap::with([new Copper()]);

        $state = Extract::state()->from($map);

        $this->expectException(BadMethodCallException::class);
        unset($state[0]);
    }

    /** @test */
    function cannot_mutate_elements_in_the_result()
    {
        $map = IdentityMap::with([new Copper()]);

        $state = Extract::state()->from($map);

        $this->expectException(BadMethodCallException::class);
        $state[0] = true;
    }

    /** @test */
    function removal_and_mutation_restrictions_are_enforced_externally()
    {
        /** @var MockObject|ListsEntityStates $entities */
        $entities = $this->createMock(ListsEntityStates::class);
        $entities->expects($this->once())->method('offsetUnset');
        $entities->expects($this->once())->method('offsetSet');

        $state = StateRepresentation::with($entities, IdentityMap::startEmpty());

        unset($state[0]);
        $state[0] = true;
    }

    /** @test */
    function adding_additional_state()
    {
        $originalCoin = new Copper();
        $originalMap = IdentityMap::with(['coin 1' => $originalCoin]);
        $originalState = Extract::state()->from($originalMap);

        $extraCoin = new Copper();
        $newMap = $originalMap->add('coin 2', $extraCoin);
        $extraState = Extract::state()->fromOnly($newMap, $extraCoin);

        $combinedState = $originalState->add($extraState);

        $this->assertCount(2, $combinedState);

        $this->assertTrue($combinedState->identityMap()->hasThe($originalCoin));
        $this->assertTrue($combinedState->identityMap()->hasThe($extraCoin));
    }

    /** @test */
    function adding_only_the_right_additional_state()
    {
        $originalCoin = new Copper();
        $originalMap = IdentityMap::with(['coin 1' => $originalCoin]);
        $originalState = Extract::state()->from($originalMap);

        $extraCoin = new Copper();
        $ignoredCoin = new Copper();
        $newMap = $originalMap
            ->add('coin 2', $extraCoin)
            ->add('coin 3', $ignoredCoin);
        $extraState = Extract::state()->fromOnly($newMap, $originalCoin, $extraCoin);

        $combinedState = $originalState->add($extraState);

        $this->assertCount(2, $combinedState);

        $this->assertTrue($combinedState->identityMap()->hasThe($originalCoin));
        $this->assertTrue($combinedState->identityMap()->hasThe($extraCoin));
        $this->assertFalse($combinedState->identityMap()->hasThe($ignoredCoin));
    }

    /** @test */
    function computing_the_differences_between_this_result_and_another_state()
    {
        $originalCoin = new Copper();
        $originalMap = IdentityMap::with(['coin 1' => $originalCoin]);
        $originalState = Extract::state()->from($originalMap);

        $newCoin = new Silver();
        $newMap = IdentityMap::with(['coin 1' => $newCoin]);
        $newState = Extract::state()->from($newMap);

        $difference = $newState->changesSince($originalState);

        $this->assertSame(Copper::class, $difference->removed()[0]->class());
        $this->assertSame('coin 1', $difference->removed()[0]->id());

        $this->assertSame(Silver::class, $difference->added()[0]->class());
        $this->assertSame('coin 1', $difference->added()[0]->id());
    }
}
