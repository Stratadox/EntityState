<?php

namespace Stratadox\EntityState\Test\Fixture\AppleTree;

interface AppleFallObserver
{
    public function itFalls(Apple $apple): void;
}
