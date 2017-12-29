<?php

final class CBMenuEditor {

    /**
     * @return [string]
     */
    static function requiredClassNames() {
        return ['CBMenuItemEditor', 'CBUI', 'CBUISpecArrayEditor',
                'CBUIStringEditor'];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [Colby::flexpath(__CLASS__, 'v361.js', cbsysurl())];
    }
}
