<?php

final class CBLinkView1Editor {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v474.js', cbsysurl()),
        ];
    }


    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return [
            'CBImage',
            'CBUIImageChooser',
            'CBUISelector',
            'CBUIStringEditor',
            'Colby',
        ];
    }
}
