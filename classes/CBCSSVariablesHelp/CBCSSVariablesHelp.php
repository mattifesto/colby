<?php

final class
CBCSSVariablesHelp
{
    /**
     * @return string
     */
    static function CBAdmin_getUserGroupClassName() {
        return 'CBDevelopersUserGroup';
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
        CBHTMLOutput::pageInformation()->title = 'CSS Variables Help';

        CBView::renderSpec((object)[
            'className' => 'CBMessageView',
            'CSSClassNames' => 'CBLightTheme CBCSSVariablesHelp_test',
            'markup' => CBCSSVariablesHelp::markup(),
        ]);
        CBView::renderSpec((object)[
            'className' => 'CBMessageView',
            'CSSClassNames' => 'CBDarkTheme',
            'markup' => CBCSSVariablesHelp::sample(),
        ]);
        CBView::renderSpec((object)[
            'className' => 'CBMessageView',
            'CSSClassNames' => 'CBLightTheme',
            'markup' => CBCSSVariablesHelp::sample(),
        ]);
        CBView::renderSpec((object)[
            'className' => 'CBMessageView',
            'CSSClassNames' => 'CBDarkTheme navy',
            'markup' => CBCSSVariablesHelp::sample(),
        ]);
        CBView::renderSpec((object)[
            'className' => 'CBMessageView',
            'CSSClassNames' => 'CBLightTheme yellow',
            'markup' => CBCSSVariablesHelp::sample(),
        ]);
    }



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_CSSURLs(
    ) {
        $cssURLS =
        [
            Colby::flexpath(
                __CLASS__,
                '2022_06_21_1655784726.css',
                cbsysurl()
            )
        ];

        return $cssURLS;
    }
    // CBHTMLOutput_CSSURLs()



    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $spec = CBModels::fetchSpecByID(CBHelpAdminMenu::ID());

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



    // -- functions



    /**
     * @return string
     */
    static function
    markup(
    ): string
    {
        $cbmessage =
        <<<EOT

            --- h1
            Colby CSS Variables
            ---
            --- description
            Simple theming using CSS variables.
            ---

            --- line
            --- inner
            ---
            ---

            --- dl
                --- dt
                CBBackgroundColor1
                ---

                This color represents the standard background color for the
                theme. The themes are created with the understanding that they
                may be used with custom background colors that will be
                considered either light or dark.

                For CBLightTheme CBBackgroundColor1 is white. A white background
                is very comfortable and expected for users.

                For CBDarkTheme it is hsl(0, 0%, 10%) because pure black feels
                too strong for comfortable reading. This value has evolved over
                the life of CBDarkTheme because while it's pretty easy to say
                that black is not right color. The correct color is subject to
                individual situations and emotions of the designer. Shades of
                dark black look visibly different even in slightly different
                layouts.

                --- dt
                CBTextColor1
                ---

                CBTextColor1 is meant to be the color that looks best for the
                main text color. For CBLightTheme it is black with and opacity
                of 0.9. For CBDarkTheme is is white with an opacity of 0.9.

                Pure white or black text is so strong that it can feel awkward.
                Using opacity to mellow the color allows the color to work with
                elements that have a custom background color specified.

                --- dt
                CBTextColor2
                ---

                CBTextColor2 is meant to be used for text that has a reduced
                emphasis compared to other text it is near. It is often used for
                descriptions adjecent to titles, for dates or captions.

                The use of CBTextColor2 is left mostly to the designer, because
                different layouts and views will dictate whether it is
                appropriate or not.

                --- dt
                CBTextColor3
                ---

                CBTextColor3 is the lightest text color and also appripriate for
                drawing lines.

            ---

        EOT;

        return $cbmessage;
    }
    // markup()



    static function sample() {
        $cbmessage =
        <<<EOT

            --- h1
            Welcome!
            ---

            This is a sample to show you what content will look like with
            various themes. By look at the style sheets associated with this
            page you can also see how to create your own customized version of
            the themes.

            This is useful for:

            --- ul
            designers

            advanced content creators

            artistic developers
            ---
        EOT;

        return $cbmessage;
    }
    // sample()

}
