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
            <?php CBAdminPageForCSSVariables::renderText() ?>
        </div>
        <div class="b b2">
            <?php
                CBAdminPageForCSSVariables::renderText();
                CBAdminPageForCSSVariables::renderPanel();
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

    static function renderPanel() {
        ?>
        <div class="panel">
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

A panel scenario occurs when a CBBackgroundColor rectangle with a CBLineColor
border is place on top of a CBBackgroundColor2 background. This scenario is use
for rendering lists such as a list of blog posts.

Panels are not intended to be used with CBBackgroundColor2 on top of
CBBackgroundColor. This is because CBBackgroundColor is intended to be the
preferable background color of the two background colors.

EOT
        ]);
    }

}
