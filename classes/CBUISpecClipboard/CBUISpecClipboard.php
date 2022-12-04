<?php

final class CBUISpecClipboard {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [
            Colby::flexpath(
                __CLASS__,
                'v361.js',
                cbsysurl()
            ),
        ];
    }
}
