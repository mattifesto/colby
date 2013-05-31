<?php

ColbyConvertMarkaroundToHTMLTests::paragraphTest();
ColbyConvertMarkaroundToHTMLTests::unorderedListTest();

/**
 *
 */
class ColbyConvertMarkaroundToHTMLTests
{
    /**
     * @return void
     */
    public static function paragraphTest()
    {
        $markaround = "Hello";
        $expected = "<p>Hello\n";
        $actual = ColbyConvert::markaroundToHTML($markaround);

        self::verifyActualStringIsExpected($actual, $expected);
    }

    /**
     * @return void
     */
    public static function unorderedListTest()
    {
        // Unordered lists

        $markaround = "-Hello";
        $expected = "<ul>\n<li><p>Hello\n</ul>\n";
        $actual = ColbyConvert::markaroundToHTML($markaround);

        self::verifyActualStringIsExpected($actual, $expected);
    }

    /**
     * @return void
     */
    public static function verifyActualStringIsExpected($actual, $expected)
    {
        if ($actual != $expected)
        {
            $expected2 = ColbyConvert::textToTextWithVisibleWhitespace($expected);
            $actual2 = ColbyConvert::textToTextWithVisibleWhitespace($actual);

            throw new RuntimeException("expected: \"{$expected2}\", actual: \"{$actual2}\"");
        }
    }
}
