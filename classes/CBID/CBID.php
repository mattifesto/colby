<?php

/**
 * This class contains functions for working with CBID values.
 *
 * This class as originally used only to push a currently relevant ID onto a
 * stack so that other functions that run can access the ID without having to be
 * passed it directly.
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



    /* -- functions -- -- -- -- -- */



    /**
     * @return CBID
     */
    static function generateRandomCBID(): string {
        $bytes = openssl_random_pseudo_bytes(20);
        return bin2hex($bytes);
    }



    /**
     * @return CBID|null
     */
    static function peek(): ?string {
        if (empty(CBID::$stack)) {
            return null;
        } else {
            return end(CBID::$stack);
        }
    }



    /**
     * @return CBID|null
     */
    static function pop(): ?string {
        return array_pop(
            CBID::$stack
        );
    }



    /**
     * @param CBID $CBID
     *
     * @return int
     *
     *      The new number of elements in the array.
     */
    static function push(string $CBID): int {
        return array_push(
            CBID::$stack,
            $CBID
        );
    }



    /*
     * CBID values are hexadecimal values that are 160-bits long (20 bytes, 40
     * hexadecimal characters). They are required to be lowercase so that they
     * can be compared for equality.
     */
    static function valueIsCBID($value): bool {
        if (is_string($value)) {
            return (bool)preg_match(
                '/^[a-f0-9]{40}$/',
                $value
            );
        } else {
            return false;
        }
    }
}
