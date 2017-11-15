<?php

final class ColbyTests {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [Colby::flexpath(__CLASS__, 'js', cbsysurl())];
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
