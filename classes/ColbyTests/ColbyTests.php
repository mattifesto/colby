<?php

final class ColbyTests {

    /* -- CBAjax interfaces -- -- -- -- -- */

    /**
     * @return null
     */
    static function CBAjax_requestWithPHPError() {
        $useLongMessage = false;

        if ($useLongMessage) {
            throw new RuntimeException(
                str_repeat(
                    "This is a long exception message. ",
                    1000
                )
            );
        }

        throw new RuntimeException(
            'This is a short exception message'
        );
    }
    /* CBAjax_requestWithPHPError() */


    /**
     * @return string
     */
    static function CBAjax_requestWithPHPError_group() {
        return 'Developers';
    }
    /* CBAjax_requestWithPHPError_group() */


    /* -- CBHTMLOutput interfaces -- -- -- -- -- */

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v529.js', cbsysurl()),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */


    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBErrorHandler',
            'Colby',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */


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
