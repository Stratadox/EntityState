<?php declare(strict_types=1);

namespace Stratadox\EntityState\Internal;

use function array_merge as these;
use function assert;
use function is_object;
use Stratadox\EntityState\PropertyState;

final class ObjectExtractor implements Extractor
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
        if (!is_object($request->value())) {
            return $this->next->extract($request, $base);
        }
        assert($base !== null);
        $properties = [];
        foreach (ReflectionProperties::ofThe($request->value()) as $property) {
            $properties[] = $base->extract(
                $request->forProperty($property),
                $base
            );
        }
        if (empty($properties)) {
            return $request->isTheOwner() ?
                [] :
                [PropertyState::with((string) $request->objectName(), null)];
        }
        return these(...$properties);
    }
}
