<?php

final class CBHex160 {

    /**
     * Hex160 values are hexadecimal values that are 160-bits long (20 bytes,
     * 40 hexadecimal characters). They are required to be lowercase so that
     * they can be compared for equality.
     *
     * @param mixed $value
     *
     * @return bool
     *      Returns true if the value is a hex160; otherwise false.
     */
    static function is($value) {
        if (is_string($value)) {
            return (bool)preg_match('/[a-f0-9]{40}/', $value);
        } else {
            return false;
        }
    }

    /**
     * @return hex160
     */
    static function random() {
        $bytes = openssl_random_pseudo_bytes(20);
        return bin2hex($bytes);
    }

    /**
     * @param hex160 | [hex160]
     *
     *      A single value or an array of 160-bit hexadecimal strings.
     *
     * @return string
     *
     *      "UNHEX('<hex160>')" | "UNHEX('<hex160>'),UNHEX('<hex160>'),..."
     */
    static function toSQL($values) {
        if (!is_array($values)) {
            $values = [$values];
        }

        $values = array_map(function($value) {
            if (!CBHex160::is($value)) {
                if (is_string($value) && preg_match('/[a-fA-F0-9]{40}/', $value)) {
                    $message = "The value '{$value}' is not a hex160 value because it contains capital letters.";
                } else {
                    $valueAsJSON = json_encode($value);
                    $message = "The value '{$valueAsJSON}' is not a 160-bit hexadecimal value.";
                }

                throw new RuntimeException($message);
            }

            $value = ColbyConvert::textToSQL($value);

            return "UNHEX('{$value}')";
        }, $values);

        return implode(',', $values);
    }
}
