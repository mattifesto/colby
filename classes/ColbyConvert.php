<?php

class ColbyConvert
{
    ///
    /// converts plain text into formatted content HTML
    ///
    ///  - trims whitespace
    ///  - converts html special characters to entities
    ///  - interprets textual formatting to create formatted content HTML
    ///
    public static function textToFormattedContent($text)
    {
        $html = self::textToHTML($text);

        $html = preg_replace('/[\r\n]+/', "\n<p>", $html);

        if ($html)
        {
            $html = "<p>{$html}";
        }

        return $html;
    }

    ///
    /// converts plain text to HTML
    ///
    ///  - trims whitespace
    ///  - converts html special characters to entities
    ///
    /// this fuction exists because it's so easy to forget
    /// the details on how this should be done
    /// such as the inclusion of ENT_QUOTES
    /// also because if I figure out something else is required
    /// I can now change it in just one place
    ///
    public static function textToHTML($text)
    {
        return htmlspecialchars(trim($text), ENT_QUOTES);
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
