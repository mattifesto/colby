<?php

final class CBConvert {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs(
    ): array {
        return [
            Colby::flexpath(
                __CLASS__,
                'v675.35.js',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */



    /**
     * @return [[string]]
     */
    static function
    CBHTMLOutput_JavaScriptVariables(
    ): array {
        return [
            [
                'CBConvert_stringToStubReplacements',
                CBConvert::stringToStubReplacements(),
            ]
        ];
    }
    /* CBHTMLOutput_JavaScriptVariables() */



    /* -- functions -- -- -- -- -- */



    /**
     * @param mixed cents
     *
     *      This parameter must convert to an integer using
     *      CBConvert.valueAsInt().
     *
     * @return string
     *
     *      150         => "1.50"
     *      "5"         => "0.05"
     *      75          => "0.75"
     *      "  3500  "  => "35.00"
     *      " -3500  "  => "-35.00"
     */
    static function centsToDollars($cents): string {
        $isNegative = false;
        $centsAsInt = CBConvert::valueAsInt($cents);

        if ($centsAsInt === null) {
            throw new InvalidArgumentException(
                'The $cents parameter is not a valid integer.'
            );
        }

        if ($centsAsInt < 0) {
            $isNegative = true;
            $centsAsInt = abs($centsAsInt);
        }

        /**
         * Convert to a string.
         */

        $centsAsString = CBConvert::valueToString($centsAsInt);

        /**
         * Pad with zeros until the string is at least 3 digits long.
         */

        while (strlen($centsAsString) < 3) {
            $centsAsString = "0" . $centsAsString;
        }

        return (
            ($isNegative ? '-' : '') .
            substr_replace($centsAsString, '.', -2, 0)
        );
    }
    /* centsToDollars() */



    /**
     * @param string $domain
     *
     *      Colby recommends that local network web servers use domains that
     *      have an element that starts with "ld", "lt", or "lp". This function
     *      will determine whether the $domain argument is one of those domains,
     *      and if it is, it will return the web server's domain.
     *
     * @return string|null
     *
     *      Examples:
     *
     *      mysite.ld4.example.com will return ld4.example.com
     *
     *      mysite.com will return null
     */
    static function
    domainToLocalServerDomain(
        string $domain
    ): ?string {
        $domainParts = explode(
            '.',
            $domain
        );

        $domainPartsCount = count(
            $domainParts
        );

        if (
            $domainPartsCount < 4
        ) {
            return null;
        }

        $serverPart = $domainParts[
            1
        ];

        if (
            preg_match('/^l[dtp][0-9]+$/', $serverPart)
        ) {
            $localServerDomainParts = $domainParts;

            array_shift(
                $localServerDomainParts
            );

            $localServerDomain = implode(
                '.',
                $localServerDomainParts
            );

            return $localServerDomain;
        }

        return null;
    }
    /* domainToLocalServerDomain() */



    /**
     * @param string $domain
     *
     * @return string
     *
     *      foo.bar.com -> com_bar_foo
     */
    static function
    domainToReverseDomain(
        string $domain
    ): string {
        $parts = explode(
            '.',
            $domain
        );

        $parts = array_reverse(
            $parts
        );

        return implode(
            '_',
            $parts
        );
    }
    /* domainToReverseDomain() */



    /**
     * @BUG 2019_01_31
     *
     *      This function incorrectly uses the CBModel class. The CBModel class
     *      actually has a dependency on this class. This function should
     *      probably be moved to another class, something like CBJavaScript.
     *
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
    /* javaScriptErrorToMessage() */



    /**
     * This function will throw an exception if the JSON is not valid.
     *
     * @param string $JSON
     *
     * @return mixed
     */
    static function JSONToValue(string $JSON) {
        $value = json_decode($JSON);
        $error = json_last_error();

        if ($error === JSON_ERROR_NONE) {
            return $value;
        } else {
            throw new Exception(
                "The JSON error {$error} was produced when trying to parse " .
                "the value: {$JSON}"
            );
        }
    }
    /* JSONToValue() */



    /**
     * This function can be used to convert local absolue URLs to root relative
     * URLs in cases where local absolute URLs were created with the wrong
     * scheme and cause security warnings in browsers.
     *
     * @param string $URL
     *
     * @return string
     *
     *      If the $URL parameter represents a local absolute URL, a root
     *      relative version of that URL will be returned. Otherwise, the
     *      original URL will be returned.
     */
    static function localAbsoluteURLToRootRelativeURL(string $URL): string {
        $domain = $_SERVER['SERVER_NAME'];

        return preg_replace(
            "/^https?:\/\/{$domain}\//",
            "/",
            $URL
        );
    }
    /* localAbsoluteURLToRootRelativeURL() */



    /**
     * This function removes extra whitespace from what should be a single line
     * of text. It is used to be able to write long lines of text across
     * multiple lines of code so that the code is easy to read, but the line can
     * still have the correct white space.
     *
     * @param string $value
     *
     * @return string
     */
    static function stringToCleanLine(string $value): string {
        return trim(
            preg_replace('/\s+/', ' ', $value)
        );
    }
    /* stringToCleanLine() */



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
     * @see documentation
     *
     * @param mixed $string
     *
     * @return string
     */
    static function
    stringToStub(
        $originalString
    ): string {
        $stub = strtolower(
            $originalString
        );

        $stubReplacements = CBConvert::stringToStubReplacements();

        foreach ($stubReplacements as $stubReplacement) {
            $stub = preg_replace(
                "/{$stubReplacement->pattern}/",
                $stubReplacement->replacement,
                $stub
            );
        }

        return $stub;
    }
    /* stringToStub() */



    /**
     * @return [object]
     */
    private static function
    stringToStubReplacements(
    ): array {
        return [
            (object)[
                'pattern' => '[\s\-_]+',
                'replacement' => '-',
            ],
            (object)[
                'pattern' => '[^a-zA-Z0-9\-]',
                'replacement' => '',
            ],
            (object)[
                'pattern' => '^-+',
                'replacement' => '',
            ],
            (object)[
                'pattern' => '-+$',
                'replacement' => '',
            ],
            (object)[
                'pattern' => '--+',
                'replacement' => '-',
            ],
        ];
    }
    /* stringToStubReplacements() */



    /**
     * @see documentation
     *
     * @param string $string
     *
     * @return string
     */
    static function stringToURI(
        $string
    ): string {
        $stubs = preg_split(
            '/\//',
            $string,
            /* limit: */ -1,
            PREG_SPLIT_NO_EMPTY
        );

        $stubs = array_map(
            'CBConvert::stringToStub',
            $stubs
        );

        $stubs = array_filter(
            $stubs,
            function ($value) {
                return $value !== '';
            }
        );

        return implode(
            '/',
            $stubs
        );
    }
    /* stringToURI() */



    /**
     * Returns the name of the function where the error occurred.
     *
     * This function needs to be very stable because it will be called from
     * error handlers.
     *
     * @param Throwable $throwable
     *
     * @return string
     */
    static function
    throwableToFunction(
        /* Throwable */ $throwable
    ) /* : string */ {
        $trace = $throwable->getTrace();
        $entry = $trace[0];

        $class = (
            empty($entry['class']) ?
            '' :
            $entry['class']
        );

        $type = (
            empty($entry['type']) ?
            '' :
            $entry['type']
        );

        $function = (
            empty($entry['function']) ?
            '' :
            $entry['function']
        );

        return "{$class}{$type}{$function}()\n";
    }
    /* throwableToFunction() */



    /**
     * @deprecated use CBException::throwableToOneLineErrorReport()
     */
    static function throwableToMessage(Throwable $throwable) {
        return CBException::throwableToOneLineErrorReport(
            $throwable
        );
    }



    /**
     * @NOTE 2021_02_25
     *
     *      This function is the main function to call to convert a single error
     *      into a plain text stack trace for that error only. If you want to
     *      iterate through previous errors you will either do so manually or
     *      call CBErrorHandler::throwableToPlainTextIteratedStackTrace().
     *
     *      This function should probably be moved to CBErrorHandler. Functions
     *      dealing with errors should not be in code that's used frequently.
     *
     * @param Throwable $throwable
     *
     * @return string
     */
    static function
    throwableToStackTrace(
        Throwable $throwable
    ) {
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
        } catch (Throwable $innerThrowable) {
            return 'INNER ERROR: "' .
                $innerThrowable->getMessage() .
                '" during ' .
                __METHOD__ .
                "()\n\n" .
                $innerThrowable->getTraceAsString();
        }
    }
    /* throwableToStackTrace() */



    /**
     * @param array $trace
     *
     * @return string
     */
    static function traceToString(array $trace) {
        foreach ($trace as $item) {
            $file =
            empty($item['file']) ?
            '(unspecified)' :
            $item['file'];

            $basename = basename($file);

            $function =
            empty($item['function']) ?
            '(unspecified)' :
            $item['function'];

            $line =
            empty($item['line']) ?
            '(unspecified)' :
            $item['line'];

            $class = '';
            $type = '';

            if (mb_substr($function, 0, 1) !== '{') {
                $class = empty($item['class']) ? '' : $item['class'];
                $type = empty($item['type']) ? '' : $item['type'];
                $function = "{$function}()";
            }

            $lines[] = (
                "{$class}{$type}{$function}\n" .
                "was called from\n" .
                "{$basename}\n" .
                "on line {$line}"
            );
        }

        return implode(
            "\n\n",
            $lines
        );
    }
    /* traceToString() */



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
    /* truncate() */



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
     * @return string|null
     */
    static function valueAsEmail(
        /* mixed */ $value
    ): ?string {
        $emailAddress = trim(
            CBConvert::valueToString(
                $value
            )
        );

        if (
            preg_match(
                '/^\S+@\S+\.\S+$/',
                $emailAddress
            )
        ) {
            return $emailAddress;
        } else {
            return null;
        }
    }
    /* valueAsEmail() */



    /**
     * @deprecated use CBID::valueIsCBID()
     */
    static function valueAsID($value): ?string {
        if (CBID::valueIsCBID($value)) {
            return $value;
        } else {
            return null;
        }
    }



    /**
     * @deprecated use CBID::valueIsCBID()
     */
    static function valueAsHex160($value): ?string {
        return CBConvert::valueAsID($value);
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
     * @return ?int
     */
    static function valueAsInt($value): ?int {
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
     * @param [string] $classNames
     *
     *      If this parameter is empty all models will be returned. If not, only
     *      models with a class name matching one of the class names in the
     *      array will be returned.
     *
     *      @TODO 2019_01_31
     *
     *          This parameter should be deprecated. This is not the place to
     *          compare class names. If it isn't deprecated, replace this note
     *          with explicit documentation on why this is the place for it and
     *          adjust the JavaScript implementation, which does not support the
     *          parameter currently.
     *
     * @return object|null
     *
     *      If $value is an object that has a non-empty string type "className"
     *      property value then $value will be returned; otherwise null.
     *
     *      @NOTE 2019_01_31
     *
     *          This function does not perform any sort of "className" property
     *          value validation because it would be time consuming and
     *          difficult, if not impossible, since it would have to work for
     *          any programming language.
     *
     *          It's more important for this function to quickly determine
     *          whether the $value parameter is attempting to be a model than
     *          whether it might succeed at being a model.
     */
    static function valueAsModel($value, array $classNames = []): ?stdClass {
        if (CBConvert::valueAsObject($value) === null) {
            return null;
        }

        if (empty($value->className) || !is_string($value->className)) {
            return null;
        }

        if (empty($classNames) || in_array($value->className, $classNames)) {
            return $value;
        } else {
            return null;
        }
    }



    /**
     * @deprecated use CBConvert::valueAsName()
     *
     * @param mixed $value
     *
     * @return ?string
     *
     *      If the string value of the $value parameter is a valid moniker, the
     *      moniker will be returned; otherwise null.
     */
    static function valueAsMoniker($value): ?string {
        $stringValue = trim(CBConvert::valueToString($value));

        if (preg_match('/^[a-z0-9_]+$/', $stringValue)) {
            return $stringValue;
        } else {
            return null;
        }
    }



    /**
     * This function converts a name potentially surrounded by whitespace into
     * a valid name or null.
     *
     * When should this function be used?
     *
     *      This function may be used when building specs into models when extra
     *      whitespace around the spec value for the name is acceptable. Then
     *      valueAsString() can be used to get the value from the model.
     *
     *      This function may be used when converting user input to a valid
     *      name.
     *
     * @param mixed $value
     *
     * @return string|null
     *
     *      If the parameter contains a valid name, the name is returned;
     *      otherwise null is returned.
     */
    static function valueAsName(
        $value
    ): ?string {
        $potentialName = trim(
            CBConvert::valueToString($value)
        );

        if (CBConvert::valueIsName($potentialName)) {
            return $potentialName;
        } else {
            return null;
        }
    }
    /* valueAsName() */



    /**
     * This function converts a string containing comma and/or white space
     * separated names into an array of valid names.
     *
     * @param mixed $value
     *
     * @return [string]|null
     *
     *      If the $value paramater contains only whitespace or comma separated
     *      whitespace an empty array is returned.
     *
     *      If the $value parameter contains a valid set of names, an array of
     *      names is returned.
     *
     *      If the $value parameter contains any characters not allowed in a
     *      name, null will be returned.
     */
    static function valueAsNames(
        $value
    ): ?array {
        $stringValue = CBConvert::valueToString($value);

        $potentialNames = preg_split(
            '/[\s,]+/',
            $stringValue,
            null,
            PREG_SPLIT_NO_EMPTY
        );


        if (!is_array($potentialNames)) {
            return null;
        }

        foreach ($potentialNames as $potentialName) {
            if (!CBConvert::valueIsName($potentialName)) {
                return null;
            }
        }

        return $potentialNames;
    }
    /* valueAsNames() */



    /**
     * Determines whether the value parameter can reasonably be interpreted to
     * be a number. It always returns a float so that it can closely match the
     * JavaScript version of the function.
     *
     * If $value is a float, it will be returned.
     *
     * If $value is a integer, the float version of that integer will be
     * returned.
     *
     * If the value is a string and its trimmed value is a series of digits with
     * an optional single decimal point, it will be converted to a float and
     * returned.
     *
     * This function differs from a cast in that boolean and other types will
     * not ever be considered numbers.
     *
     * @NOTE
     *
     *      This function does not currently convert strings in exponent
     *      notation such as "9.223372036854776e+18".
     *
     *      This function has not been tested with strings containing huge
     *      numbers.
     *
     * @param mixed $value
     *
     * @return ?float
     */
    static function valueAsNumber($value): ?float {
        if (is_float($value)) {
            if (is_nan($value) || is_infinite($value)) {
                return null;
            }

            return $value;
        }

        if (is_int($value)) {
            return floatval($value);
        }

        if (is_string($value)) {

            /**
             * Start by verifing the presence of at least a single number
             * character in the string because the next regular expression does
             * not require a number character to be present on either side of
             * the decimal point.
             *
             * The combination of these two regular expressions guarantees that
             * there is at least one number character either before or after the
             * decimal point.
             */
            if (preg_match('/[0-9]/', $value)) {
                $value = trim($value);

                if (preg_match('/^-?[0-9]*\.?[0-9]*$/', $value)) {
                    return floatval($value);
                }
            }
        }

        return null;
    }
    /* valueAsNumber() */



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
     * A "name" is a simple string that can be used as an identifier in many
     * situations. As Colby matured, the importance and nature of the "name"
     * concept has grown.
     *
     * Names can contain the following characters:
     *
     *      a-z
     *      A-Z
     *      0-9
     *      _ (underscore)
     *
     *      Why not hyphens?
     *
     *          Technically hyphens are not word characters. This means that
     *          double clicking on a word with a hyphen will not select the
     *          entire word. Since names are intended to provide positive
     *          benefits to speed development, hyphens are not allowed.
     *
     * Uses for names
     *
     *      Monikers
     *
     *          A moniker is a readable ID for a model that's usually provided
     *          in a CSV file that will be used to import models.
     *
     *      CSS class names
     *
     *          Restricting your CSS class names to the characters allowed in
     *          names will help speed your development process.
     *
     * @param mixed $value
     *
     * @return bool
     */
    static function valueIsName($value): bool {
        if (!is_string($value)) {
            return false;
        }

        return (bool)preg_match(
            '/^[a-zA-Z0-9_]+$/',
            $value,
        );
    }



    /**
     * @param mixed $value
     *
     * @return [mixed]
     *
     *      If the $value parameter is an array it is returned; otherwise an
     *      empty array is returned.
     */
    static function valueToArray($value): array {
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
    static function valueToArrayOfObjects($value): array {
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
     * This function exists to simplify boolean conversions, especially with
     * regard to JSON object property values and strings typed into spreadsheet
     * cells or text fields.
     *
     *      False:
     *
     *          false
     *          null
     *          undefined (JavaScript)
     *          0 or 0.0
     *          trimmed string is "0"
     *          trimmed string is ""
     *
     *      True:
     *
     *          everything else
     *
     * @param mixed $value
     *
     * @return bool
     */
    static function valueToBool($value): bool {
        if (is_string($value)) {
            $value = trim($value);

            if (
                $value === '' ||
                $value === '0'
            ) {
                return false;
            } else {
                return true;
            }
        }

        if (
            $value === false ||
            $value === 0 ||
            $value === 0.0 ||
            $value === null
        ) {
            return false;
        } else {
            return true;
        }
    }
    /* valueToBool() */



    /**
     * @NOTE 2018_12_06
     *
     *      A $value of an empty string or a non-string that converts to
     *      an empty string will return an array with one empty string item.
     *      This function disagrees with the str_getcsv() behavior of returning
     *      an array with one null item in that case, especially since
     *      str_getcsv() will return an array of two empty strings for the value
     *      ",".
     *
     *      An investigation of this issue did not produce an absolutely
     *      definitive answer as to the correct result for an empty CSV string.
     *      No trustworthy sources address the scenario of an empty CSV string.
     *      However, in addition, no trustworthy sources acknowledge the idea
     *      that a one of the values in a CSV string could represent null.
     *
     *      This function asserts that a CSV string is a series of one or more
     *      string values separated by commas. By passing a string to this
     *      function you are asserting that the string is a CSV string. An empty
     *      CSV string has no commas, and therefore has only one value, and that
     *      value is an empty string.
     *
     *      Potential callers that consider an empty string to represent no
     *      values may detect an empty string and choose not to call this
     *      function. Other callers may choose to trim and ignore empty string
     *      values returned by this function.
     *
     *      Reference: RFC 4180
     *
     *          https://www.ietf.org/rfc/rfc4180.txt
     *
     * @param mixed $value
     *
     *      The $value parameter is converted to a string before being parsed
     *      for comma separated values.
     *
     * @return [string]
     */
    static function valueToCommaSeparatedValues($value): array {
        $csv = CBConvert::valueToString($value);

        if ($csv === '') {
            return [''];
        }

        return str_getcsv($csv);
    }



    /**
     * @deprecated use CBConvert::valueAsNames()
     *
     *      This function has an odd implementation in that it will convert the
     *      word naïve into ["na", "ve"]. The concepts of "name" and "moniker"
     *      have been more completely analyzed resulting in the valueAsName()
     *      and valueAsNames() functions on this class.``
     *
     * Split a value into an array of names.
     *
     * In the past the concept of "name" has been specialized to mean CSS names
     * or PHP class names. This function simplifies a name to be a string of
     * [A-Za-z0-9_] characters. Trying to get more specific than this is a task
     * that results in wasted time for a yet another imperfect solution.
     *
     * @param mixed $value
     *
     * @return [string]
     */
    static function valueToNames($value): array {
        $value = CBConvert::valueToString($value);
        $names = preg_split(
            '/[^A-Za-z0-9_]+/',
            $value,
            null,
            PREG_SPLIT_NO_EMPTY
        );

        if ($names === false) {
            return [];
        }

        return $names;
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
     * NOTE 2018.01.06
     *
     *      This is an odd function. Callers should be investigated and a more
     *      sensible and straightforward solution should be found.
     *
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
        return json_encode(
            $value,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        );
    }



    /**
     * This function exists because PHP will complain at various levels when
     * attempting to convert some values, such as objects and arrays, to
     * strings.
     *
     * It's reasonable for PHP to complain, but when retreiving values from
     * models we don't care about such issues. And the "ToString" name means if
     * the value can't be reasonably converted to a string, return an empty
     * string.
     *
     * @param mixed $value
     *
     * @return string
     */
    static function valueToString($value): string {
        try {
            return (string)$value;
        } catch (Throwable $throwable) {
            return '';
        }
    }

}
