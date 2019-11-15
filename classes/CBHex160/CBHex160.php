<?php

/**
 * @deprecated 2019_11_15
 *
 *      This class has been replaced by the CBID class.
 */
final class CBHex160 {

    /* -- functions -- -- -- -- -- */



    /**
     * @deprecated use CBID::valueIsCBID()
     */
    static function is($value): bool {
        return CBID::valueIsCBID($value);
    }



    /**
     * @deprecated use CBID::generateRandomCBID()
     */
    static function random(): string {
        return CBID::generateRandomCBID();
    }



    /**
     * @param CBID|[CBID]
     *
     * @return string
     *
     *      "UNHEX('<CBID>')"
     *
     *      "UNHEX('<CBID>'),UNHEX('<CBID>'),..."
     */
    static function toSQL($values) {
        if (!is_array($values)) {
            $values = [$values];
        }

        $values = array_map(
            function($value) {
                if (!CBHex160::is($value)) {
                    $valueAsJSON = json_encode($value);

                    if (
                        is_string($value) &&
                        preg_match('/^[a-fA-F0-9]{40}$/', $value)
                    ) {
                        $message = (
                            "The value {$valueAsJSON} is not a hex160 value " .
                            "because it contains capital letters."
                        );
                    } else {
                        $message = (
                            "The value {$valueAsJSON} is not a 160-bit " .
                            "hexadecimal value."
                        );
                    }

                    throw new RuntimeException($message);
                }

                $value = CBDB::escapeString($value);

                return "UNHEX('{$value}')";
            },
            $values
        );

        return implode(',', $values);
    }
    /* toSQL() */

}
