<?php

final class CBHex160 {

    /**
     * Hex160 values are hexadecimal values that are 160-bits long (20 bytes,
     * 40 hexadecimal characters). They are required to be lowercase so that
     * they can be compared for equality.
     *
     * @return bool
     *      Returns true if the value is a hex160; otherwise false.
     */
    public static function is($value) {
        return preg_match('/[a-f0-9]{40}/', $value);
    }

    /**
     * @return {hex160}
     */
    public static function random() {
        $bytes = openssl_random_pseudo_bytes(20);
        return bin2hex($bytes);
    }

    /**
     * @param hex160 | [hex160]
     *      A single value or an array of 160-bit hexadecimal strings.
     *
     * @return string
     *      "UNHEX('<hex160>')" | "UNHEX('<hex160>'),UNHEX('<hex160>'),..."
     */
    public static function toSQL($values) {
        if (!is_array($values)) {
            $values = [$values];
        }

        $values = array_map(function($value) {
            if (!CBHex160::is($value)) {
                if (preg_match('/[a-fA-F0-9]{40}/', $value)) {
                    $message = "The value '{$value}' is not a hex160 value because it contains capital letters.";
                } else {
                    $message = "The value '{$value}' is not a 160-bit hexadecimal value.";
                }

                throw new RuntimeException($message);
            }
            $value = ColbyConvert::textToSQL($value);
            return "UNHEX('{$value}')";
        }, $values);

        return implode(',', $values);
    }
}
