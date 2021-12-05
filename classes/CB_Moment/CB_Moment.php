<?php

final class
CB_Moment {

    /* -- CBAjax interfaces -- */



    /**
     * @param $args
     *
     *      {
     *          CB_CBView_Moment_text: string
     *      }
     *
     *  @return void
     */
    static function
    CBAjax_create(
        stdClass $args
    ): void {
        $text = trim(
            CBModel::valueToString(
                $args,
                'CB_Moment_create_text'
            )
        );

        if (
            $text === ''
        ) {
            return;
        }

        $momentSpec = CBModel::createSpec(
            'CB_Moment',
            CBID::generateRandomCBID()
        );

        CB_Moment::setAuthorUserModelCBID(
            $momentSpec,
            ColbyUser::getCurrentUserCBID()
        );

        CB_Moment::setCreatedTimestamp(
            $momentSpec,
            time()
        );

        CB_Moment::setText(
            $momentSpec,
            $text
        );

        CBDB::transaction(
            function ()
            use ($momentSpec) {
                CBModels::save(
                    $momentSpec
                );
            }
        );
    }
    /* CBAjax_create() */



    /**
     * @return string
     */
    static function
    CBAjax_create_getUserGroupClassName(
    ): string {
        return 'CBPublicUserGroup';
    }
    /* CBAjax_create_getUserGroupClassName() */



    /**
     * @param object $args
     *
     *      {
     *          userModelCBID: CBID
     *      }
     *
     * @return [CB_Moment]
     */
    static function
    CBAjax_fetchMomentsForUserModelCBID(
        stdClass $args
    ): array {
        $userModelCBID = CBModel::valueAsCBID(
            $args,
            'userModelCBID'
        );

        $modelAssociations = (
            CBModelAssociations::fetchModelAssociationsByFirstCBIDAndAssociationKey(
                $userModelCBID,
                'CB_Moment_userMoments',
                'descending'
            )
        );

        $momentModels = CBModelAssociations::fetchSecondModels(
            $modelAssociations
        );

        usort(
            $momentModels,
            function (
                $momentModelA,
                $momentModelB
            ) {
                $sortingValueA = CB_Moment::getCreatedTimestamp(
                    $momentModelA
                );

                $sortingValueB = CB_Moment::getCreatedTimestamp(
                    $momentModelB
                );

                if (
                    $sortingValueB > $sortingValueA
                ) {
                    return 1;
                } else if (
                    $sortingValueB < $sortingValueA
                ) {
                    return -1;
                } else {
                    return 0;
                }
            }
        );

        return $momentModels;
    }
    /* CBAjax_fetchMomentsForUserModelCBID() */



    /**
     * @return string
     */
    static function
    CBAjax_fetchMomentsForUserModelCBID_getUserGroupClassName(
    ): string {
        return 'CBPublicUserGroup';
    }
    /* CBAjax_fetchMomentsForUserModelCBID_getUserGroupClassName() */



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
            'CBModel',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */



    /* -- CBModel interfaces -- */



    /**
     * @param object $momentSpec
     *
     * @return object
     */
    static function
    CBModel_build(
        stdClass $momentSpec
    ): stdClass {
        $momentModel = (object)[];

        CB_Moment::setAuthorUserModelCBID(
            $momentModel,
            CB_Moment::getAuthorUserModelCBID(
                $momentSpec
            )
        );

        CB_Moment::setCreatedTimestamp(
            $momentModel,
            CB_Moment::getCreatedTimestamp(
                $momentSpec
            )
        );

        CB_Moment::setText(
            $momentModel,
            CB_Moment::getText(
                $momentSpec
            )
        );

        return $momentModel;
    }
    /* CBModel_build() */



    /**
     * @param object $momentModel
     *
     * @return string
     */
    static function
    CBModel_toSearchText(
        stdClass $momentModel
    ): string {
        return CB_Moment::getText(
            $momentModel
        );
    }
    /* CBModel_toSearchText() */



    /**
     * @param objecct $momentModel
     *
     * @return string
     */
    static function
    CBModel_toURLPath(
        stdClass $momentModel
    ): string {
        $CBID = CBModel::getCBID(
            $momentModel
        );

        if (
            $CBID === null
        ) {
            return '';
        }

        return "/moment/{$CBID}/";
    }
    /* CBModel_toURLPath() */



    /**
     * @param obejct $momentSpec
     *
     * @return object
     */
    static function
    CBModel_upgrade(
        stdClass $momentSpec
    ): stdClass {

        /**
         * The model version date is updated whenever changes are made that
         * will require existing model to be rebuilt.
         *
         * 2021_11_25 Implemented CBModel_toSearchText()
         * 2021_12_04 Implemented CBModel_toURLPath()
         */

        $momentSpec->CB_Moment_modelVersionDate = '2021_12_04';

        return $momentSpec;
    }
    /* CBModel_upgrade() */



    /* -- CBModel interfaces -- */



    /**
     * @param [object] $momentModels
     *
     * @return void
     */
    static function
    CBModels_willSave(
        array $momentModels
    ): void {
        $modelAssociations = [];

        foreach (
            $momentModels as $momentModel
        ) {
            $momentModelCBID = CBModel::getCBID(
                $momentModel
            );

            $modelAssociation = CBModel::createSpec(
                'CB_ModelAssociation'
            );

            CB_ModelAssociation::setFirstCBID(
                $modelAssociation,
                CB_Moment::getAuthorUserModelCBID(
                    $momentModel
                )
            );

            CB_ModelAssociation::setAssociationKey(
                $modelAssociation,
                'CB_Moment_userMoments'
            );

            CB_ModelAssociation::setSortingValue(
                $modelAssociation,
                CB_Moment::getCreatedTimestamp(
                    $momentModel
                )
            );

            CB_ModelAssociation::setSecondCBID(
                $modelAssociation,
                $momentModelCBID
            );

            array_push(
                $modelAssociations,
                $modelAssociation
            );
        }

        CBModelAssociations::insertOrUpdate(
            $modelAssociations
        );
    }
    /* CBModels_willSave() */



    /* -- CBPage interfaces -- */



    /**
     * @param object $momentModel
     *
     * @return void
     */
    static function
    CBPage_render(
        stdClass $momentModel
    ): void {
        $viewPageSpec = CBModel::createSpec(
            'CBViewPage'
        );

        CBViewPage::setFrameClassName(
            $viewPageSpec,
            'CB_StandardPageFrame'
        );

        CBViewPage::setIsPublished(
            $viewPageSpec,
            true
        );

        CBViewPage::setPageSettingsClassName(
            $viewPageSpec,
            'CB_StandardPageSettings'
        );

        CBViewPage::setTitle(
            $viewPageSpec,
            mb_substr(
                CB_Moment::getText(
                    $momentModel
                ),
                0,
                240
            )
        );

        $viewSpecs = [];

        $momentPageViewSpec = CBModel::createSpec(
            'CB_CBView_MomentPage'
        );

        CB_CBView_MomentPage::setMomentModelCBID(
            $momentPageViewSpec,
            CBModel::getCBID(
                $momentModel
            )
        );

        array_push(
            $viewSpecs,
            $momentPageViewSpec
        );

        CBViewPage::setViews(
            $viewPageSpec,
            $viewSpecs
        );

        CBPage::renderSpec(
            $viewPageSpec
        );
    }
    /* CBPage_render() */



    /* -- accessors -- */



    /**
     * @param object $momentModel
     *
     * @return CBID|null
     */
    static function
    getAuthorUserModelCBID(
        stdClass $momentModel
    ): ?string {
        return CBModel::valueAsCBID(
            $momentModel,
            'CB_Moment_authorUserModelCBID'
        );
    }
    /* getAuthorUserModelCBID() */



    /**
     * @param object $momentModel
     * @param CBID|null $authorUserModelCBID
     *
     * @return void
     */
    static function
    setAuthorUserModelCBID(
        stdClass $momentModel,
        ?string $authorUserModelCBID
    ): void {
        if (
            $authorUserModelCBID !== null &&
            !CBID::valueIsCBID($authorUserModelCBID)
        ) {
            throw new InvalidArgumentException(
                'authorUserModelCBID'
            );
        }

        $momentModel->CB_Moment_authorUserModelCBID = $authorUserModelCBID;
    }
    /* setAuthorUserModelCBID() */



    /**
     * @param object $momentModel
     *
     * @return int|null
     */
    static function
    getCreatedTimestamp(
        stdClass $momentModel
    ): ?int {
        return CBModel::valueAsInt(
            $momentModel,
            'CB_Moment_createdTimestamp'
        );
    }
    /* getCreatedTimestamp() */



    /**
     * @param object $momentModel
     * @param CBID|null $createdTimestamp
     *
     * @return void
     */
    static function
    setCreatedTimestamp(
        stdClass $momentModel,
        /* mixed */ $createdTimestamp
    ): void {
        $momentModel->CB_Moment_createdTimestamp = CBConvert::valueAsInt(
            $createdTimestamp
        );
    }
    /* setCreatedTimestamp() */



    /**
     * @param object $momentModel
     *
     * @return string
     */
    static function
    getText(
        stdClass $momentModel
    ): string {
        return CBModel::valueToString(
            $momentModel,
            'CB_Moment_text'
        );
    }
    /* getText() */



    /**
     * @param object $momentModel
     * @param CBID|null $authorUserModelCBID
     *
     * @return void
     */
    static function
    setText(
        stdClass $momentModel,
        string $text
    ): void {
        $momentModel->CB_Moment_text = $text;
    }
    /* setText() */

}