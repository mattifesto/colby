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
    static function stringToCSSBackgroundImage($string) {
        return CBConvert::stringToCSSValue($string);
    }

    /**
     * Converts an string of space and comma delimited CSS class names into an
     * array of CSS class names.
     *
     * @param mixed $string
     *
     *      Example: "right bold, red h&ck"
     *
     * @return [string]
     *
     *      Example: ["right", "bold", "red", "h&amp;ck"]
     *
     *      The returned class names will be escaped for HTML. Valid CSS class
     *      names shouldn't need to be escaped for HTML, but rather than try to
     *      validate the class names this function just guaratees they are safe
     *      for use in HTML.
     */
    static function stringToCSSClassNames($string): array {
        $string = cbhtml(trim($string));
        $classNames = preg_split('/[\s,]+/', $string, null, PREG_SPLIT_NO_EMPTY);

        if ($classNames === false) {
            throw new RuntimeException("preg_split() returned false");
        }

        return $classNames;
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
    static function stringToCSSColor($string) {
        return CBConvert::stringToCSSValue($string);
    }

    /**
     * Sanitizes a string to be used as a CSS value. If the string is not a CSS
     * value null is returned.
     *
     * @return string|null
     */
    static function stringToCSSValue($string) {
        $value = str_replace([';', '"', "'"], '', trim($string));
        return empty($value) ? null : $value;
    }

    /**
     * @NOTE needs testing
     *
     * This function has been analyzed to do exactly what it needs to do. It
     * focuses on the three most common line delimiters: "\r\n", "\r", and "\n".
     * Any and all of these will be treated as a line ending. "\r\n" will be
     * treated as a single line ending, but "\n\r" will be two. "\r\n\n" will
     * also be two line endings and so will "\r\n\r\n" and "\r\n\r".
     *
     * This function has the additional benefit of guaranteeing that no lone
     * line ending characters will remain in any of the strings in the returned
     * array.
     *
     * This function is meant to commonly used and be considered very safe
     * because it handles line splitting perfectly in all situations except for
     * those where custom line ending rules are in effect.
     *
     * The "\R" regular expression escape sequence appears to represent the
     * same thing as the regular expression used in this function, according
     * to tests. However, since this escape sequence is undocumented, it is not
     * used in this function.
     *
     * @param string $value
     *
     * @return [string]
     */
    static function stringToLines($value) {
        return preg_split('/(\r\n|\r|\n)/', $value);
    }

    /**
     * Converts plain text to a simple "stub".
     *
     * A stub is a string that contains only the characters [0-9a-z-]. It
     * replaces spaces with a hyphens and trims the string.
     *
     * Useful for:
     *
     *  - URL stubs
     *  - HTML classes and IDs
     *
     * History: This function used to use iconv() to convert international
     * characters to their ASCII base characters, but this is unreliable on
     * different hosts. Future options include maintaining a list of character
     * replacements which is a method used by many systems.
     *
     * current algorithm:
     *
     * 1. trim the input string, which also converts the input to a string if
     *    it isn't already one
     *
     * 2. replace sequences of white space and hyphens with one hyphen
     *
     * 3. remove all characters except: a-z, A-Z, 0-9, and hyphen
     *
     * 4. remove leading hyphens
     *
     * 5. remove trailing hyphens
     *
     * 6. replace two or more adjacent hyphens with one hypen
     *    step 3 can result in characters being removed which causes this
     *
     * 7. make all characters lowercase
     *
     * common example: 'Piñata Örtega' --> 'piata-rtega'
     *
     * @param mixed $string
     *
     * @return string
     */
    static function stringToStub($string) {
        $stub = trim($string);

        $patterns =     array('/[\s-]+/', '/[^a-zA-Z0-9-]/', '/^-+/', '/-+$/', '/--+/');
        $replacements = array('-'       , ''               , ''     , ''     , '-'    );

        $stub = preg_replace($patterns, $replacements, $stub);

        return strtolower($stub);
    }

    /**
     * This function is very similar to CBConvert::stringToStub() but it handles
     * forward slashes
     *
     * " hey  //  you read  / this post /  " --> "hey/you-read/this-post"
     *
     * TODO: Reconcile with Colby.textToURI()
     *
     * @param string $string
     *
     * @return string
     */
    static function stringToURI($string) {
        $stubs = preg_split('/\//', $string, /* limit: */ -1, PREG_SPLIT_NO_EMPTY);
        $stubs = array_map('CBConvert::stringToStub', $stubs);
        $stubs = array_filter($stubs, function ($value) { return $value !== ''; });

        return implode('/', $stubs);
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
     * The caller should prepend this message with "Error", "INNER ERROR", or
     * "SECOND INNER ERROR" as appropriate for the situation.
     *
     * @return string
     */
    static function throwableToMessage(Throwable $throwable) {
        $message = $throwable->getMessage();
        $basename = basename($throwable->getFile());
        $line = $throwable->getLine();

        return "\"{$message}\" in {$basename} line {$line}";
    }

    /**
     * @param Throwable $throwable
     *
     * @return string
     */
    static function throwableToStackTrace(Throwable $throwable) {
        try {
            $lines = [];
            $trace = $throwable->getTrace();

            $class = get_class($throwable);
            $message = $throwable->getMessage();
            $basename = basename($throwable->getFile());
            $line = $throwable->getLine();

            $firstLine = "{$class}\n\"{$message}\"\n{$basename}\nline {$line}";
            $traceAsString = CBConvert::traceToString($trace);

            return "{$firstLine}\n\n{$traceAsString}";
        } catch (Throwable $throwable) {
            return 'INNER EXCEPTION "' .
                $throwable->getMessage() .
                '" during ' .
                __METHOD__ .
                "()\n\n" .
                $throwable->getTraceAsString();
        }
    }

    /**
     * @param array $trace
     *
     * @return string
     */
    static function traceToString(array $trace) {
        foreach ($trace as $item) {
            $file = empty($item['file']) ? '(unspecified)' : $item['file'];
            $basename = basename($file);
            $function = empty($item['function']) ? '(unspecified)' : $item['function'];
            $line = empty($item['line']) ? '(unspecified)' : $item['line'];
            $class = '';
            $type = '';

            if (mb_substr($function, 0, 1) !== '{') {
                $class = empty($item['class']) ? '' : $item['class'];
                $type = empty($item['type']) ? '' : $item['type'];
                $function = "{$function}()";
            }

            $lines[] = "{$class}{$type}{$function}\nwas called from\n{$basename}\non line {$line}";
        }

        return implode("\n\n", $lines);
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
     * @NOTE
     *
     *      The "valueAs" and "valueTo" functions are used as parameter
     *      validation and sometimes conversion functions.
     *
     *      A "valueAs" function checks to see if the parameter is the requested
     *      type or can be reasonably interpreted to be the requested type. If
     *      so, the function returns the approprate value of the requested type.
     *      If the parameter can not reasonably be interpreted to be the
     *      requested type then null is returned.
     *
     *      A "valueTo" function does basically the same thing as a "valueAs"
     *      function, but if the value can't reasonably be interpreted to be a
     *      value of the requested type, it returns an empty value of the
     *      requested type.
     *
     *      Objects and arrays are the most common types of "valueTo" functions.
     *      A function named "valueToInt" would be odd because its difficult to
     *      determine the value of an "empty" integer that would be useful to
     *      all callers.
     */


    /**
     * @param mixed $value
     *
     * @return [mixed]
     *
     *      If the $value parameter is an array it is returned; otherwise an
     *      empty array is returned.
     */
    static function valueAsArray($value): array {
        if (is_array($value)) {
            return $value;
        }

        return [];
    }

    /**
     * @param mixed $value
     *
     * @return [object]
     *
     *      If the $value parameter is an array of objects it is returned;
     *      otherwise and empty array is returned.
     */
    static function valueAsArrayOfObjects($value): array {
        if (is_array($value)) {
            foreach ($value as $item) {
                if (!is_object($item)) {
                    return [];
                }
            }

            return $value;
        }

        return [];
    }

    /**
     * @param mixed $value
     *
     * @return hex160|null
     */
    static function valueAsHex160($value) {
        if (CBHex160::is($value)) {
            return $value;
        } else {
            return null;
        }
    }

    /**
     * This function returns an integer if the $value parameter can reasonably
     * be interpreted to represent an integer. This is different than a cast to
     * int or the intval() function because it will not truncate floating point
     * values.
     *
     * The function name uses "as" because we are returning the same value, but
     * as an integer instead of the original value type.
     *
     * @param mixed $value
     *
     *      5       => 5
     *      5.0     => 5
     *      5.1     => null
     *      "5"     => 5
     *      " 5 "   => 5
     *      "5.0"   => 5
     *      "5.1"   => null
     *      "five"  => null
     *
     * @return int|null
     */
    static function valueAsInt($value) {
        if (is_string($value)) {
            $value = trim($value);
        }

        if (is_numeric($value)) {
            $intValue = intval($value);

            if ($intValue == $value) {
                return $intValue;
            }
        }

        return null;
    }

    /**
     * @param mixed $value
     *
     * @return object|null
     *
     *      If $value is an object, it will be returned; otherwise null will be
     *      returned.
     */
    static function valueAsObject($value): ?stdClass {
        if (is_object($value)) {
            return $value;
        } else {
            return null;
        }
    }

    /**
     * @param mixed $value
     *
     * @return object
     *
     *      If $value is an object, it will be returned; otherwise an empty
     *      object will be returned.
     */
    static function valueToObject($value): stdClass {
        if (is_object($value)) {
            return $value;
        } else {
            return (object)[];
        }
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

    /**
     * @param mixed $value
     *
     * @return string
     */
    static function valueToPrettyJSON($value): string {
        return json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
