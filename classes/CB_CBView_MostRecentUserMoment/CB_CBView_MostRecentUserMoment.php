<?php

final class
CB_CBView_MostRecentUserMoment {

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
                'v675.45.css',
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
    ): array {
        return [
            Colby::flexpath(
                __CLASS__,
                'v675.47.js',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_requiredClassNames(
    ): array {
        return [
            'CB_CBView_Moment',
            'CBAjax',
            'CBConvert',
            'CBErrorHandler',
            'Colby',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */



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
    ): void {
        $userModelCBID = cbhtml(
            CB_CBView_MostRecentUserMoment::getUserModelCBID(
                $viewModel
            )
        );

        ?>

        <div
            class="CB_CBView_MostRecentUserMoment"
            data-user-model-c-b-i-d="<?= $userModelCBID ?>"
        >
        </div>

        <?php
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
        $viewModel->CB_CBView_MostRecentUserMoment_userModelCBID = (
            $userModelCBID
        );
    }
    /* setUserModelCBID() */

}