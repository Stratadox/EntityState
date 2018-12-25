<?php declare(strict_types=1);

namespace Stratadox\EntityState\Test\Unit\Extract;

use PHPUnit\Framework\TestCase;
use Stratadox\EntityState\Extract;
use Stratadox\EntityState\Test\Fixture\RentalCar\Aspects;
use Stratadox\EntityState\Test\Fixture\RentalCar\Branding;
use Stratadox\EntityState\Test\Fixture\RentalCar\Make;
use Stratadox\EntityState\Test\Fixture\RentalCar\Model;
use Stratadox\EntityState\Test\Fixture\RentalCar\Space;
use Stratadox\EntityState\Test\Support\PropertyAsserting;
use Stratadox\IdentityMap\IdentityMap;

/**
 * @covers \Stratadox\EntityState\Extract
 * @covers \Stratadox\EntityState\Internal\ExtractionRequest
 * @covers \Stratadox\EntityState\Internal\EntityReferenceExtractor
 */
class Extract_related_entities extends TestCase
{
    use PropertyAsserting;

    /** @test */
    function mapping_other_entities_as_their_identifier()
    {
        $opelCar = new Model(
            new Branding(new Make('Opel'), 'Corsa E', 2015),
            new Aspects(new Space(4, 4, 2), 115, false)
        );

        $fiatCar = new Model(new Branding(
            new Make('Fiat'), '500', 2007),
            new Aspects(new Space(4, 2, 1), 69, false),
            $opelCar
        );

        [$fiat] = Extract::state()->from(IdentityMap::with([
            'Fiat 500' => $fiatCar,
            'Opel Corsa' => $opelCar,
        ]));

        $this->assertSame($opelCar, $fiatCar->largerModel());
        $this->assertProperty($fiat, Model::class . ':largerModel', 'Opel Corsa');
    }

}
