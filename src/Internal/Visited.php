<?php
declare(strict_types=1);

namespace Stratadox\EntityState\Internal;

use function array_merge as add;
use function array_search as search;
use function get_class as classOf;
use function gettype as typeOf;
use function in_array as has;
use function is_object as isObject;

/**
 * Visited list.
 *
 * @internal
 * @author Stratadox
 */
final class Visited
{
    private $visited;
    private $paths;

    private function __construct(array $visited, array $paths)
    {
        $this->visited = $visited;
        $this->paths = $paths;
    }

    /**
     * Produces an empty visited list.
     *
     * @return Visited The empty visited list.
     */
    public static function noneYet(): Visited
    {
        return new Visited([], []);
    }

    /**
     * Checks whether the value is already on the list.
     *
     * @param mixed $value The value to check for.
     * @return bool        Whether the value is on the list.
     */
    public function already($value): bool
    {
        return has($value, $this->visited, true);
    }

    /**
     * Retrieves the name associated with the visited value.
     *
     * @param mixed $value The value to find the property name for.
     * @return string      The name associated with the value.
     */
    public function name($value): string
    {
        return $this->paths[search($value, $this->visited)];
    }

    /**
     * Adds the value to the list of visited values.
     *
     * Also keeps a note on where to find it.
     *
     * @param mixed $value The value to mark as visited.
     * @param Name  $name  The name of the visited property.
     * @return Visited     The updated visited list.
     */
    public function add($value, Name $name): Visited
    {
        return new Visited(
            add($this->visited, [$value]),
            add($this->paths, [sprintf(
                '%s:%s',
                isObject($value) ? classOf($value) : typeOf($value),
               $name
           )])
        );
    }
}
