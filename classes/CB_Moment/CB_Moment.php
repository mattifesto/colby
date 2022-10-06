<?php

final class
CB_Moment
{
    // -- CBAjax interfaces



    /**
     * @param $args
     *
     *      {
     *          CB_Moment_create_text_parameter: string
     *          CB_Moment_create_imageAlternativeText_parameter: string
     *          CB_Moment_create_imageModel_parameter: object
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

        $currentUserModel = CBModelCache::fetchModelByID(
            $currentUserModelCBID
        );

        $publicProfileIsEnabled = CBUser::getPublicProfileIsEnabled(
            $currentUserModel
        );

        if (
            $publicProfileIsEnabled !== true
        ) {
            $response->CB_Moment_create_userErrorMessage = (
                CBConvert::stringToCleanLine(<<<EOT

                    You are not allowed to create public content.

                EOT)
            );

            return $response;
        }


        // moment

        $momentModelCBID = CBID::generateRandomCBID();

        $momentSpec = CBModel::createSpec(
            'CB_Moment',
            $momentModelCBID
        );

        CB_Moment::setAuthorUserModelCBID(
            $momentSpec,
            $currentUserModelCBID
        );

        $reservedCBTimestampModel = CB_Timestamp::reserveNow(
            $momentModelCBID
        );

        CB_Moment::setCBTimestamp(
            $momentSpec,
            $reservedCBTimestampModel
        );



        // arguments

        $textArgument =
        CB_Ajax_Moment_Create::getTextArgument(
            $args
        );

        $verifiedImageSpecArgument =
        CB_Ajax_Moment_Create::getVerifiedImageSpecArgument(
            $args
        );

        $imageAlternativeTextArgument =
        '';

        if (
            $verifiedImageSpecArgument !==
            null
        ) {
            $imageAlternativeTextArgument =
            CB_Ajax_Moment_Create::getImageAlternativeTextArgument(
                $args
            );
        }



        if (
            $verifiedImageSpecArgument ===
            null &&
            $textArgument ===
            ''
        ) {
            $response->CB_Moment_create_userErrorMessage =
            CBConvert::stringToCleanLine(<<<EOT

                You have provided no text for your moment.

            EOT);

            return $response;
        }

        CB_Moment::setImageAlternativeText(
            $momentSpec,
            $imageAlternativeTextArgument
        );

        CB_Moment::setImage(
            $momentSpec,
            $verifiedImageSpecArgument
        );

        CB_Moment::setText(
            $momentSpec,
            $textArgument
        );



        // save

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

        $response->CB_Moment_create_momentModel =
        CBModels::fetchModelByCBID(
            CBModel::getCBID(
                $momentSpec
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
     *          maxFemtoseconds: int
     *          maxModelsCount: int
     *          maxUnixTimestamp: int
     *          userModelCBID: CBID
     *      }
     *
     * @return [<CB_Moment model>]
     */
    static function
    CBAjax_fetchMomentsForUserModelCBID(
        stdClass $args
    ): array
    {
        $userModelCBID = CBModel::valueAsCBID(
            $args,
            'userModelCBID'
        );

        $maxModelsCount = CBModel::valueAsInt(
            $args,
            'maxModelsCount'
        );

        $maxUnixTimestamp = CBModel::valueAsInt(
            $args,
            'maxUnixTimestamp'
        );

        $maxFemtoseconds = CBModel::valueAsInt(
            $args,
            'maxFemtoseconds'
        );

        if (
            $maxUnixTimestamp === null
        ) {
            $cbtimestamp = null;
        }

        else
        {
            $cbtimestamp =
            CB_Timestamp::from(
                $maxUnixTimestamp,
                $maxFemtoseconds
            );
        }

        return
        CB_Moment::fetchMomentsForUserModelCBID(
            $userModelCBID,
            $maxModelsCount,
            $cbtimestamp
        );
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



    // -- CBHTMLOutput interfaces



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs(
    ): array
    {
        $javaScriptURLs =
        [
            CBLibrary::buildLibraryClassFilePath(
                __CLASS__,
                '2022_08_26_1661547529',
                'js',
                cbsysurl()
            ),
        ];

        return $javaScriptURLs;
    }
    // CBHTMLOutput_JavaScriptURLs()



    /**
     * @return [[<name>, <value>]]
     */
    static function
    CBHTMLOutput_JavaScriptVariables(
    ): array {
        $showMomentEditor = false;
        $currentUserModelCBID = ColbyUser::getCurrentUserCBID();

        if (
            $currentUserModelCBID !== null
        ) {
            $currentUserModel = CBModelCache::fetchModelByID(
                $currentUserModelCBID
            );

            $publicProfileIsEnabled = CBUser::getPublicProfileIsEnabled(
                $currentUserModel
            );

            $showMomentEditor = $publicProfileIsEnabled;
        }

        return [
            [
                'CB_Moment_showMomentEditor_jsvariable',
                $showMomentEditor,
            ],
        ];
    }
    /* CBHTMLOutput_JavaScriptVariables() */



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

        $authorUserModelCBID = CB_Moment::getAuthorUserModelCBID(
            $momentSpec
        );

        if (
            $authorUserModelCBID === null
        ) {
            throw new CBExceptionWithValue(
                'The author user model CBID must be set.',
                $momentSpec,
                '30c42acdc8443486eb38b3870ead30b12770d1fb'
            );
        }

        CB_Moment::setAuthorUserModelCBID(
            $momentModel,
            $authorUserModelCBID
        );

        /**
         * @NOTE 2022_01_02
         *
         *      This is special logic used during the transition from a Unix
         *      timestamp to a cbtimestamp.
         */

        $cbtimestampModel = CB_Moment::getCBTimestamp(
            $momentSpec,
        );

        if (
            $cbtimestampModel !== null
        ) {
            CB_Moment::setCBTimestamp(
                $momentModel,
                $cbtimestampModel
            );
        } else {
            CB_Moment::setCreatedTimestamp(
                $momentModel,
                CB_Moment::getCreatedTimestamp(
                    $momentSpec
                )
            );
        }

        CB_Moment::setImage(
            $momentModel,
            CB_Moment::getImage(
                $momentSpec
            )
        );

        CB_Moment::setImageAlternativeText(
            $momentModel,
            CB_Moment::getImageAlternativeText(
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
     * @return [<CB_Timestamp>]
     */
    static function
    CBModel_getCBTimestamps(
        stdClass $momentModel
    ): array {
        $cbtimestampModel = CB_Moment::getCBTimestamp(
            $momentModel
        );

        if (
            $cbtimestampModel === null
        ) {
            return [];
        } else {
            return [
                $cbtimestampModel,
            ];
        }
    }
    /* CBModel_getCBTimestamps() */



    /**
     * @param object $momentModel
     *
     * @return string
     */
    static function
    CBModel_getTitle(
        stdClass $momentModel
    ): string {
        $authorUserModelCBID = CB_Moment::getAuthorUserModelCBID(
            $momentModel
        );

        $authorUserModel = CBModelCache::fetchModelByID(
            $authorUserModelCBID
        );

        $authorFullName = CBUser::getName(
            $authorUserModel
        );

        $textAsCleanLine = CBConvert::stringToCleanLine(
            CB_Moment::getText(
                $momentModel
            )
        );

        return "{$authorFullName} moment: \"{$textAsCleanLine}\"";
    }
    /* CBModel_getTitle() */



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
     * @param object $momentModel
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
         * cbtimestamps can only be reserved if a root model CBID exists. If
         * the root model doesn't have a CBID then an uprade won't work. It's
         * rare for upgrades to occur on models that don't have a root model
         * CBID.
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

        $cbtimestampModel = CB_Moment::getCBTimestamp(
            $momentSpec
        );

        if (
            $cbtimestampModel === null
        ) {
            $createdTimestamp = CB_Moment::getCreatedTimestamp(
                $momentSpec
            );

            /**
             * If there's not a cbtimestamp, the created timestamp should never
             * be null but if it is, we can't do much.
             */

            if (
                $createdTimestamp !== null
            ) {
                $cbtimestampSpec = CB_Timestamp::from(
                    $createdTimestamp
                );

                $cbtimestampSpec = CB_Timestamp::reserveNear(
                    $cbtimestampSpec,
                    $rootModelCBID
                );

                CB_Moment::setCBTimestamp(
                    $momentSpec,
                    $cbtimestampSpec
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
     * @param [object] $momentModelCBIDs
     *
     * @return void
     */
    static function
    CBModels_willDelete(
        array $momentModelCBIDs
    ): void {
        $momentModels = CBModels::fetchModelsByID2(
            $momentModelCBIDs
        );

        foreach (
            $momentModels as $momentModel
        ) {
            $momentModelCBID = CBModel::getCBID(
                $momentModel
            );

            $firstCBID = CB_Moment::getAuthorUserModelCBID(
                $momentModel
            );

            $associationKey = 'CB_Moment_userMoments';

            $secondCBID = $momentModelCBID;

            CBModelAssociations::delete(
                $firstCBID,
                $associationKey,
                $secondCBID
            );
        }
    }
    /* CBModels_willDelete() */



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

            $cbtimestampModel = CB_Moment::getCBTimestamp(
                $momentModel
            );

            CB_ModelAssociation::setSortingValue(
                $modelAssociation,
                CB_Timestamp::getUnixTimestamp(
                    $cbtimestampModel
                )
            );

            CB_ModelAssociation::setSortingValueDifferentiator(
                $modelAssociation,
                CB_Timestamp::getFemtoseconds(
                    $cbtimestampModel
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
    ): void
    {
        $viewPageSpec =
        CBModel::createSpec(
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
            CBModel::getTitle(
                $momentModel
            )
        );

        $viewSpecs =
        [];

        $momentViewSpec =
        CBModel::createSpec(
            'CB_CBView_Moment'
        );

        CB_CBView_Moment::setMomentModelCBID(
            $momentViewSpec,
            CBModel::getCBID(
                $momentModel
            )
        );

        array_push(
            $viewSpecs,
            $momentViewSpec
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
    getCBTimestamp(
        stdClass $momentModel
    ): ?stdClass {
        return CBModel::valueAsModel(
            $momentModel,
            'CB_Moment_cbtimestamp_property',
            'CB_Timestamp'
        );
    }
    /* getCBTimestamp() */



    /**
     * @param object $momentModel
     * @param object $cbtimestampModel
     *
     * @return void
     */
    static function
    setCBTimestamp(
        stdClass $momentModel,
        stdClass $cbtimestampModel
    ): void {
        $verifiedCBTimestampModel = CBConvert::valueAsModel(
            $cbtimestampModel,
            'CB_Timestamp'
        );

        $momentModel->CB_Moment_cbtimestamp_property = (
            $verifiedCBTimestampModel
        );
    }
    /* setCBTimestamp() */



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
     *      This property will always be lower priority than the cbtimestamp
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
     *      This property will always be lower priority than the cbtimestamp
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
     * @return object|null
     */
    static function
    getImage(
        stdClass $momentModel
    ): ?stdClass {
        return CBModel::valueAsModel(
            $momentModel,
            'CB_Moment_imageModel_property',
            'CBImage'
        );
    }
    /* getImage() */



    /**
     * @param object $momentModel
     * @param object $imageModel
     *
     * @return void
     */
    static function
    setImage(
        stdClass $momentModel,
        ?stdClass $imageModel
    ): void {
        if (
            $imageModel === null
        ) {
            $verifiedImageModel = null;
        } else {
            $verifiedImageModel = CBConvert::valueAsModel(
                $imageModel,
                'CBImage'
            );

            if (
                $verifiedImageModel === null
            ) {
                throw new CBExceptionWithValue(
                    'The imageModel parameter is not valid.',
                    $imageModel,
                    'f24ac9f1f710c12988b4e99e3912bb21f674512a'
                );
            }
        }

        $momentModel->CB_Moment_imageModel_property = $verifiedImageModel;
    }
    /* setImage() */



    /**
     * @param object $momentModel
     *
     * @return string
     */
    static function
    getImageAlternativeText(
        stdClass $momentModel
    ): string
    {
        $imageAlternativeText =
        trim(
            CBModel::valueToString(
                $momentModel,
                'CB_Moment_imageAlternativeText_property'
            )
        );

        return $imageAlternativeText;
    }
    /* getImageAlternativeText() */



    /**
     * @param object $momentModel
     * @param srting $newImageAlternativeText
     *
     * @return void
     */
    static function
    setImageAlternativeText(
        stdClass $momentModel,
        string $newImageAlternativeText
    ): void {
        $momentModel->CB_Moment_imageAlternativeText_property =
        $newImageAlternativeText;
    }
    /* setImageAlternativeText() */



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
     * @param string $newText
     *
     * @return void
     */
    static function
    setText(
        stdClass $momentModel,
        string $newText
    ): void {
        $momentModel->CB_Moment_text =
        $newText;
    }
    /* setText() */



    /* -- functions -- */



    /**
     * @param CBID $userModelCBIDArgument
     * @param int $maximumResultCountArgument
     *
     * @return [CBID]
     */
    static function
    fetchMostRecentMomentModelCBIDsForUserModelCBID(
        string $userModelCBIDArgument,
        int $maximumResultCountArgument = 10,
    ): array
    {
        $modelAssociations =
        CBModelAssociations::fetchModelAssociationsByFirstCBIDAndAssociationKey(
            $userModelCBIDArgument,
            'CB_Moment_userMoments',
            'descending',
            $maximumResultCountArgument
        );

        $mostRecentMomentModelCBIDs =
        array_map(
            function (
                stdClass $modelAssociation
            ) {
                $mostRecentMomentModelCBID =
                CB_ModelAssociation::getSecondCBID(
                    $modelAssociation
                );

                return $mostRecentMomentModelCBID;
            },
            $modelAssociations
        );

        return $mostRecentMomentModelCBIDs;
    }
    // fetchMostRecentMomentModelCBIDsForUserModelCBID()



    /**
     * @param CBID $userModelCBID
     * @param int|null $maxModelsCount
     * @param object|null $maxCBTimestamp
     *
     * @return [object]
     */
    static function
    fetchMomentsForUserModelCBID(
        string $userModelCBID,
        ?int $maxModelsCount = null,
        ?object $maxCBTimestamp = null
    ): array
    {
        if (
            $maxCBTimestamp !== null
        )
        {
            $maxUnixTimestamp =
            CB_Timestamp::getUnixTimestamp(
                $maxCBTimestamp
            );

            $maxFemtoseconds =
            CB_Timestamp::getFemtoseconds(
                $maxCBTimestamp
            );
        }

        else
        {
            $maxUnixTimestamp =
            null;

            $maxFemtoseconds =
            null;
        }

        if (
            $maxModelsCount === null ||
            $maxModelsCount < 1 ||
            $maxModelsCount > 50
        )
        {
            $maxModelsCount =
            10;
        }

        $modelAssociations =
        CBModelAssociations::fetchModelAssociationsByFirstCBIDAndAssociationKey(
            $userModelCBID,
            'CB_Moment_userMoments',
            'descending',
            $maxModelsCount,
            null,
            $maxUnixTimestamp,
            null,
            $maxFemtoseconds,
        );

        $momentModels =
        CBModelAssociations::fetchSecondModels(
            $modelAssociations,
            true /* maintainPositions */
        );

        return $momentModels;
    }
    // fetchMomentsForUserModelCBID()

}
