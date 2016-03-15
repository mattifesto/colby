<?php

final class CBConvert {

    /**
     * This function determines whether a string is a CSS color. If it is a
     * color then it is sanitized and returned; if not then null is returned.
     *
     * As this function gets more advanced it may return null more often as it
     * learns to recognize strings that aren't valid colors.
     *
     * @return string|null
     */
    public static function stringToCSSColor($string) {
        $color = str_replace(';', '', trim($string));
        return empty($color) ? null : $color;
    }
}
