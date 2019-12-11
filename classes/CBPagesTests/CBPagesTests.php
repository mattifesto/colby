<?php

final class CBPagesTests {

    /* -- CBTest interfaces -- -- -- -- -- */



    static function CBTest_getTests(): array {
        return [
            (object)[
                'name' => 'stringToDencodedURIPath',
                'type' => 'server',
            ],
        ];
    }



    /* -- tests -- -- -- -- -- */



    /**
     * @return object
     */
    static function CBTest_stringToDencodedURIPath(): stdClass {
        $tests = [
            ['',                                    ''],
            ['///////',                             ''],
            ['///bob// //sam///',                   'bob/sam'],
            ['bob/sam',                             'bob/sam'],
            ['///Piñata // Örtega Smith /// //',    'piata/rtega-smith']
        ];

        foreach ($tests as $test) {
            $dencodedURIPath = CBPages::stringToDencodedURIPath($test[0]);

            if ($dencodedURIPath !== $test[1]) {
                throw new Exception("The parameter \"{$test[0]}\" returned " .
                "the path \"{$dencodedURIPath}\" instead of the expected " .
                "path \"{$test[1]}\"");
            }
        }

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_stringToDencodedURIPath() */

}
