<?php declare(strict_types=1);

namespace Stratadox\EntityState\Internal;

use function array_merge as these;
use function assert;
use function is_iterable;
use Stratadox\EntityState\PropertyState;

final class CollectionExtractor implements Extractor
{
    private $next;

    private function __construct(Extractor $next)
    {
        $this->next = $next;
    }

    public static function withAlternative(Extractor $next): Extractor
    {
        return new self($next);
    }

    public function extract(
        ExtractionRequest $request,
        Extractor $base = null
    ): array {
        if (!is_iterable($request->value())) {
            return $this->next->extract($request, $base);
        }
        assert($base !== null);
        $properties = [];
        $count = 0;
        foreach ($request->value() as $key => $value) {
            $properties[] = $base->extract($request->forCollectionItem(
                $request->value(),
                (string) $key,
                $value
            ));
            $count++;
        }
        return these(
            [PropertyState::with((string) $request->nameForCounting(), $count)],
            ...$properties
        );
    }
}
