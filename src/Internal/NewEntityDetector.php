<?php declare(strict_types=1);

namespace Stratadox\EntityState\Internal;

use function is_object;
use Stratadox\EntityState\DefinesEntityType;
use Stratadox\EntityState\PropertyState;

final class NewEntityDetector implements Extractor
{
    private $next;
    private $newEntitiesProcessor;
    private $entityTypes;

    private function __construct(
        Extractor $next,
        AcceptsNewEntities $newEntities,
        DefinesEntityType ...$entityTypes
    ) {
        $this->next = $next;
        $this->newEntitiesProcessor = $newEntities;
        $this->entityTypes = $entityTypes;
    }

    public static function with(
        AcceptsNewEntities $newEntities,
        Extractor $next,
        DefinesEntityType ...$asEntities
    ): Extractor {
        return new self($next, $newEntities, ...$asEntities);
    }

    public function extract(
        ExtractionRequest $request,
        Extractor $baseExtractor = null
    ): array {
        $subject = $request->value();
        if (!is_object($subject) || $request->pointsToAKnownEntity()) {
            return $this->next->extract($request, $baseExtractor ?: $this);
        }
        foreach ($this->entityTypes as $definition) {
            if ($definition->recognises($subject)) {
                $id = $definition->idFor($subject);
                $this->newEntitiesProcessor->addAsNewEntity($subject, $id);
                return [PropertyState::with(
                    (string) $request->objectName(),
                    $id
                )];
            }
        }
        return $this->next->extract($request, $baseExtractor ?: $this);
    }
}
