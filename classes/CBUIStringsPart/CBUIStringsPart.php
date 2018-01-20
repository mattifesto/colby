<?php

final class CBUIStringsPart {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [Colby::flexpath(__CLASS__, 'v370.css', cbsysurl())];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [Colby::flexpath(__CLASS__, 'v368.js', cbsysurl())];
    }

    /**
     * @param string? $string1
     * @param string? $string2
     * @param string? $classNames
     *
     * @return void
     */
    static function render($string1 = '', $string2 = '', $classNames = ''): void {
        ?>
        <div class="CBUIStringsPart <?= $classNames ?>">
            <div class="string1"><?= cbhtml($string1) ?></div>
            <div class="string2"><?= cbhtml($string2) ?></div>
        </div>
        <?php
    }
}
