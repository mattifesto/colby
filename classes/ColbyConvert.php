<?php

class ColbyConvert
{
    /**
     * @return string
     */
    public static function markaroundToHTML($markaround)
    {
        include_once(COLBY_SITE_DIRECTORY . '/colby/classes/ColbyMarkaroundParser.php');

        $parser = ColbyMarkaroundParser::parserWithMarkaround($markaround);

        return $parser->html();
    }

    /**
     * Deprecated
     *  2013.01.14
     *
     * @return string
     */
    public static function textToFormattedContent($text)
    {
        Colby::debugLog('`ColbyConvert::textToFormattedContent` has been deprecated in favor of `ColbyConvert::markaroundToHTML`');

        return self::markaroundToHTML($text);
    }

    /**
     * This function converts plain text to HTML by escaping it.
     *
     * This function is important despite how small it is. It represents the
     * current best practices for exactly how to escape text for use in HTML.
     *
     * History:
     *
     * 2013.01.13
     *  Changed this function to not trim the text for two reasons. One: it was
     *  an unadvertised side effect that was non-obvious. Two: When calling this
     *  function for text to be used in a pre-formatted elements the leading and
     *  trailing whitespace should be preserved.
     *
     * @param string $text
     *
     * @return string
     *  The $text parameter escaped for use in HTML.
     */
    public static function textToHTML($text)
    {
        return htmlspecialchars($text, ENT_QUOTES);
    }

    ///
    /// converts plain text to a very clean URL stub
    ///
    /// I debated a long time before including this function in Colby. I was
    /// starting to work on an image uploader where having friendly URL stubs
    /// would be nice.
    ///
    /// the facts made me finally decide to include this code:
    ///
    ///  - it's commonly useful
    ///  - the implementation turned out to be very simple
    ///  - friendly stubs can improve SEO
    ///  - regular users like this kind behavior from web applications
    ///    it makes them happy
    ///
    /// WordPress and other people have implemented this with a long list
    /// of character replacements, but the iconv() function makes it much
    /// easier.
    ///
    /// caution:
    ///
    ///   This function should not be assumed to behave a certain way. It's
    ///   intended to change its behavior as implementation opportunities
    ///   arise and as the nature of URLs changes. It will always return a URL
    ///   that's clean and meant to be forward compatible, but the exact nature
    ///   ot the returned URLs may change over time.
    ///
    /// current algorithm:
    ///
    /// 1. iconv() converts UTF-8 to ASCII
    ///    since ASCII is a subset of UTF-8 the string is still also UTF-8
    ///    iconv will do some handy coversions, like '£' becomes 'lb'
    ///
    /// 2. replace sequences of spaces and hyphens with one hyphen
    ///
    /// 3. remove leading and trailing hyphens
    ///
    /// 4. remove all characters except: a-z, A-Z, 0-9, and hyphen
    ///
    /// 5. make all characters lowercase
    ///
    /// common example: 'Piñata Örtega' --> 'pinata-ortega'
    ///
    public static function textToStub($text)
    {
        $stub = iconv('UTF-8', 'ASCII//TRANSLIT', $text);

        $patterns =     array('/[\s-]+/', '/^-+/', '/-+$/', '/[^a-zA-Z0-9-]/');
        $replacements = array('-'       , ''     , ''     , '');

        $stub = preg_replace($patterns, $replacements, $stub);

        return strtolower($stub);
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
    public static function textToTextWithVisibleWhitespace($text)
    {
        return addcslashes($text, "\\\n\r\t");
    }

    /**
     * @return string
     */
    public static function timestampToFriendlyTime($timestamp)
    {
        $delta = time() - $timestamp;

        // Less than a minute ago

        if ($delta < 60)
        {
            return 'less than a minute ago';
        }

        // One minute ago

        if ($delta < 120)
        {
            return 'one minute ago';
        }

        // Less than an hour ago

        if ($delta < 3600)
        {
            return intval(($delta / 60)) . ' minutes ago';
        }

        // One hour ago (between 1 and 2 hours ago)

        if ($delta < 7200)
        {
            return 'one hour ago';
        }

        // Less than 24 hours ago

        if ($delta < 86400)
        {
            return intval(($delta / 3600)) . ' hours ago';
        }

        // return values will need the local time zone modified timestamp
        // this function will give more accurate results than wordpress
        // in cases where the timezone changes after posts have been created
        // they use a cached time for the old timezone, it's dumb
        // (this is why one should only store utc times in a database)

        if ($userRow = ColbyUser::userRow())
        {
            $gmtOffset = intval($userRow->facebookTimeZone) * 3600;
        }
        else
        {
            $gmtOffset = 0;
        }

        //$gmtOffset = intval(get_option('gmt_offset')) * 3600;
        $localTimeStamp = $timestamp + $gmtOffset;

        // Less than 6 days ago

        // BUGBUG: using date does timezone conversions
        //         instead use DateTime conversions as show below

        if ($delta < 518400)
        {
            return date('l', $localTimeStamp) .
                ' at ' .
                date(get_option('time_format'), $localTimeStamp);

        }

        return date(get_option('date_format'), $localTimeStamp) .
            ' at ' .
            date(get_option('time_format'), $localTimeStamp);
    }

    /**
     * @return string
     */
    public static function timestampToLocalUserTime($timestamp)
    {
        $date = new DateTime("@{$timestamp}", new DateTimeZone('UTC'));

        if ($userRow = ColbyUser::userRow())
        {
            $timeZoneName = timezone_name_from_abbr('', $userRow->facebookTimeZone * 3600, false);

            $timeZone = new DateTimeZone($timeZoneName);

            $date->setTimeZone($timeZone);
        }

        return $date->format(DateTime::RFC3339);
    }

    /**
     * @return string
     */
    public static function timestampToSQLDateTime($timestamp)
    {
        if (empty($timestamp))
        {
            return 'NULL';
        }
        else
        {
            $value = gmdate('Y-m-d H:i:s', $timestamp);

            // return value in single quotes because it should be ready
            // to insert into a SQL statement

            return "'{$value}'";
        }
    }
}
