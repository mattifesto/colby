<?php

final class
CB_StandardPageFrame {

    /* -- CBInstall interfaces -- */



    /**
     * @return void
     */
    static function
    CBInstall_install(
    ): void {
        CBPageFrameCatalog::install(
            __CLASS__
        );
    }
    /* CBInstall_install() */



    /**
     * @return [string]
     */
    static function
    CBInstall_requiredClassNames(
    ): array {
        return [
            'CBPageFrameCatalog'
        ];
    }
    /* CBInstall_requiredClassNames() */



    /* -- CBHTMLOutput interfaces -- */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_CSSURLs(
    ) {
        return [
            Colby::flexpath(
                __CLASS__,
                'v675.37.css',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_CSSURLs() */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs(
    ) {
        return [
            Colby::flexpath(
                __CLASS__,
                'v675.37.js',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_CSSURLs() */



    /**
     * @return [string]
     */
    static function
    CBInstall_requiredClassNames(
    ): array {
        return [
            'CB_UI',
        ];
    }
    /* CBInstall_requiredClassNames() */



    /* -- CBPageFrame interfaces -- */



    /**
     * @param callable $renderContent
     *
     * @return void
     */
    static function
    CBPageFrame_render(
        callable $renderContent
    ): void {
        echo <<<EOT

            <div class="CB_StandardPageFrame">

                <div class="CB_StandardPageFrame_leftSidebar">
                </div>

                <div class="CB_StandardPageFrame_main">

        EOT;

        CBView::renderSpec(
            (object)[
                'className' => 'CB_CBView_MainHeader',
            ]
        );

        $renderContent();

        echo <<<EOT

                </div>

                <div class="CB_StandardPageFrame_rightSidebar">
                </div>

            </div>

        EOT;
    }
    /* CBPageFrame_render() */

}
