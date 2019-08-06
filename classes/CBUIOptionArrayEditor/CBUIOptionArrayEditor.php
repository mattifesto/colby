<?php

final class CBUIOptionArrayEditor {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'js', cbsysurl()),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */
}
