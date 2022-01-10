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
     *  @return object
     *
     *      {
     *          CB_Moment_create_momentModel: object|null
     *
     *              The moment model that was created.
     *
     *          CB_Moment_create_userErrorMessage: string|null
     *
     *              If something went wrong that the user can fix this property
     *              will be set to a user appropriate error message.
     *      }
     */
    static function
    CBAjax_create(
        stdClass $args
    ): ?stdClass {
        $response = (object)[
            'CB_Moment_create_momentModel' => null,
            'CB_Moment_create_userErrorMessage' => null,
        ];

        $currentUserModelCBID = ColbyUser::getCurrentUserCBID();

        if (
            $currentUserModelCBID === null
        ) {
            $response->CB_Moment_create_userErrorMessage = (
                CBConvert::stringToCleanLine(<<<EOT

                    You are not currently logged in.

                EOT)
            );

            return $response;
        }

        $text = trim(
            CBModel::valueToString(
                $args,
                'CB_Moment_create_text'
            )
        );

        if (
            $text === ''
        ) {
            $response->CB_Moment_create_userErrorMessage = (
                CBConvert::stringToCleanLine(<<<EOT

                    You have provided no text for your moment.

                EOT)
            );

            return $response;
        }

        $momentModelCBID = CBID::generateRandomCBID();

        $momentSpec = CBModel::createSpec(
            'CB_Moment',
            $momentModelCBID
        );

        CB_Moment::setAuthorUserModelCBID(
            $momentSpec,
            $currentUserModelCBID
        );

        $reservedAttostampModel = CB_Attostamp::reserveNow(
            $momentModelCBID
        );

        CB_Moment::setAttostamp(
            $momentSpec,
            $reservedAttostampModel
        );

        CB_Moment::setText(
            $momentSpec,
            $text
        );

        CBDB::transaction(
            function (
            ) use (
                $momentSpec
            ) {
                CBModels::save(
                    $momentSpec
                );
            }
        );

        $response->CB_Moment_create_momentModel = (
            CBModels::fetchModelByCBID(
                CBModel::getCBID(
                    $momentSpec
                )
            )
        );

        return $response;
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
     *          maxModelsCount: int
     *          userModelCBID: CBID
     *      }
     *
     * @return [<CB_Moment model>]
     */
    static function
    CBAjax_fetchMomentsForUserModelCBID(
        stdClass $args
    ): array {
        $userModelCBID = CBModel::valueAsCBID(
            $args,
            'userModelCBID'
        );

        $maxModelsCount = CBModel::valueAsInt(
            $args,
            'maxModelsCount'
        );

        if (
            $maxModelsCount === null ||
            $maxModelsCount < 1 ||
            $maxModelsCount > 50
        ) {
            $maxModelsCount = 10;
        }

        $modelAssociations = (
            CBModelAssociations::fetchModelAssociationsByFirstCBIDAndAssociationKey(
                $userModelCBID,
                'CB_Moment_userMoments',
                'descending',
                $maxModelsCount
            )
        );

        $momentModels = CBModelAssociations::fetchSecondModels(
            $modelAssociations,
            true /* maintainPositions */
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
                'v675.48.js',
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

        /**
         * @NOTE 2022_01_02
         *
         *      This is special logic used during the transition from a Unix
         *      timestamp to an atto timestamp.
         */

        $attostampModel = CB_Moment::getAttostamp(
            $momentSpec,
        );

        if (
            $attostampModel !== null
        ) {
            CB_Moment::setAttostamp(
                $momentModel,
                $attostampModel
            );
        } else {
            CB_Moment::setCreatedTimestamp(
                $momentModel,
                CB_Moment::getCreatedTimestamp(
                    $momentSpec
                )
            );
        }

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
     * @return [<CB_Attostamp>]
     */
    static function
    CBModel_getAttostamps(
        stdClass $momentModel
    ): array {
        $attostampModel = CB_Moment::getAttostamp(
            $momentModel
        );

        if (
            $attostampModel === null
        ) {
            return [];
        } else {
            return [
                $attostampModel,
            ];
        }
    }
    /* CBModel_getAttostamps() */



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
     * @param object $momentSpec
     *
     * @return object
     */
    static function
    CBModel_upgrade(
        stdClass $momentSpec
    ): stdClass {
        $rootModelCBID = CBModel::getCBID(
            CBModel::getRootSpecCurrentlyBeingUpgraded()
        );

        /**
         * Attostamps can only be reserved if a root model CBID exists. If the
         * root model doesn't have a CBID then an uprade won't work. It's rare
         * for upgrades to occur on models that don't have a root model CBID.
         */

        if (
            $rootModelCBID === null
        ) {
            throw new CBExceptionWithValue(
                CBConvert::stringToCleanLine(<<<EOT

                    A CB_Moment spec can only be upgraded if it is or has a root
                    spec with a CBID.

                EOT),
                $momentSpec,
                '0a851becdee9fee6b5a175881c21ec24400468c3'
            );
        }

        $attostampModel = CB_Moment::getAttostamp(
            $momentSpec
        );

        if (
            $attostampModel === null
        ) {
            $createdTimestamp = CB_Moment::getCreatedTimestamp(
                $momentSpec
            );

            /**
             * If there's not an attostamp, the created timestamp should never
             * be null  but if it is, we can't do much.
             */

            if (
                $createdTimestamp !== null
            ) {
                $attostampSpec = CB_Attostamp::from(
                    $createdTimestamp
                );

                $attostampSpec = CB_Attostamp::reserveNear(
                    $attostampSpec,
                    $rootModelCBID
                );

                CB_Moment::setAttostamp(
                    $momentSpec,
                    $attostampSpec
                );

                unset(
                    $momentSpec->CB_Moment_createdTimestamp
                );
            }
        }

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



    /* -- CBModels interfaces -- */



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

            $attostampModel = CB_Moment::getAttostamp(
                $momentModel
            );

            CB_ModelAssociation::setSortingValue(
                $modelAssociation,
                CB_Attostamp::getUnixTimestamp(
                    $attostampModel
                )
            );

            CB_ModelAssociation::setSortingValueDifferentiator(
                $modelAssociation,
                CB_Attostamp::getAttoseconds(
                    $attostampModel
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
     * @return object
     */
    static function
    getAttostamp(
        stdClass $momentModel
    ): ?stdClass {
        return CBModel::valueAsModel(
            $momentModel,
            'CB_Moment_attostamp_property',
            'CB_Attostamp'
        );
    }
    /* getAttostamp() */



    /**
     * @param object $momentModel
     * @param object $attostampModel
     *
     * @return void
     */
    static function
    setAttostamp(
        stdClass $momentModel,
        stdClass $attostampModel
    ): void {
        $verifiedAttostampModel = CBConvert::valueAsModel(
            $attostampModel,
            'CB_Attostamp'
        );

        $momentModel->CB_Moment_attostamp_property = $verifiedAttostampModel;
    }
    /* setAttostamp() */



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
     * @deprecated 2022_01_09
     *
     *      This property will always be lower priority than the attostamp
     *      property.
     *
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
     * @deprecated 2022_01_09
     *
     *      This property will always be lower priority than the attostamp
     *      property.
     *
     * @param object $momentModel
     * @param int $createdTimestamp
     *
     * @return void
     */
    static function
    setCreatedTimestamp(
        stdClass $momentModel,
        int $createdTimestamp
    ): void {
        $momentModel->CB_Moment_createdTimestamp = $createdTimestamp;
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
