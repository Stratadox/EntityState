<?php
declare(strict_types=1);

namespace Stratadox\EntityState\Test\Fixture;

use function sprintf;
use Stratadox\Hydrator\Hydrates;

final class StaticMethodCaller implements Hydrates
{
    private $class;
    private $call;

    public function __construct(string $class, string $method)
    {
        $this->class = $class;
        $this->call = sprintf('%s::%s', $class, $method);
    }

    public static function for(string $class, string $method): self
    {
        return new self($class, $method);
    }

    public function fromArray(array $input)
    {
        return ($this->call)($input);
    }

    public function classFor(array $input): string
    {
        return $this->class;
    }
}
