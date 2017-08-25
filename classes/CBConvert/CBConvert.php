<?php

final class CBConvert {

    /**
     * @param object $error
     *
     * @return string
     */
    static function javaScriptErrorToMessage($error) {
        $message = CBModel::value($error, 'message');

        if (empty($message)) {
            $message = '(no message)';
        }

        $basename = CBModel::value($error, 'sourceURL');

        if (empty($basename)) {
            $basename = '(no sourceURL)';
        } else {
            $basename = basename($basename);
        }

        $line = CBModel::value($error, 'line');

        if (empty($line)) {
            $line = '(no line)';
        }

        return "\"{$message}\" in {$basename} at line {$line}";

    }

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
     * Returns the name of the function where the error occurred.
     *
     * This function needs to be very stable because it will be called from
     * error handlers.
     *
     * @return string
     */
    static function throwableToFunction(/* Throwable: */ $throwable) {
        $trace = $throwable->getTrace();
        $entry = $trace[0];
        $class = empty($entry['class']) ? '' : $entry['class'];
        $type = empty($entry['type']) ? '' : $entry['type'];
        $function = empty($entry['function']) ? '' : $entry['function'];

        return "{$class}{$type}{$function}()\n";
    }

    /**
     * Creates a string with the thrown message and reasonably helpful
     * information for finding the source of the problem. This function should
     * be used for creating all "short" descriptions of an error.
     *
     * This function needs to be very stable because it will be called from
     * error handlers.
     *
     * @return string
     */
    static function throwableToMessage(/* Throwable */ $throwable) {
        $message = $throwable->getMessage();
        $basename = basename($throwable->getFile());
        $line = $throwable->getLine();

        return "\"{$message}\" in {$basename} line {$line}";
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

    /**
     * @param mixed $value
     *
     * @return string|null
     */
    static function valueToOptionalTrimmedString($value) {
        $string = trim($value);
        return ($string === '') ? null : $string;
    }
}
