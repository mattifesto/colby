<?php

final class
ColbyConvert
{
    /* -- functions -- */



    /**
     * Sanitizes a cents string into a cents int. If the string is not a valid
     * cents string, null is returned.
     *
     * @param string $centsString
     *
     * @return int|null
     */
    static function centsStringToOptionalCentsInt($centsString) {
        $centsString = trim($centsString);

        if (preg_match('/^[0-9]+$/', $centsString)) {
            return intval($centsString);
        } else {
            return null;
        }
    }



    /**
     * This function converts an amount expressed in dollars with limited
     * optional formatting to an amount expressed in cents. Fractional cents
     * are allowed to be passed in, but they will be truncated. So if
     * "$53.236" is passed in, `5323` will be returned, not `5324`.
     *
     * Examples:
     *
     * 53.2373483 => 5323
     * $1.        => 100
     * 3054       => 305400
     * $2.5       => 250
     * $1,045.20  => 104520
     *
     * @return int
     */
    static function dollarsToCentsInt($amountInDollars) {
        $amountInDollars = (string)$amountInDollars;

        /**
         * Regular expression information
         *
         * ^\$?
         * There may or may not be a dollar sign at the beginning
         *
         * ([0-9,]*)
         * Followed by zero or more digits and commas, this is the dollars amount
         *
         * (.
         * Followed by decimal point
         *
         * ([0-9]{0,2})
         * Followed by between 0 and 2 digits, this is the cents amount
         *
         * [0-9]*
         * Followed by zero or more digits, this is the fractional cents amount
         * which is ignored
         *
         * )?
         * The cents portion of the amount is optional
         *
         * $
         * There is nothing else before the end of the string
         *
         * Capture groups:
         *
         *  1   dollars amount including commas
         *  2   full cents amount including decimal point and fractional cents
         *  3   cents amount, a single digit represents tens of cents
         */

        $amountInDollarsExpression = '/^\$?([0-9,]*)(.([0-9]{0,2})[0-9]*)?$/';

        $result = preg_match($amountInDollarsExpression, $amountInDollars, $matches);

        /**
         * Remove the commas from the dollars amount
         */

        $dollarsAmount = $matches[1];
        $dollarsAmount = str_replace(',', '', $dollarsAmount);

        /**
         * Pad the cents amount if necessary
         */

        $centsAmount = isset($matches[3]) ? $matches[3] : '00';
        $centsAmount = str_pad($centsAmount, 2, '0');

        /**
         * Combine dollars and cents and convert to an integer
         */

        $amountInCents = (int)"{$dollarsAmount}{$centsAmount}";

        return $amountInCents;
    }



    /**
     * This function coalesces adjacent lines into paragraphs. Aside from
     * gluing adjacent lines together with a space character, no other
     * transformations on the text occur.
     *
     * @param [string] $lines
     *
     * @return [string]
     */
    static function linesToParagraphs(array $lines): array {
        $paragraphs = array_reduce(
            $lines,
            function($carry, $string) {
                if (trim($string) == '') {
                    array_push(
                        $carry,
                        []
                    );
                } else {
                    $array          = end($carry);
                    $array[]        = $string;
                    $key            = key($carry);
                    $carry[$key]    = $array;
                }
                return $carry;
            },
            [[]]
        );

        $paragraphs = array_filter(
            $paragraphs,
            function($paragraph) {
                return !empty($paragraph);
            }
        );

        return array_values(
            array_map(
                function($paragraph) {
                    return implode(
                        ' ',
                        $paragraph
                    );
                },
                $paragraphs
            )
        );
    }
    /* linesToParagraphs() */



    /**
     * This function will make the whitespace inside text visible, usually for
     * debugging purposes. I've added it here because I've needed and lost it
     * a few times and had a hard time finding it again and that usually means
     * it should be here. It also seems like the type of function that will
     * be perfected over time as I encounter different types of whitespace.
     *
     * This function also adds backslashes to backslashes so that the returned
     * string looks like what you'd type in PHP code to get the $text parameter
     * value.
     *
     * Having said that, the return value of this function should be treated
     * as informational only and not necessarily round-trippable.
     *
     * @param string $text
     *
     * @return string
     */
    static function textToTextWithVisibleWhitespace($text) {
        return addcslashes($text, "\\\n\r\t");
    }



    /**
     * This function parallels Colby.unixTimestampToElement()
     *
     * @param int? $timestamp
     * @param string? $nullTextContent
     *
     * @return string
     */
    static function
    timestampToHTML(
        $timestamp = null,
        $nullTextContent = null,
        string $className = ''
    ): string
    {
        $classAttribute =
        'class="time ' .
        cbhtml(
            $className
        ) .
        '"';

        if (
            is_numeric($timestamp)
        )
        {
            $datetime =
            gmdate(
                'c',
                $timestamp
            );

            $datetimeAttribute =
            "datetime=\"{$datetime}\"";

            $timestampForJavaScript =
            $timestamp *
            1000;

            $timestampAttribute =
            "data-timestamp=\"{$timestampForJavaScript}\"";

            $timeElementHTML =
            CBConvert::stringToCleanLine(<<<EOT

                <time
                    ${classAttribute}
                    ${datetimeAttribute}
                    ${timestampAttribute}
                >
                    &nbsp
                </time>

            EOT);

            return $timeElementHTML;
        }

        else
        {
            $nullTextContentAttribute =
            'data-nulltextcontent="' .
            cbhtml(
                $nullTextContent
            ) .
            '"';

            return
            "<span {$classAttribute} {$nullTextContentAttribute}></span>";
        }
    }
    // timestampToHTML()



    /**
     * 2013.05.05
     *
     * At this time Colby still supports IE8 and on that browser the javascript
     * timestamp display does not work. While Colby supports IE8 this function
     * can provide a readable date string for a timespan that's better than
     * nothing.
     *
     * When all browsers support the javascript timestamp display consider
     * removing this method.
     *
     * @returns string
     */
    static function timestampToOldBrowserReadableTime($timestamp) {
        /**
         * The `date` function will convert the timespan to the server's time
         * zone, which is probably more useful than UTC.
         *
         * F   A full textual representation of a month, such as January or
         *     March
         *
         * j   Day of the month without leading zeros
         *
         * Y   A full numeric representation of a year, 4 digits
         *
         * g   12-hour format of an hour without leading zeros
         *
         * i   Minutes with leading zeros
         *
         * a   Lowercase Ante meridiem and Post meridiem
         *
         * T   Timezone abbreviation
         */

         return date('F j, Y g:i a T', $timestamp);
    }



    /**
     * @return string
     */
    static function timestampToRFC3339($timestamp) {
        return gmdate(DateTime::RFC3339, $timestamp);
    }



    /**
     * This function should basically be deprecated because the MySQL
     * `DATETIME` type should never be used. Instead store the UNIX time
     * stamp in a `BIGINT` column. For more information see the snippet for
     * database upgrade 0006.
     *
     * For now, this function is being kept around for reference.
     *
     * @return string
     */
    static function timestampToSQLDateTime($timestamp) {
        if (!$timestamp) {
            return 'NULL';
        } else {
            $value = gmdate('Y-m-d H:i:s', $timestamp);

            // return value in single quotes because it should be ready
            // to insert into a SQL statement

            return "'{$value}'";
        }
    }



    /**
     * @return string
     */
    static function timestampToYearMonth($timestamp) {
        return gmdate('Ym', $timestamp);
    }

}
