<?php

class CBMarkaroundTests {

    /* -- CBTest interfaces -- -- -- -- -- */



    /**
     * @return [object]
     */
    static function CBTest_getTests(): array {
        return [
            (object)[
                'name' => 'paragraphToHTML',
                'type' => 'server',
            ],
        ];
    }
    /* CBTest_getTests() */



    /* -- tests -- -- -- -- -- */



    /**
     * @return object
     */
    static function CBTest_paragraphToHTML(): stdClass {
        $tests[]    = array('Hello \\\\ world!',
                            'Hello \\ world!',
                            'Hello \\ world!');
        $tests[]    = array('Hello \/ world!',
                            'Hello / world!',
                            'Hello / world!');
        $tests[]    = array('Hello \\* world!',
                            'Hello * world!',
                            'Hello * world!');
        $tests[]    = array('Hello \\{ world!',
                            'Hello { world!',
                            'Hello { world!');
        $tests[]    = array('Hello \\} world!',
                            'Hello } world!',
                            'Hello } world!');
        $tests[]    = array('Hello \\_ world!',
                            'Hello _ world!',
                            'Hello _ world!');
        $tests[]    = array('Hello \\` world!',
                            'Hello ` world!',
                            'Hello ` world!');

        $tests[]    = array('Hello *world*!',
                            'Hello <b>world</b>!',
                            'Hello world!');
        $tests[]    = array('Hello _world_!',
                            'Hello <i>world</i>!',
                            'Hello world!');
        $tests[]    = array('Hello {world}!',
                            'Hello <cite>world</cite>!',
                            'Hello world!');
        $tests[]    = array('Hello `world`!',
                            'Hello <code>world</code>!',
                            'Hello world!');
        $tests[]    = array('Hello / earth/world!',
                            'Hello <br> earth/world!',
                            'Hello  earth/world!');

        $tests[]    = array('Hello * world*!',
                            'Hello * world*!',
                            'Hello * world*!');
        $tests[]    = array('Hello _ world_!',
                            'Hello _ world_!',
                            'Hello _ world_!');
        $tests[]    = array('Hello { world}!',
                            'Hello { world}!',
                            'Hello { world}!');
        $tests[]    = array('Hello ` world`!',
                            'Hello ` world`!',
                            'Hello ` world`!');

        $tests[]    = array('Hello \*world*!',
                            'Hello *world*!',
                            'Hello *world*!');
        $tests[]    = array('Hello \_world_!',
                            'Hello _world_!',
                            'Hello _world_!');
        $tests[]    = array('Hello \{world}!',
                            'Hello {world}!',
                            'Hello {world}!');
        $tests[]    = array('Hello \`world`!',
                            'Hello `world`!',
                            'Hello `world`!');
        $tests[]    = array('Hello \/ earth/world!',
                            'Hello / earth/world!',
                            'Hello / earth/world!');

        $tests[]    = array('{words{separated}by{curlybrackets}}',
                            '<cite>words{separated</cite>by<cite>curlybrackets</cite>}',
                            'words{separatedbycurlybrackets}');


        foreach ($tests as $test) {
            $html = CBMarkaround::paragraphToHTML($test[0]);
            if ($html != $test[1]) {
                $s = __METHOD__ . ": The input '{$test[0]}' produced the HTML " .
                "output '{$html}' instead of the expected output " .
                "'{$test[1]}'.";
                throw new RuntimeException($s);
            }
            $text = CBMarkaround::paragraphToText($test[0]);
            if ($text != $test[2]) {
                $s = __METHOD__ . ": The input '{$test[0]}' produced the text " .
                "output '{$text}' instead of the expected output " .
                "'{$test[2]}'.";
                throw new RuntimeException($s);
            }
        }

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_paragraphToHTML() */

}
