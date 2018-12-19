<?php declare(strict_types=1);

namespace Stratadox\EntityState\Internal;

use Stratadox\EntityState\PropertyState;
use Stratadox\Specification\Contract\Satisfiable;

final class Stringifier implements Extractor
{
    private $next;
    private $condition;

    private function __construct(Extractor $next, Satisfiable $condition)
    {
        $this->next = $next;
        $this->condition = $condition;
    }

    public static function withCondition(
        Satisfiable $constraint,
        Extractor $next
    ): Extractor {
        return new self($next, $constraint);
    }

    public function extract(
        ExtractionRequest $request,
        Extractor $baseExtractor = null
    ): array {
        if ($this->condition->isSatisfiedBy($request->value())) {
            return [PropertyState::with(
                (string) $request->objectName(),
                (string) $request->value()
            )];
        }
        return $this->next->extract($request, $baseExtractor);
    }
}
