<?php declare(strict_types=1);

namespace Stratadox\EntityState\Internal;

use Stratadox\EntityState\PropertyState;

final class EntityReferenceExtractor implements Extractor
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
        Extractor $baseExtractor = null
    ): array {
        if ($request->pointsToAnotherEntity()) {
            return [PropertyState::with(
                (string) $request->objectName(),
                $request->otherEntityId()
            )];
        }
        return $this->next->extract($request, $baseExtractor);
    }
}
