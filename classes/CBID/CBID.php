<?php

/**
 * This class is used to push a currently relevant ID onto a stack so that other
 * functions that run can access the ID without having to be passed it directly.
 *
 * Examples:
 *
 *      CBTask2 will push the ID associated with the currently running task onto
 *      the stack while a task is running.
 *
 *      CBLog::log() uses the ID on the top of the stack as the ID for a log
 *      entry if one isn't specified explicitly.
 */
final class CBID {

    private static $stack = [];

    /**
     * @return ?hex160
     */
    static function peek(): ?string {
        if (empty(CBID::$stack)) {
            return null;
        } else {
            return end(CBID::$stack);
        }
    }

    /**
     * @return ?hex160
     */
    static function pop(): ?string {
        return array_pop(CBID::$stack);
    }

    /**
     * @param hex160 $ID
     *
     * @return int
     *
     *      The new number of elements in the array.
     */
    static function push(string $ID): int {
        return array_push(CBID::$stack, $ID);
    }
}
