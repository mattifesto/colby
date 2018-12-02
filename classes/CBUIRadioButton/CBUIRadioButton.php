<?php

/**
 * A CBUIRadioButton is a container similar to CBUISectionItem4. If a radio
 * button is selected, it displays a highlight.
 */
final class CBUIRadioButton {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v468.css', cbsysurl()),
        ];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v468.js', cbsysurl()),
        ];
    }
}
