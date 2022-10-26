<?php

final class
CB_CBView_MostRecentUserMoment
{
    // -- CBHTMLOutput interfaces



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_CSSURLs(
    ): array
    {
        return
        [
            Colby::flexpath(
                __CLASS__,
                'v675.61.4.css',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_CSSURLs() */



    /* -- CBInstall interfaces -- -- -- -- -- */



    /**
     * @return void
     */
    static function
    CBInstall_install(
    ): void {
        CBViewCatalog::installView(
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
            'CBViewCatalog',
        ];
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

        CB_CBView_MostRecentUserMoment::setUserModelCBID(
            $viewModel,
            CB_CBView_MostRecentUserMoment::getUserModelCBID(
                $viewSpec
            )
        );

        return $viewModel;
    }
    /* CBModel_build() */



    /**
     * @param object $viewModel
     */
    static function
    CBView_render(
        stdClass $viewModel
    ): void
    {
        $userModelCBID =
        CB_CBView_MostRecentUserMoment::getUserModelCBID(
            $viewModel
        );

        if (
            $userModelCBID === null
        ) {
            return;
        }

        $arrayOfMomentModels =
        CB_Moment::fetchMomentsForUserModelCBID(
            $userModelCBID,
            1
        );

        if (
            count($arrayOfMomentModels) === 0
        ) {
            return;
        }

        echo
        '<div class="CB_CBView_MostRecentUserMoment">';

        $moment2ViewSpec =
        CBModel::createSpec(
            'CB_View_Moment2'
        );

        CB_View_Moment2::setMomentModelCBID(
            $moment2ViewSpec,
            CBModel::getCBID(
                $arrayOfMomentModels[0]
            )
        );

        CBView::renderSpec(
            $moment2ViewSpec
        );

        echo
        '</div>';
    }
    /* CBView_render() */



    /* -- accessors -- */



    /**
     * @param object $viewModel
     *
     * @return CBID|null
     */
    static function
    getUserModelCBID(
        stdClass $viewModel
    ): ?string {
        return CBModel::valueAsCBID(
            $viewModel,
            'CB_CBView_MostRecentUserMoment_userModelCBID'
        );
    }
    /* getUserModelCBID() */



    /**
     * @param object $viewModel
     * @param CBID|null $userModelCBID
     *
     * @return void
     */
    static function
    setUserModelCBID(
        stdClass $viewModel,
        ?string $userModelCBID
    ): void {
        $viewModel->CB_CBView_MostRecentUserMoment_userModelCBID =
        $userModelCBID;
    }
    /* setUserModelCBID() */

}
