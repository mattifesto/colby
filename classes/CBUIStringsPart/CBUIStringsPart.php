<?php

final class CBUIStringsPart {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [Colby::flexpath(__CLASS__, 'v368.css', cbsysurl())];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [Colby::flexpath(__CLASS__, 'v368.js', cbsysurl())];
    }

    static function render($string1 = '', $string2 = '', $classNames = '') {
        ?>
        <div class="CBUIStringsPart <?= $classNames ?>">
            <div class="string1"><?= cbhtml($string1) ?></div>
            <div class="string2"><?= cbhtml($string2) ?></div>
        </div>
        <?php
    }
}
