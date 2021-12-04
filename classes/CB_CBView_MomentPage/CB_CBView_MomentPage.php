<?php

final class
CB_CBView_MomentPage {

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
                'v675.45.js',
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

        CB_CBView_MomentPage::setMomentModelCBID(
            $viewModel,
            CB_CBView_MomentPage::getMomentModelCBID(
                $viewSpec
            )
        );

        return $viewModel;
    }
    /* CBModel_build() */



    /**
     * @param object $viewModel
     *
     * @return string
     */
    static function
    CBModel_toSearchText(
        stdClass $viewModel
    ): string {
        $momentModelCBID = CB_CBView_MomentPage::getMomentModelCBID(
            $viewModel
        );

        if (
            $momentModelCBID === null
        ) {
            return '';
        }

        $momentModel = CBModelCache::fetchModelByID(
            $momentModelCBID
        );

        return CB_Moment::getText(
            $momentModel
        );
    }
    /* CBModel_toSearchText() */



    /* -- CBView interfaces -- */



    /**
     * @param object $viewModel
     *
     * @return void
     */
    static function
    CBView_render(
        stdClass $viewModel
    ): void {
        $momentModelCBID = CB_CBView_MomentPage::getMomentModelCBID(
            $viewModel
        );

        $momentModel = CBModelCache::fetchModelByID(
            $momentModelCBID
        );

        $momentModelAsJSON = json_encode(
            $momentModel
        );

        ?>

        <div
            class="CB_CBView_MomentPage"
            data-moment="<?= cbhtml($momentModelAsJSON) ?>"
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
    getMomentModelCBID(
        stdClass $viewModel
    ): ?string {
        return CBModel::valueAsCBID(
            $viewModel,
            'CB_CBView_MomentPage_momentModelCBID_property'
        );
    }
    /* getMomentModelCBID() */



    /**
     * @param object $viewModel
     * @param string $newMomentModelCBID
     *
     * @return void
     */
    static function
    setMomentModelCBID(
        stdClass $viewModel,
        string $newMomentModelCBID
    ): void {
        if (
            !CBID::valueIsCBID(
                $newMomentModelCBID
            )
        ) {
            throw new InvalidArgumentException(
                'newMomentModelCBID'
            );
        }

        $viewModel->CB_CBView_MomentPage_momentModelCBID_property = (
            $newMomentModelCBID
        );
    }
    /* setMomentModelCBID() */

}
