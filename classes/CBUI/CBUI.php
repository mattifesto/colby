<?php

final class CBUI {

    /**
     * @return null
     */
    static function renderHalfSpace() {
        ?><div class="CBUIHalfSpace"></div><?php
    }

    /**
     * @return null
     */
    static function renderKeyValue($key = '', $value = '') {
        ?>
        <div class="CBUIKeyValue">
            <div class="key"><?= cbhtml($key) ?></div>
            <div class="value"><?= cbhtml($value) ?></div>
        </div>
        <?php
    }

    /**
     * @return null
     */
    static function renderKeyValueSectionItem($key = '', $value = '') {
        CBUI::renderSectionItemStart();
        CBUI::renderKeyValue($key, $value);
        CBUI::renderSectionItemEnd();
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
            <div><?= cbhtml($description) ?><div>
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

    /**
     * @return [string]
     */
    static function requiredClassNames() {
        return ['CBUIDropdown'];
    }

    /**
     * @return [string]
     */
    public static function requiredCSSURLs() {
        return [CBSystemURL . '/javascript/CBUI.css'];
    }

    /**
     * @return [string]
     */
    public static function requiredJavaScriptURLs() {
        return [CBSystemURL . '/javascript/CBUI.js'];
    }

    /**
     * @param string $filename
     *
     * @return string
     */
    public static function URL($filename) {
        $className = __CLASS__;
        return CBSystemURL . "/classes/{$className}/{$filename}";
    }
}
