<?php

final class ColbyTests {

    /* -- CBAjax interfaces -- -- -- -- -- */



    /**
     * @return void
     */
    static function CBAjax_requestWithPHPError(): void {
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
    static function CBAjax_requestWithPHPError_getUserGroupClassName(): string {
        return 'CBDevelopersUserGroup';
    }



    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v604.js', cbsysurl()),
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBErrorHandler',
            'CBModel',
            'CBTest',
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
            ],
            (object)[
                'name' => 'random160',
            ],
            (object)[
                'name' => 'URIToImage',
            ],
            (object)[
                'name' => 'encrypt',
                'type' => 'server',
            ],
            (object)[
                'name' => 'displayAndReportError',
                'type' => 'interactive',
            ],
        ];
    }
    /* CBTest_getTests() */



    /* -- tests -- -- -- -- -- */



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
