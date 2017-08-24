<?php

final class CBAdminPageForCSSVariables {

    /**
     * @return [string]
     */
    static function adminPageMenuNamePath() {
        return ['help', 'cssvariables'];
    }

    /**
     * @return stdClass
     */
    static function adminPagePermissions() {
        return (object)['group' => 'Developers'];
    }

    /**
     * @return null
     */
    static function adminPageRenderContent() {
        CBHTMLOutput::setTitleHTML('CSS Variables');

        ?>
        <div class="CBLightTheme">
            <?php
            CBView::renderSpec((object)[
                'className' => 'CBTextView2',
                'contentAsCommonMark' => '# CBLightTheme',
                'CSSClassNames' => 'center',
            ]);

            CBAdminPageForCSSVariables::render();
            ?>
        </div>
        <div class="CBDarkTheme">
            <?php
            CBView::renderSpec((object)[
                'className' => 'CBTextView2',
                'contentAsCommonMark' => '# CBDarkTheme',
                'CSSClassNames' => 'center',
            ]);

            CBAdminPageForCSSVariables::render();
            ?>
        </div>
        <div class="CBAdminTheme">
            <?php
            CBView::renderSpec((object)[
                'className' => 'CBTextView2',
                'contentAsCommonMark' => '# CBAdminTheme',
                'CSSClassNames' => 'center',
            ]);

            CBAdminPageForCSSVariables::render();
            ?>
        </div>

        <?php
    }

    static function CBHTMLOutput_CSSURLs() {
        return [Colby::flexpath(__CLASS__, 'css', cbsysurl())];
    }

    static function render() {
        ?>

        <div class="b">
            <?php
            CBAdminPageForCSSVariables::renderText();
            CBAdminPageForCSSVariables::renderPanel('panel1');
            ?>
        </div>
        <div class="b b2">
            <?php
                CBAdminPageForCSSVariables::renderText();
                CBAdminPageForCSSVariables::renderPanel('panel2');
             ?>
        </div>

        <?php
    }

    static function renderText() {
        CBView::renderSpec((object)[
            'className' => 'CBTextView2',
            'contentAsCommonMark' => <<<EOT
# Text Content

Text content should look good on top of CBBackgroundColor and
CBBackgroundColor2. The background colors can be alternated to create clear
borders between sections on a page.

EOT
        ]);
    }

    static function renderPanel($className) {
        ?>
        <div class="panel <?= cbhtml($className) ?>">
            <div>
                <?php CBAdminPageForCSSVariables::renderPanelText(); ?>
            </div>
        </div>
        <?php
    }

    static function renderPanelText() {
        CBView::renderSpec((object)[
            'className' => 'CBTextView2',
            'contentAsCommonMark' => <<<EOT
# Panel

A panel is a rectangle with the alternate background color and a CBLineColor
border. Panels are used when rendering lists such as a list of blog posts.

EOT
        ]);
    }

}
