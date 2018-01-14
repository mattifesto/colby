<?php

final class CBUI {

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

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return ['CBContentStyleSheet', 'CBMessageMarkup', 'CBUIDropdown'];
    }

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
     * @param object args
     *
     *      {
     *          title: string?
     *          description: string?
     *      }
     *
     * @return void
     */
    static function renderTitleAndDescriptionSectionItemPart(stdClass $args): void {
        $title = CBModel::value($args, 'title', '', 'strval');
        $description = CBModel::value($args, 'description', '', 'strval');

        echo '<div class="CBUITitleAndDescriptionSectionItemPart">';

        if (!empty($title)) {
            echo '<div class="title">', cbhtml($title), '</div>';
        }

        if (!empty($description)) {
            echo '<div class="description">', cbhtml($description), '</div>';
        }

        echo '</div>';
    }
}
