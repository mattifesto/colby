<?php

/**
 * @deprecated 2018_11_23
 *
 *      Use CBUIRadioButton and/or CBMutator
 */
final class CBUIRadioMenu {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v468.js', cbsysurl()),
        ];
    }
}
