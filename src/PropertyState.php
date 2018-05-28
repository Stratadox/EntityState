<?php
declare(strict_types=1);

namespace Stratadox\EntityState;

/**
 * Represents the state of a property.
 *
 * @author Stratadox
 */
final class PropertyState implements RepresentsProperty
{
    private $name;
    private $value;

    private function __construct(string $name, $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * Something something with.
     *
     * @param string $name
     * @param mixed  $value
     * @return RepresentsProperty
     */
    public static function with(string $name, $value): RepresentsProperty
    {
        return new self($name, $value);
    }

    /** @inheritdoc */
    public function name(): string
    {
        return $this->name;
    }

    /** @inheritdoc */
    public function value()
    {
        return $this->value;
    }

    /** @inheritdoc */
    public function isSameAs(RepresentsProperty $otherProperty): bool
    {
        return $this->name === $otherProperty->name()
            && $this->value === $otherProperty->value();
    }

    /** @inheritdoc */
    public function isDifferentInThe(ListsPropertyStates $otherProperties): bool
    {
        foreach ($otherProperties as $otherProperty) {
            if ($this->isSameAs($otherProperty)) {
                return false;
            }
        }
        return true;
    }
}
