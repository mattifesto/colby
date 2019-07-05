<?php

final class ColbyTests {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v484.js', cbsysurl()),
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
                'name' => 'dateToString',
                'title' => 'Colby.dateToString()',
            ],
            (object)[
                'name' => 'random160',
                'title' => 'Colby.random160()',
            ],
            (object)[
                'name' => 'encrypt',
                'title' => 'Colby::encrypt() and Colby::decrypt()',
                'type' => 'server',
            ],
            (object)[
                'name' => 'displayAndReportError',
                'title' => 'Colby.displayAndReportError()',
                'type' => 'interactive',
            ],
        ];
    }
    /* CBTest_getTests() */


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
