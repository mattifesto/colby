<?php

final class
CB_StandardPageFrame
{
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



    // -- CBHTMLOutput interfaces



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_CSSURLs(
    ): array
    {
        $arrayOfCSSURLs =
        [
            CBLibrary::buildLibraryClassFilePath(
                __CLASS__,
                '2022_10_08_1665256555',
                'css',
                cbsysurl()
            ),
        ];

        return $arrayOfCSSURLs;
    }
    // CBHTMLOutput_CSSURLs()



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs_Immediate(
    ) {
        return [
            Colby::flexpath(
                __CLASS__,
                'v675.55.js',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs_Immediate() */



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
        $rootElementCSSClassNames = implode(
            ' ',
            [
                'CB_StandardPageFrame_element',
            ]
        );

        echo <<<EOT

            <div class="${rootElementCSSClassNames}">

        EOT;

        CB_StandardPageFrame::renderLeftSidebar();

        CB_StandardPageFrame::renderMain(
            $renderContent
        );

        CB_StandardPageFrame::renderRightSidebar();

        echo <<<EOT

            </div> <!-- ${rootElementCSSClassNames} -->

        EOT;

        CB_StandardPageFrame::renderMenuPanel();
    }
    /* CBPageFrame_render() */



    /* -- accessors -- */



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
    getLeftSidebarPageModelCBID(
    ): ?string {
        return CBModelAssociations::fetchSingularSecondCBID(
            CB_StandardPageFrame::getCBID(),
            'CB_StandardPageFrame_leftSidebarPage'
        );
    }
    /* getLeftSidebarPageModelCBID() */



    /**
     * @param CBID $newPageModelCBID
     *
     * @return void
     */
    static function
    setLeftSidebarPageModelCBID(
        string $newPageModelCBID
    ): void {
        CBModelAssociations::replaceAssociatedID(
            CB_StandardPageFrame::getCBID(),
            'CB_StandardPageFrame_leftSidebarPage',
            $newPageModelCBID
        );
    }
    /* setLeftSidebarPageModelCBID() */



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



    // -- functions



    /**
     * @return void
     */
    private static function
    renderLeftSidebar(
    ): void {
        $sidebarElementCSSClasses = implode(
            ' ',
            [
                'CB_StandardPageFrame_leftSidebar_class',
                'CB_StandardPageFrame_page_leftSidebar_element',
            ]
        );

        $sidebarContentElementCSSClasses = implode(
            ' ',
            [
                'CB_StandardPageFrame_leftSidebarContent_element',
            ]
        );

        echo <<<EOT

            <div class="${sidebarElementCSSClasses}">
                <div class="${sidebarContentElementCSSClasses}">

        EOT;

        $leftSidebarPageModelCBID = (
            CB_StandardPageFrame::getLeftSidebarPageModelCBID()
        );

        if (
            $leftSidebarPageModelCBID !== null
        ) {
            $viewPageModel = CBModelCache::fetchModelByID(
                $leftSidebarPageModelCBID
            );

            if (
                $viewPageModel !== null
            ) {
                $viewModels = CBViewPage::getViews(
                    $viewPageModel
                );

                foreach(
                    $viewModels as $viewModel
                ) {
                    CBView::render(
                        $viewModel
                    );
                }
            }
        }

        echo <<<EOT

                </div> <!-- ${sidebarContentElementCSSClasses} -->
            </div> <!-- ${sidebarElementCSSClasses} -->

        EOT;
    }
    /* renderLeftSidebar() */



    /**
     * @param callable $renderContent
     *
     * @return void
     */
    private static function
    renderMain(
        callable $renderContent
    ): void {
        $mainElementCSSClasses = implode(
            ' ',
            [
                'CB_StandardPageFrame_main_element',
            ]
        );

        echo <<<EOT

            <div class="${mainElementCSSClasses}">

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

            </div> <!-- ${mainElementCSSClasses} -->

        EOT;
    }
    /* renderMain() */



    /**
     * @return void
     */
    private static function
    renderMenuPanel(
    ): void {
        echo <<<EOT


            <div class="CB_StandardPageFrame_element CB_StandardPageFrame_menuPanel_element">
                <div class="CB_StandardPageFrame_leftSidebar_class">
                </div>
                <div class="CB_StandardPageFrame_main_element CB_StandardPageFrame_menuPopup_main_element">

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

        echo <<<EOT

                </div>
                <div class="CB_StandardPageFrame_rightSidebar_element">
                </div>
            </div>

        EOT;
    }
    /* renderMenuPanel() */



    /**
     * @return void
     */
    private static function
    renderRightSidebar(
    ): void {
        $sidebarElementCSSClassNames = implode(
            ' ',
            [
                'CB_StandardPageFrame_rightSidebar_element',
                'CB_StandardPageFrame_page_rightSidebar_element',
            ]
        );

        $sidebarContentElementCSSClassNames = implode(
            ' ',
            [
                'CB_StandardPageFrame_rightSidebarContent_element'
            ]
        );

        echo <<<EOT

            <div class="${sidebarElementCSSClassNames}">
                <div class="${sidebarContentElementCSSClassNames}">

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
                $viewModels = CBViewPage::getViews(
                    $viewPageModel
                );

                foreach(
                    $viewModels as $viewModel
                ) {
                    CBView::render(
                        $viewModel
                    );
                }
            }
        }

        echo <<<EOT

                </div> <!-- ${sidebarContentElementCSSClassNames} -->
            </div> <!-- ${sidebarElementCSSClassNames} -->

        EOT;
    }
    /* renderRightSidebar() */

}
