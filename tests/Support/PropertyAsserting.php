<?php declare(strict_types=1);

namespace Stratadox\EntityState\Test\Support;

use Stratadox\EntityState\RepresentsEntity;

trait PropertyAsserting
{
    private function assertProperty(
        RepresentsEntity $entity,
        string $expectedName,
        $expectedValue,
        string $extraMessage = ''
    ): void {
        $names = [];
        foreach ($entity->properties() as $property) {
            if ($property->name() === $expectedName) {
                $this->assertSame($expectedValue, $property->value(), sprintf(
                    'Failed to assert the value of %s `%s` property `%s`. %s',
                    $entity->class(),
                    $entity->id(),
                    $expectedName,
                    $extraMessage
                ));
                return;
            }
            $names[] = $property->name();
        }
        $this->fail(sprintf(
            'Failed to assert that the %s `%s` has a registered property `%s`. ' .
            'Found: %s%s%s%s',
            $entity->class(),
            $entity->id(),
            $expectedName,
            PHP_EOL,
            implode(PHP_EOL, $names),
            PHP_EOL,
            $extraMessage
        ));
    }

    abstract public function assertSame($expected, $actual, string $message = ''): void;
    abstract public function fail(string $message = ''): void;
}
