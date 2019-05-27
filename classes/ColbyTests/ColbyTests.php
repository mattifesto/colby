<?php

final class ColbyTests {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v474.js', cbsysurl()),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */


    /* -- CBTest interfaces -- -- -- -- -- */

    /**
     * @return [object]
     */
    static function CBTest_getTests(): array {
        return [
            (object)[
                'title' => 'Colby.dateToString()',
                'name' => 'dateToString',
            ],
            (object)[
                'title' => 'Colby.random160()',
                'name' => 'random160',
            ],
            (object)[
                'type' => 'server',
                'title' => 'Colby::encrypt() and Colby::decrypt()',
                'name' => 'encrypt',
            ],
        ];
    }
    /* CBTest_tests() */


    /**
     * @return object
     */
    static function CBTest_encrypt(): stdClass {
        $originalText = 'Hello, world!';

        $cipherOutput = Colby::encrypt($originalText);

        $decryptedText = Colby::decrypt($cipherOutput);

        if ($decryptedText !== $originalText) {
            return CBTest::resultMismatchFailure(
                'Colby::encrypt() test',
                $decryptedText,
                $originalText
            );
        }

        return (object) [
            'succeeded' => true,
        ];
    }
    /* CBTest_encrypt() */
}
/* ColbyTests */
