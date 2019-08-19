<?php

final class CBMenuEditor {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v514.js', cbsysurl()),
        ];
    }


    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBMenuItemEditor',
            'CBUI',
            'CBUISpecArrayEditor',
            'CBUIStringEditor',
        ];
    }
}
