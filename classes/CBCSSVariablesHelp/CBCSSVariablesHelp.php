<?php

final class CBCSSVariablesHelp {

    /**
     * @return string
     */
    static function CBAdmin_group() {
        return 'Developers';
    }

    /**
     * @return [string]
     */
    static function CBAdmin_menuNamePath(): array {
        return ['help', 'cssvariables'];
    }

    /**
     * @return void
     */
    static function CBAdmin_render(): void {
        CBHTMLOutput::setTitleHTML('CSS Variables');

        CBView::renderSpec((object)[
            'className' => 'CBMessageView',
            'CSSClassNames' => 'CBLightTheme CBCSSVariablesHelp_test',
            'markup' => CBCSSVariablesHelp::markup('CBLightTheme'),
        ]);
        CBView::renderSpec((object)[
            'className' => 'CBMessageView',
            'CSSClassNames' => 'CBDarkTheme CBCSSVariablesHelp_test',
            'markup' => CBCSSVariablesHelp::markup('CBDarkTheme'),
        ]);
        CBView::renderSpec((object)[
            'className' => 'CBMessageView',
            'CSSClassNames' => 'CBAdminTheme CBCSSVariablesHelp_test',
            'markup' => CBCSSVariablesHelp::markup('CBAdminTheme'),
        ]);
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
            'URL' => '/admin/?c=CBCSSVariablesHelp'
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

    /**
     * @return string
     */
    static function markup($title): string {
        return <<<EOT

            --- h1
            {$title}
            ---
            --- description
            Easy themes using CSS variables.
            ---

            --- line
            --- inner
            ---
            ---

            The primary use of the a theme is presenting (CBTextColor (i)) text
            on top of a (CBBackgroundColor (i)) background.

            (CBTextColor2 (i)) will render lighter text like the description
            above.

            (CBLineColor (i)) will render a light line like the line above.

EOT;
    }
}
