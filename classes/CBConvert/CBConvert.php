<?php

final class CBConvert {

    /**
     * Determines whether a string is a CSS background image. If so then it is
     * sanitized and returned; if not then null is returned.
     *
     * As this function gets more advanced it may return null more often as it
     * learns to recognize strings that aren't valid background images.
     *
     * @return string|null
     */
    public static function stringToCSSBackgroundImage($string) {
        return CBConvert::stringToCSSValue($string);
    }

    /**
     * Determines whether a string is a CSS color. If it is then it is sanitized
     * and returned; if not then null is returned.
     *
     * As this function gets more advanced it may return null more often as it
     * learns to recognize strings that aren't valid colors.
     *
     * @return string|null
     */
    public static function stringToCSSColor($string) {
        return CBConvert::stringToCSSValue($string);
    }

    /**
     * Sanitizes a string to be used as a CSS value. If the string is not a CSS
     * value null is returned.
     *
     * @return string|null
     */
    public static function stringToCSSValue($string) {
        $value = str_replace([';', '"', "'"], '', trim($string));
        return empty($value) ? null : $value;
    }

    /**
     * @param mixed $value
     *
     * @return string|null
     */
    public static function valueToOptionalTrimmedString($value) {
        $string = trim($value);
        return ($string === '') ? null : $string;
    }

    /**
     * @param string $value
     * @param int $length
     * @param string $append
     *
     * @return string
     *      This function always returns a trimmed string containing no line
     *      breaks or tabs.
     */
    static function truncate($value, $length = 40, $append = '…') {
        $value = trim($value);
        $value = preg_replace('/[ \n\r\t]+/', ' ', $value);

        if (strlen($value) > $length) {
            $value = wordwrap($value, $length);
            $value = explode("\n", $value, 2);
            $value = $value[0] . $append;
        }

        return $value;
    }
}
