<?php

final class
CB_StandardPageFrame {

    /* -- CBAjax interfaces -- */



    /**
     * @param object $args
     *
     *      {
     *          pageModelCBID: CBID
     *      }
     *
     * @return void
     */
    static function
    CBAjax_setRightSidebarPageModelCBID(
        stdClass $args
    ): void {
        $pageModelCBID = CBModel::valueAsCBID(
            $args,
            'pageModelCBID'
        );

        if (
            $pageModelCBID === null
        ) {
            // TODO throw exception
        }

        CB_StandardPageFrame::setRightSidebarPageModelCBID(
            $pageModelCBID
        );
    }
    /* CBAjax_setRightSidebarPageModelCBID() */



    /**
     * @return string
     */
    static function
    CBAjax_setRightSidebarPageModelCBID_getUserGroupClassName(
    ): string {
        return 'CBAdministratorsUserGroup';
    }
    /* CBAjax_setRightSidebarPageModelCBID_getUserGroupClassName() */



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
                'v675.39.css',
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
                'v675.39.js',
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
            'Colby',
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

        EOT;

        $mainMenuModelCBID = (
            CB_StandardPageFrame::getDefaultMainMenuModelCBID()
        );

        if (
            $mainMenuModelCBID !== null
        ) {
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

        $mainHeaderViewSpec = CBModel::createSpec(
            'CB_CBView_MainHeader'
        );

        CB_CBView_MainHeader::setContext(
            $mainHeaderViewSpec,
            'page'
        );

        CBView::renderSpec(
            $mainHeaderViewSpec
        );

        $renderContent();

        echo <<<EOT

                </div>
                <div class="CB_StandardPageFrame_rightSidebar">

        EOT;

        $rightSidebarPageModelCBID = (
            CB_StandardPageFrame::getRightSidebarPageModelCBID()
        );

        if (
            $rightSidebarPageModelCBID !== null
        ) {
            $viewPageModel = CBModelCache::fetchModelByID(
                $rightSidebarPageModelCBID
            );

            if (
                $viewPageModel !== null
            ) {
                $views = CBViewPage::getViews(
                    $viewPageModel
                );

                array_walk(
                    $views,
                    function (
                        $view
                    ) {
                        CBView::render(
                            $view
                        );
                    }
                );
            }
        }

        echo <<<EOT

                </div>
            </div>

        EOT;

        /* -- TODO move this into another functon -- */


        echo <<<EOT


            <div class="CB_StandardPageFrame CB_StandardPageFrame_mainMenuPopup CBUI_panel">
                <div class="CB_StandardPageFrame_leftSidebar">
                </div>
                <div class="CB_StandardPageFrame_main">

        EOT;

        $mainHeaderViewSpec = CBModel::createSpec(
            'CB_CBView_MainHeader'
        );

        CB_CBView_MainHeader::setContext(
            $mainHeaderViewSpec,
            'menu'
        );

        CBView::renderSpec(
            $mainHeaderViewSpec
        );

        if (
            $mainMenuModelCBID !== null
        ) {
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
     * @return CBID|null
     */
    static function
    getDefaultMainMenuModelCBID(
    ): ?string {
        return CBModelAssociations::fetchSingularSecondCBID(
            CB_StandardPageFrame::getCBID(),
            'CB_StandardPageFrame_defaultMainMenu'
        );
    }
    /* getDefaultMainMenuModelCBID() */



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



    /**
     * @return CBID|null
     */
    static function
    getRightSidebarPageModelCBID(
    ): ?string {
        return CBModelAssociations::fetchSingularSecondCBID(
            CB_StandardPageFrame::getCBID(),
            'CB_StandardPageFrame_rightSidebarPage'
        );
    }
    /* getRightSidebarPageModelCBID() */



    /**
     * @param CBID $newPageModelCBID
     *
     * @return void
     */
    static function
    setRightSidebarPageModelCBID(
        string $newPageModelCBID
    ): void {
        CBModelAssociations::replaceAssociatedID(
            CB_StandardPageFrame::getCBID(),
            'CB_StandardPageFrame_rightSidebarPage',
            $newPageModelCBID
        );
    }
    /* setRightSidebarPageModelCBID() */

}
