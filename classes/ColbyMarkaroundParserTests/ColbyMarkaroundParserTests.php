<?php

class ColbyMarkaroundParserTests {

    /* -- CBTest interfaces -- -- -- -- -- */



    /**
     * @return [object]
     */
    static function CBTest_getTests(): array {
        return [
            (object)[
                'name' => 'orderedList',
                'type' => 'server',
            ],
            (object)[
                'name' => 'paragraph',
                'type' => 'server',
            ],
            (object)[
                'name' => 'unorderedList',
                'type' => 'server',
            ],
        ];
    }
    /* CBTest_getTests() */



    /* -- tests -- -- -- -- -- */



    /**
     * @return object
     */
    static function CBTest_orderedList(): stdClass {
        $markaround = "17.5\" by 21.5\"";
        $expected   = "<p>17.5&quot; by 21.5&quot;\n";
        $actual     = CBMarkaround::markaroundToHTML($markaround);

        self::verifyActualStringIsExpected($actual, $expected);

        $markaround = "17. 5\" by 21.5\"";
        $expected   = "<ol>\n<li><p>5&quot; by 21.5&quot;\n</ol>\n";
        $actual     = CBMarkaround::markaroundToHTML($markaround);

        self::verifyActualStringIsExpected($actual, $expected);

        return (object)[
            'succeeded' => true,
        ];
    }



    /**
     * @return object
     */
    static function CBTest_paragraph(): stdClass {
        $markaround = "Hello *world!*";
        $expected   = "<p>Hello <b>world!</b>\n";
        $actual     = CBMarkaround::markaroundToHTML($markaround);

        self::verifyActualStringIsExpected($actual, $expected);

        return (object)[
            'succeeded' => true,
        ];
    }



    /**
     * @return object
     */
    static function CBTest_unorderedList(): stdClass {
        $markaround = "-Hello";
        $expected   = "<ul>\n<li><p>Hello\n</ul>\n";
        $actual     = CBMarkaround::markaroundToHTML($markaround);

        self::verifyActualStringIsExpected($actual, $expected);

        return (object)[
            'succeeded' => true,
        ];
    }



    /* -- functionsl -- -- -- -- -- */



    /**
     * @return null
     */
    static function verifyActualStringIsExpected($actual, $expected) {
        if ($actual != $expected) {
            $expected2  = ColbyConvert::textToTextWithVisibleWhitespace($expected);
            $actual2    = ColbyConvert::textToTextWithVisibleWhitespace($actual);

            throw new RuntimeException(__METHOD__ .
                ": expected: \"{$expected2}\", actual: \"{$actual2}\"");
        }
    }

}
