<?php

/**
 * This view shows the most recent public moments for all users. It's meant to
 * be used on the home page.
 */
final class
CBRecentMomentsView
{
    // -- CBHTMLOutput interfaces



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_CSSURLs(): array
    {
        $arrayOfCSSURLs =
            [
                CBLibrary::buildLibraryClassFilePath(
                    __CLASS__,
                    '2024_02_03_0749',
                    'css',
                    cbsysurl()
                ),
            ];

        return $arrayOfCSSURLs;
    }
    // CBHTMLOutput_CSSURLs()



    /* -- CBInstall interfaces -- -- -- -- -- */



    /**
     * @return void
     */
    static function
    CBInstall_install(): void
    {
        CBViewCatalog::installView(
            __CLASS__
        );
    }
    /* CBInstall_install() */



    /**
     * @return [string]
     */
    static function
    CBInstall_requiredClassNames(): array
    {
        $requiredClasNames =
            [
                'CBViewCatalog',
            ];

        return $requiredClasNames;
    }
    /* CBInstall_requiredClassNames() */



    /* -- CBModel interfaces -- */



    /**
     * @param object $viewSpec
     *
     * @return object
     */
    static function
    CBModel_build(
        stdClass $viewSpec
    ): stdClass {
        $viewModel = (object)[];

        return $viewModel;
    }
    /* CBModel_build() */



    /**
     * @param object $viewModel
     */
    static function
    CBView_render(
        stdClass $viewModel
    ): void {
        $arrayOfMomentModelCBIDs =
            CB_Moment::fetchMostRecentMomentModelCBIDs(
                20
            );

        if (count($arrayOfMomentModelCBIDs) === 0) {
            return;
        }

        $arrayOfMomentModels =
            CBModels::fetchModelsByID2(
                $arrayOfMomentModelCBIDs,
                true
            );

        echo '<div class="CBRecentMomentsView">';

        foreach ($arrayOfMomentModels as $momentModel) {
            $moment2ViewSpec =
                CBModel::createSpec(
                    'CB_View_Moment2'
                );

            CB_View_Moment2::setMomentModelCBID(
                $moment2ViewSpec,
                CBModel::getCBID(
                    $momentModel
                )
            );

            CBView::renderSpec(
                $moment2ViewSpec
            );
        }

        echo '</div>';
    }
    // CBView_render()
}
