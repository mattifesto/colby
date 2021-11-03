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

        CBModelAssociations::delete(
            CB_StandardPageFrame::getCBID(),
            'CB_StandardPageFrame_defaultMainMenu'
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
            'CBModelAssociations',
            'CBPageFrameCatalog',
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
                'v675.38.css',
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
    CBHTMLOutput_requiredClassNames(
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
        $mainMenuModelCBID = CBModelAssociations::fetchSingularSecondCBID(
            CB_StandardPageFrame::getCBID(),
            'CB_StandardPageFrame_defaultMainMenu'
        );

        echo <<<EOT

            <div class="CB_StandardPageFrame">

                <div class="CB_StandardPageFrame_leftSidebar">
        EOT;

        if ($mainMenuModelCBID !== null) {
            $menuViewSpec = CBModel::createSpec(
                'CBMenuView'
            );

            CBMenuView::setCSSClassNames(
                $menuViewSpec,
                'custom CB_StandardPageFrame_mainMenu'
            );

            CBMenuView::setMenuModelCBID(
                $menuViewSpec,
                $mainMenuModelCBID
            );

            CBView::renderSpec(
                $menuViewSpec
            );
        }

        echo <<<EOT

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



    /* -- functions -- */



    /**
     * @return CBID
     *
     *      This function returns the CBID that represents the standard page
     *      frame in the associations table when something is associated with
     *      it.
     */
    static function
    getCBID(
    ): string {
        return '59bd18277734acc3ac0d3ed7963ff2f128989cfc';
    }
    /* getCBID() */



    /**
     * @param CBID @newMainMenuModelCBID
     *
     *      This menu will be shown as the default main menu when the standard
     *      page frame is used.
     *
     * @return void
     */
    static function
    setDefaultMainMenuModelCBID(
        string $newMainMenuModelCBID
    ): void {
        CBModelAssociations::replaceAssociatedID(
            CB_StandardPageFrame::getCBID(),
            'CB_StandardPageFrame_defaultMainMenu',
            $newMainMenuModelCBID
        );
    }
    /* setDefaultMainMenuModelCBID() */

}
