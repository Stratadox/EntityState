<?php
declare(strict_types=1);

namespace Stratadox\EntityState;

/**
 * Changes.
 *
 * @author Stratadox
 */
final class Changes implements TellsWhatChanged
{
    private $added;
    private $altered;
    private $removed;

    private function __construct(
        ListsEntityStates $added,
        ListsEntityStates $altered,
        ListsEntityStates $removed
    ) {
        $this->added = $added;
        $this->altered = $altered;
        $this->removed = $removed;
    }

    /**
     * Produces a container of changed state.
     *
     * @param ListsEntityStates $added   The added entities.
     * @param ListsEntityStates $altered The altered entities.
     * @param ListsEntityStates $removed The removed entities.
     * @return TellsWhatChanged          The container with changes.
     */
    public static function wereMade(
        ListsEntityStates $added,
        ListsEntityStates $altered,
        ListsEntityStates $removed
    ): TellsWhatChanged {
        return new self($added, $altered, $removed);
    }

    /** @inheritdoc */
    public function added(): ListsEntityStates
    {
        return $this->added;
    }

    /** @inheritdoc */
    public function altered(): ListsEntityStates
    {
        return $this->altered;
    }

    /** @inheritdoc */
    public function removed(): ListsEntityStates
    {
        return $this->removed;
    }
}
