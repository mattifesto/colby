<?php

class CBUnitTestsForCBMarkaround {

    /**
     * @return void
     */
    public static function runAll() {
        self::runTestForParagraphToHTML();
    }

    /**
     * @return void
     */
    public static function runTestForParagraphToHTML() {
        $tests[]    = array('Hello \\\\ world!',
                            'Hello \\ world!');
        $tests[]    = array('Hello \/ world!',
                            'Hello / world!');
        $tests[]    = array('Hello \\* world!',
                            'Hello * world!');
        $tests[]    = array('Hello \\{ world!',
                            'Hello { world!');
        $tests[]    = array('Hello \\} world!',
                            'Hello } world!');
        $tests[]    = array('Hello \\_ world!',
                            'Hello _ world!');
        $tests[]    = array('Hello \\` world!',
                            'Hello ` world!');
        $tests[]    = array('Hello *world*!',
                            'Hello <b>world</b>!');
        $tests[]    = array('Hello _world_!',
                            'Hello <i>world</i>!');
        $tests[]    = array('Hello {world}!',
                            'Hello <cite>world</cite>!');
        $tests[]    = array('Hello `world`!',
                            'Hello <code>world</code>!');
        $tests[]    = array('Hello / earth/world!',
                            'Hello <br> earth/world!');

        foreach ($tests as $test) {
            $actual = CBMarkaround::paragraphToHTML($test[0]);
            if ($actual != $test[1]) {
                $s = __METHOD__ . ": The input '{$test[0]}' produced the " .
                "output '{$actual}' instead of the expected output " .
                "'{$test[1]}'.";
                throw new RuntimeException($s);
            }
        }
    }
}
