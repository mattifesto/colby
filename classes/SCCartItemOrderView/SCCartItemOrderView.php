<?php

final class SCCartItemOrderView {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v86.js', scliburl()),
        ];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBUI',
            'SCCartItemCartView',
        ];
    }

    /* -- CBInstall interfaces -- -- -- -- -- */

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        SCPreferences::installCartItemCheckoutViewClass(__CLASS__);
        SCPreferences::installCartItemOrderViewClass(__CLASS__);
    }

    /**
     * @return array
     */
    static function CBInstall_requiredClassNames(): array {
        return [
            'SCPreferences',
        ];
    }
}
