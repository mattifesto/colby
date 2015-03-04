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
        $tests      = array();
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

        foreach ($tests as $test) {
            $actual = CBMarkaround::paragraphToHTML($test[0]);
            if ($actual != $test[1]) {
                $s = __METHOD__ . ": The input '{$test[0]}' did not produce " .
                "the expected output '{$test[1]}'.";
                throw new RuntimeException($s);
            }
        }
    }
}
