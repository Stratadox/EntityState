<?php declare(strict_types=1);

namespace Stratadox\EntityState\Internal;

use Stratadox\EntityState\PropertyState;

final class PropertyExtractor implements Extractor
{
    private $next;

    private function __construct(Extractor $next)
    {
        $this->next = $next;
    }

    public static function using(Extractor $next): self
    {
        return new self($next);
    }

    public function extract(
        ExtractionRequest $request,
        Extractor $baseExtractor = null
    ): array {
        if ($request->isRecursive()) {
            return [PropertyState::with(
                (string) $request->name(),
                [$request->visitedName()]
            )];
        }
        return $this->next->extract(
            $request->withVisitation(),
            $baseExtractor ?: $this
        );
    }
}
