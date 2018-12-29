<?php declare(strict_types=1);

namespace Stratadox\EntityState\Test\Unit\Extract;
use PHPUnit\Framework\TestCase;
use Stratadox\EntityState\AnEntity;
use Stratadox\EntityState\Extract;
use Stratadox\EntityState\Test\Fixture\Guests\Guest;
use Stratadox\EntityState\Test\Fixture\Guests\Referrer;
use Stratadox\EntityState\Test\Fixture\SpyAgency\Agency;
use Stratadox\EntityState\Test\Fixture\SpyAgency\Cover;
use Stratadox\EntityState\Test\Fixture\SpyAgency\Spy;
use Stratadox\IdentityMap\IdentityMap;

/**
 * @covers \Stratadox\EntityState\Extract
 * @covers \Stratadox\EntityState\Internal\ExtractionRequest
 * @covers \Stratadox\EntityState\Internal\NewEntityDetector
 */
class Extract_new_related_entities extends TestCase
{
    /** @test */
    function extracting_a_newly_created_entity_that_is_not_in_the_identity_map()
    {
        $referrer = Referrer::fromFirstReferral('stratadox.com', '127.0.0.1');
        $map = IdentityMap::with([
            'stratadox.com' => $referrer,
            '127.0.0.1' => $referrer->referrals()[0]
        ]);
        $referrer->refer('192.0.2.0');

        $entityStates = Extract::state()
            ->consideringIt(AnEntity::whenEncountering(Guest::class))
            ->from($map);

        $this->assertSame('stratadox.com', $entityStates[0]->id());
        $this->assertSame('127.0.0.1', $entityStates[1]->id());
        $this->assertNull($entityStates[2]->id());
    }

    /** @test */
    function extracting_a_new_entity_and_figuring_out_its_id()
    {
        $referrer = Referrer::fromFirstReferral('stratadox.com', '127.0.0.1');
        $map = IdentityMap::with([
            'stratadox.com' => $referrer,
            '127.0.0.1' => $referrer->referrals()[0]
        ]);
        $referrer->refer('192.0.2.0');

        $entityStates = Extract::state()
            ->consideringIt(
                AnEntity::whenEncountering(Guest::class, 'ipAddress')
            )
            ->from($map);

        $this->assertSame('stratadox.com', $entityStates[0]->id());
        $this->assertSame('127.0.0.1', $entityStates[1]->id());
        $this->assertSame('192.0.2.0', $entityStates[2]->id());
    }

    /** @test */
    function not_adding_the_new_entity_to_the_identity_map()
    {
        $referrer = Referrer::fromFirstReferral('stratadox.com', '127.0.0.1');
        $map = IdentityMap::with([
            'stratadox.com' => $referrer,
            '127.0.0.1' => $referrer->referrals()[0]
        ]);
        $referrer->refer('192.0.2.0');
        $this->assertFalse($map->hasThe($referrer->referrals()[1]));

        $entityStates = Extract::state()
            ->consideringIt(AnEntity::whenEncountering(Guest::class))
            ->from($map);

        $map = $entityStates->identityMap();
        $this->assertFalse($map->hasThe($referrer->referrals()[1]));
    }

    /** @test */
    function extracting_new_entities_made_by_new_entities()
    {
        $agency = Agency::named('Secret Spy Service');
        $agency->hireSpyNamed('James Front');
        $agency->giveCoverIdentityTo('James Front', 'John Doe');

        $state = Extract::state()->consideringIt(
            AnEntity::whenEncountering(Spy::class, 'name'),
            AnEntity::whenEncountering(Cover::class, 'name')
        )->from(IdentityMap::with([$agency->name() => $agency]));

        $this->assertCount(3, $state);

        $this->assertSame(Agency::class, $state[0]->class());
        $this->assertSame('Secret Spy Service', $state[0]->id());

        $this->assertSame(Spy::class, $state[1]->class());
        $this->assertSame('James Front', $state[1]->id());

        $this->assertSame(Cover::class, $state[2]->class());
        $this->assertSame('John Doe', $state[2]->id());
    }
}
