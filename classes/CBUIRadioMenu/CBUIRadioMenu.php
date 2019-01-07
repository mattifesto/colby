<?php

/**
 * @deprecated 2018_11_23
 *
 *      Use CBUI_selectable and CBMutator
 */
final class CBUIRadioMenu {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v469.js', cbsysurl()),
        ];
    }
}
