<?php

final class ColbyTests {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [Colby::flexpath(__CLASS__, 'v437.js', cbsysurl())];
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
