<?php declare(strict_types=1);

namespace Stratadox\EntityState\Test\Unit;

use PHPUnit\Framework\TestCase;
use Stratadox\EntityState\AnEntity;
use Stratadox\EntityState\Test\Fixture\Guests\Guest;

/**
 * @covers \Stratadox\EntityState\AnEntity
 */
class AnEntity_definition_recognises_entities extends TestCase
{
    /** @test */
    function recognising_the_defined_entity_type()
    {
        $definition = AnEntity::whenEncountering(Guest::class);
        $guest = Guest::withIp('127.0.0.1');

        $this->assertTrue($definition->recognises($guest));
    }

    /** @test */
    function not_recognising_an_undefined_entity_type()
    {
        $definition = AnEntity::whenEncountering(Guest::class);

        $this->assertFalse($definition->recognises($this));
    }

    /**
     * @test
     * @dataProvider ipAddresses
     */
    function finding_the_identifier_for_the_entity($ipAddress)
    {
        $definition = AnEntity::whenEncountering(Guest::class, 'ipAddress');
        $guest = Guest::withIp($ipAddress);

        $this->assertSame($ipAddress, $definition->idFor($guest));
    }

    /** @test */
    function returning_null_if_the_id_is_generated_later()
    {
        $definition = AnEntity::whenEncountering(Guest::class);
        $guest = Guest::withIp('127.0.0.1');

        $this->assertNull($definition->idFor($guest));
    }

    public function ipAddresses(): iterable
    {
        return ['127.0.0.1' => ['127.0.0.1'], '192.0.2.0' => ['192.0.2.0']];
    }
}
