<?php

class CBUnitTestsForColbyMarkaroundParser {

    /**
     * @return void
     */
    public static function runAll() {
        self::orderedListTest();
        self::paragraphTest();
        self::unorderedListTest();
    }

    /**
     * @return void
     */
    public static function orderedListTest()
    {
        $markaround = "17.5\" by 21.5\"";
        $expected   = "<p>17.5&quot; by 21.5&quot;\n";
        $actual     = CBMarkaround::markaroundToHTML($markaround);

        self::verifyActualStringIsExpected($actual, $expected);

        $markaround = "17. 5\" by 21.5\"";
        $expected   = "<ol>\n<li><p>5&quot; by 21.5&quot;\n</ol>\n";
        $actual     = CBMarkaround::markaroundToHTML($markaround);

        self::verifyActualStringIsExpected($actual, $expected);
    }

    /**
     * @return void
     */
    public static function paragraphTest()
    {
        $markaround = "Hello *world!*";
        $expected   = "<p>Hello <b>world!</b>\n";
        $actual     = CBMarkaround::markaroundToHTML($markaround);

        self::verifyActualStringIsExpected($actual, $expected);
    }

    /**
     * @return void
     */
    public static function unorderedListTest()
    {
        $markaround = "-Hello";
        $expected   = "<ul>\n<li><p>Hello\n</ul>\n";
        $actual     = CBMarkaround::markaroundToHTML($markaround);

        self::verifyActualStringIsExpected($actual, $expected);
    }

    /**
     * @return void
     */
    public static function verifyActualStringIsExpected($actual, $expected)
    {
        if ($actual != $expected)
        {
            $expected2  = ColbyConvert::textToTextWithVisibleWhitespace($expected);
            $actual2    = ColbyConvert::textToTextWithVisibleWhitespace($actual);

            throw new RuntimeException(__METHOD__ .
                ": expected: \"{$expected2}\", actual: \"{$actual2}\"");
        }
    }
}
