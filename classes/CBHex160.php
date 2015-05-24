<?php

final class CBHex160 {

    /**
     * Hex160 values are hexadecimal values that are 160-bits long (20 bytes,
     * 40 hexadecimal characters). They are required to be lowercase so that
     * they can be compared for equality.
     *
     * @return boolean
     *  Returns true if the value is a hex160; otherwise false.
     */
    public static function is($value) {
        return preg_match('/[a-f0-9]{40}/', $value);
    }

    /**
     * @param {hex160} | [{hex160}] A single value or an array of 160-bit
     *                              hexadecimal strings
     * @return {string}
     */
    public static function toSQL($values) {
        if (!is_array($values)) {
            $values = [$values];
        }

        $values = array_map(function($value) {
            if (!CBHex160::is($value)) {
                throw new RuntimeException("The value '{$value}' is not hexadecimal.");
            }
            $value = ColbyConvert::textToSQL($value);
            return "UNHEX('{$value}')";
        }, $values);

        return implode(',', $values);
    }
}
