<?php

final class CBUI {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */

    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v480.css', cbsysurl()),
        ];
    }
    /* CBHTMLOutput_CSSURLs() */


    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v470.js', cbsysurl()),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */


    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return [
            'CBContentStyleSheet',
            'CBMessageMarkup',
            'CBUIDropdown',
            'CBUISection',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */


    /* -- functions -- -- -- -- -- */

    /**
     * @return null
     */
    static function renderHalfSpace() {
        ?><div class="CBUIHalfSpace"></div><?php
    }

    /**
     * @return null
     */
    static function renderLinkSectionItem($href, $text) {
        CBUI::renderSectionItemStart();
        ?>
        <a class="CBUILink" href="<?= cbhtml($href) ?>">
            <span><?= cbhtml($text) ?></span>
        </a>
        <?php
        CBUI::renderSectionItemEnd();
    }

    /**
     * @return null
     */
    static function renderSectionStart() {
        ?><div class="CBUISection"><?php
    }

    /**
     * @return null
     */
    static function renderSectionEnd() {
        ?></div><?php
    }

    /**
     * @return null
     */
    static function renderSectionHeader($title = '', $description = '') {
        ?>
        <header class="CBUISectionHeader">
            <h1><?= cbhtml($title) ?></h1>
            <div><?= cbhtml($description) ?></div>
        </header>
        <?php
    }

    /**
     * @return null
     */
    static function renderSectionItemStart() {
        ?><div class="CBUISectionItem"><?php
    }

    /**
     * @return null
     */
    static function renderSectionItemEnd() {
        ?></div><?php
    }
}
/* CBUI */
