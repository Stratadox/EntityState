<?php
declare(strict_types=1);

namespace Stratadox\EntityState\Test\Fixture\Beer;

use Stratadox\Collection\Appendable;
use Stratadox\ImmutableCollection\Appending;
use Stratadox\ImmutableCollection\ImmutableCollection;

final class Beers extends ImmutableCollection implements Appendable
{
    use Appending;

    public function __construct(Beer ...$brands)
    {
        parent::__construct(...$brands);
    }

    public function current(): Beer
    {
        return parent::current();
    }
}
