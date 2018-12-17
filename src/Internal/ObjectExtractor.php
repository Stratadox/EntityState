<?php declare(strict_types=1);

namespace Stratadox\EntityState\Internal;

use function array_merge as these;
use function assert;
use function is_object;
use Stratadox\EntityState\PropertyState;
use Stratadox\Specification\Contract\Satisfiable;

final class ObjectExtractor implements Extractor
{
    private $next;
    private $stringifier;

    private function __construct(Extractor $next, Satisfiable $constraint)
    {
        $this->next = $next;
        $this->stringifier = $constraint;
    }

    public static function withAlternative(Extractor $next): Extractor
    {
        return new self($next, Unsatisfiable::constraint());
    }

    public static function stringifyingWithAlternative(
        Satisfiable $constraint,
        Extractor $next
    ): Extractor {
        return new self($next, $constraint);
    }

    public function extract(
        ExtractionRequest $request,
        Extractor $base = null
    ): array {
        if (!is_object($request->value())) {
            return $this->next->extract($request, $base);
        }
        if ($this->stringifier->isSatisfiedBy($request->value())) {
            return [PropertyState::with(
                (string) $request->objectName(),
                (string) $request->value()
            )];
        }
        if ($request->pointsToAnotherEntity()) {
            return [PropertyState::with(
                (string) $request->objectName(),
                $request->otherEntityId()
            )];
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
