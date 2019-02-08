<?php

final class ColbyTests {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v437.js', cbsysurl()),
        ];
    }

    /* -- CBTest interfaces -- -- -- -- -- */

    /**
     * @return [[<className>, <testName>]]
     */
    static function CBTest_JavaScriptTests(): array {
        return [
            ['Colby', 'centsToDollars'],
            ['Colby', 'dateToString'],
            ['Colby', 'random160'],
        ];
    }

    /**
     * @return null
     */
    static function encryptionTest() {
        $originalText = 'Hello, world!';

        $cipherOutput = Colby::encrypt($originalText);

        $decryptedText = Colby::decrypt($cipherOutput);

        if ($decryptedText !== $originalText) {
            throw new Exception("Text was not the same after being encrypted and decrypted. Decrypted: \"{$decryptedText}\" Original: \"{$originalText}\"");
        }
    }
}
