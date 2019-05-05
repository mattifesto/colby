<?php

final class CBActiveArrayTests {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v472.js', cbsysurl()),
        ];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBActiveArray',
            'CBActiveObject',
            'CBModel',
            'CBTest',
        ];
    }

    /* -- CBTest interfaces -- -- -- -- -- */

    /**
     * @return [[<className>, <testName>]]
     */
    static function CBTest_JavaScriptTests(): array {
        return [
            ['CBActiveArray', 'deactivate'],
            ['CBActiveArray', 'events'],
            ['CBActiveArray', 'find'],
            ['CBActiveArray', 'general'],
            ['CBActiveArray', 'replace'],
            ['CBActiveArray', 'slice'],
        ];
    }
}
