<?php

final class CBUISectionItem4 {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v469.css', cbsysurl()),
        ];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v368.js', cbsysurl()),
        ];
    }

    /**
     * @return void
     */
    static function renderOpen(): void {
        echo '<div class="CBUISectionItem4">';
    }

    /**
     * @return void
     */
    static function renderClose(): void {
        echo '</div>';
    }
}
