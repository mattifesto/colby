<?php

final class CBCSSVariablesHelp {

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

            CBCSSVariablesHelp::render();
            ?>
        </div>
        <div class="CBDarkTheme">
            <?php
            CBView::renderSpec((object)[
                'className' => 'CBTextView2',
                'contentAsCommonMark' => '# CBDarkTheme',
                'CSSClassNames' => 'center',
            ]);

            CBCSSVariablesHelp::render();
            ?>
        </div>
        <div class="CBAdminTheme">
            <?php
            CBView::renderSpec((object)[
                'className' => 'CBTextView2',
                'contentAsCommonMark' => '# CBAdminTheme',
                'CSSClassNames' => 'center',
            ]);

            CBCSSVariablesHelp::render();
            ?>
        </div>

        <?php
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [Colby::flexpath(__CLASS__, 'v374.css', cbsysurl())];
    }

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $spec = CBModels::fetchSpecByID(CBHelpAdminMenu::ID);

        $spec->items[] = (object)[
            'className' => 'CBMenuItem',
            'name' => 'cssvariables',
            'text' => 'CSS Variables',
            'URL' => '/admin/page/?class=CBCSSVariablesHelp'
        ];

        CBDB::transaction(function () use ($spec) {
            CBModels::save($spec);
        });
    }

    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return ['CBHelpAdminMenu'];
    }

    static function render() {
        ?>

        <div class="b">
            <?php
            CBCSSVariablesHelp::renderText();
            CBCSSVariablesHelp::renderPanel('panel1');
            ?>
        </div>
        <div class="b b2">
            <?php
                CBCSSVariablesHelp::renderText();
                CBCSSVariablesHelp::renderPanel('panel2');
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
                <?php CBCSSVariablesHelp::renderPanelText(); ?>
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
