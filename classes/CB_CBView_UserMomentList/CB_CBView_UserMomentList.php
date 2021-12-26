<?php

final class
CB_CBView_UserMomentList {

    /* -- CBHTMLOutput interfaces -- */



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
            'CB_CBView_MomentCreator',
            'CBAjax',
            'CBConvert',
            'CBErrorHandler',
            'CBUser',
            'Colby',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */



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

        CB_CBView_UserMomentList::setShowMomentCreator(
            $viewModel,
            CB_CBView_UserMomentList::getShowMomentCreator(
                $viewSpec
            )
        );

        CB_CBView_UserMomentList::setUserModelCBID(
            $viewModel,
            CB_CBView_UserMomentList::getUserModelCBID(
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
            CB_CBView_UserMomentList::getUserModelCBID(
                $viewModel
            )
        );

        $showMomentCreator = CB_CBView_UserMomentList::getShowMomentCreator(
            $viewModel
        );

        ?>

        <div
            class="CB_CBView_UserMomentList"
            data-user-model-c-b-i-d="<?= $userModelCBID ?>"
            data-show-moment-creator="<?= json_encode($showMomentCreator) ?>"
        >
        </div>

        <?php
    }
    /* CBView_render() */



    /* -- accessors -- */



    /**
     * @param object $viewModel
     *
     * @return bool
     */
    static function
    getShowMomentCreator(
        stdClass $viewModel
    ): bool {
        return CBModel::valueToBool(
            $viewModel,
            'CB_CBView_UserMomentList_showMomentCreator'
        );
    }
    /* getShowMomentCreator() */



    /**
     * @param object $viewModel
     * @param bool $showMomentCreator
     *
     * @return void
     */
    static function
    setShowMomentCreator(
        stdClass $viewModel,
        bool $showMomentCreator
    ): void {
        $viewModel->CB_CBView_UserMomentList_showMomentCreator = (
            $showMomentCreator
        );
    }
    /* setShowMomentCreator() */



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
            'CB_CBView_UserMomentList_userModelCBID'
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
        $viewModel->CB_CBView_UserMomentList_userModelCBID = $userModelCBID;
    }
    /* setUserModelCBID() */

}
