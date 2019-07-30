<?php

final class ColbyConvert {

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
     * @note functional programming
     * @note needs testing
     *
     * This function coalesces adjacent lines into paragraphs. Aside from
     * gluing adjacent lines together with a space character, no other
     * transformations on the text occur.
     *
     * @return array
     */
    static function linesToParagraphs($lines) {
        $paragraphs = array_reduce($lines, function($carry, $string) {
            if (trim($string) == '') {
                array_push($carry, array());
            } else {
                $array          = end($carry);
                $array[]        = $string;
                $key            = key($carry);
                $carry[$key]    = $array;
            }
            return $carry;
        }, array(array()));

        $paragraphs = array_filter($paragraphs, function($paragraph) { return !empty($paragraph); });

        return array_map(function($paragraph) { return implode(' ', $paragraph); }, $paragraphs);
    }


    /**
     * @deprecated use CBConvert::stringToStub()
     */
    static function textToStub($text) {
        return CBConvert::stringToStub($text);
    }


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
     * @return string
     */
    static function timestampToFriendlyTime($timestamp) {
        $delta = time() - $timestamp;

        // Less than a minute ago

        if ($delta < 60) {
            return 'less than a minute ago';
        }

        // One minute ago

        if ($delta < 120) {
            return 'one minute ago';
        }

        // Less than an hour ago

        if ($delta < 3600) {
            return intval(($delta / 60)) . ' minutes ago';
        }

        // One hour ago (between 1 and 2 hours ago)

        if ($delta < 7200) {
            return 'one hour ago';
        }

        // Less than 24 hours ago

        if ($delta < 86400) {
            return intval(($delta / 3600)) . ' hours ago';
        }

        // return values will need the local time zone modified timestamp
        // this function will give more accurate results than wordpress
        // in cases where the timezone changes after posts have been created
        // they use a cached time for the old timezone, it's dumb
        // (this is why one should only store utc times in a database)

        if ($userRow = ColbyUser::userRow()) {
            $gmtOffset = intval($userRow->facebookTimeZone) * 3600;
        } else {
            $gmtOffset = 0;
        }

        //$gmtOffset = intval(get_option('gmt_offset')) * 3600;
        $localTimeStamp = $timestamp + $gmtOffset;

        // Less than 6 days ago

        // BUGBUG: using date does timezone conversions
        //         instead use DateTime conversions as show below

        if ($delta < 518400) {
            return date('l', $localTimeStamp) .
                ' at ' .
                date(get_option('time_format'), $localTimeStamp);

        }

        return date(get_option('date_format'), $localTimeStamp) .
            ' at ' .
            date(get_option('time_format'), $localTimeStamp);
    }

    /**
     * @param int? $timestamp
     * @param string? $nullTextContent
     *
     * @return string
     */
    static function timestampToHTML($timestamp = null, $nullTextContent = null) {
        $classAttribute = 'class="time"';

        if (is_numeric($timestamp)) {
            $datetime = gmdate('c', $timestamp);
            $datetimeAttribute = "datetime=\"{$datetime}\"";
            $timestampForJavaScript = $timestamp * 1000;
            $timestampAttribute = "data-timestamp=\"{$timestampForJavaScript}\"";

            return "<time {$classAttribute} {$datetimeAttribute} {$timestampAttribute}></time>";
        } else {
            $nullTextContentAttribute = 'data-nulltextcontent="' . cbhtml($nullTextContent). '"';

            return "<span {$classAttribute} {$nullTextContentAttribute}></span>";
        }
    }

    /**
     * 2013.05.05
     *
     * This function looks up the time zone that Facebook gives us for a user
     * and converts a timestamp to that time zone. However, Colby has since
     * moved to sending timestamps to the browser for the browser to convert
     * to whatever it feels the user's current time zone is.
     *
     * So, this function is not very useful especially since a user has to be
     * logged in. Furthermore, for some reason it's outputting in the RFC3399
     * format which is really not very friendly.
     *
     * Most likely, this function should be removed. I'm letting it stay
     * for now because I need to think more about whether it is potentially
     * useful. Also, when we remove this we might want to stop storing the
     * Facebook time zone data since I can't think of a use for that anymore
     * either.
     *
     * @return string
     */
    static function timestampToLocalUserTime($timestamp) {
        $date = new DateTime("@{$timestamp}", new DateTimeZone('UTC'));

        if ($userRow = ColbyUser::userRow()) {
            $timeZoneName = timezone_name_from_abbr('', $userRow->facebookTimeZone * 3600, false);

            $timeZone = new DateTimeZone($timeZoneName);

            $date->setTimeZone($timeZone);
        }

        return $date->format(DateTime::RFC3339);
    }

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
