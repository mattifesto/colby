<?php

final class
CBUser
{
    /* -- CBAjax interfaces -- */



    /**
     * @param object $args
     *
     *      {
     *          email: string
     *          email2: string
     *          password: string
     *          password2: string
     *      }
     *
     * @return object
     *
     *      {
     *          succeeded: bool
     *
     *          cbmessage: string
     *
     *              This will only be returned if succeeded is false.
     *      }
     */
    static function CBAjax_addEmailAddress(
        stdClass $args
    ): stdClass {
        $currentUserCBID = ColbyUser::getCurrentUserCBID();

        if ($currentUserCBID === null) {
            return (object)[
                'cbmessage' => (
                    'You must be signed in to add an email address.'
                ),
            ];
        }

        $email = CBModel::valueAsEmail(
            $args,
            'email'
        );

        if ($email === null) {
            return (object)[
                'cbmessage' => 'The email address you entered is not valid.',
            ];
        }

        $emailUserCBID = CBUser::emailToUserCBID(
            $email
        );

        if ($emailUserCBID !== null) {
            return (object)[
                'cbmessage' => 'This email is used by another account.',
            ];
        }

        $email2 = CBModel::valueAsEmail(
            $args,
            'email2'
        );

        if ($email2 !== $email) {
            return (object)[
                'cbmessage' => 'Your email addresses do not match.',
            ];
        }

        $password = CBModel::valueToString(
            $args,
            'password'
        );

        $passwordIssues = CBUser::passwordIssues($password);

        if ($passwordIssues !== null) {
            return (object)[
                'cbmessage' => CBMessageMarkup::stringToMessage(
                    $passwordIssues
                ),
            ];
        }

        $password2 = CBModel::valueToString(
            $args,
            'password2'
        );

        if ($password2 !== $password) {
            return (object)[
                'cbmessage' => 'Your passwords do not match.',
            ];
        }

        $currentUserSpec = CBModels::fetchSpecByIDNullable(
            $currentUserCBID
        );

        $currentUserEmail = trim(
            CBModel::valueToString(
                $currentUserSpec,
                'email'
            )
        );

        if ($currentUserEmail !== '') {
            return (object)[
                'cbmessage' => 'This account already has an email addresss.',
            ];
        }

        $passwordHash = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        if ($passwordHash === false) {
            return (object)[
                'cbmessage' => 'An error occured while hashing your password.',
            ];
        }

        $currentUserSpec->email = $email;
        $currentUserSpec->passwordHash = $passwordHash;

        CBDB::transaction(
            function () use ($currentUserSpec) {
                CBModels::save($currentUserSpec);
            }
        );

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBAjax_addEmailAddress() */



    /**
     * @return string
     */
    static function
    CBAjax_addEmailAddress_getUserGroupClassName(
    ): string {
        return 'CBPublicUserGroup';
    }



    /**
     * @param object $args
     *
     *      {
     *          email: string
     *          email2: string
     *          password: string
     *      }
     *
     * @return object
     *
     *      {
     *          succeeded: bool
     *
     *          cbmessage: string
     *
     *              This will only be returned if succeeded is false.
     *      }
     */
    static function
    CBAjax_changeEmailAddress(
        stdClass $args
    ): stdClass {
        $currentUserCBID = ColbyUser::getCurrentUserCBID();

        if ($currentUserCBID === null) {
            return (object)[
                'cbmessage' => (
                    'You must be signed in to change your email address.'
                ),
            ];
        }

        $email = CBModel::valueAsEmail(
            $args,
            'email'
        );

        if ($email === null) {
            return (object)[
                'cbmessage' => 'The email address you entered is not valid.',
            ];
        }

        $emailUserCBID = CBUser::emailToUserCBID(
            $email
        );

        if ($emailUserCBID !== null) {
            return (object)[
                'cbmessage' => 'This email is used by another account.',
            ];
        }

        $email2 = CBModel::valueAsEmail(
            $args,
            'email2'
        );

        if ($email2 !== $email) {
            return (object)[
                'cbmessage' => 'Your email addresses do not match.',
            ];
        }

        $password = CBModel::valueToString(
            $args,
            'password'
        );

        $currentUserSpec = CBModels::fetchSpecByIDNullable(
            $currentUserCBID
        );

        $passwordHash = CBModel::valueToString(
            $currentUserSpec,
            'passwordHash'
        );

        $passwordIsVerified = password_verify(
            $password,
            $passwordHash
        );

        if ($passwordIsVerified !== true) {
            return (object)[
                'cbmessage' => 'Your password is not correct.',
            ];
        }

        $currentUserSpec->email = $email;

        CBDB::transaction(
            function () use ($currentUserSpec) {
                CBModels::save($currentUserSpec);
            }
        );

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBAjax_changeEmailAddress() */



    /**
     * @return string
     */
    static function
    CBAjax_changeEmailAddress_getUserGroupClassName(
    ): string {
        return 'CBPublicUserGroup';
    }
    /* CBAjax_changeEmailAddress_getUserGroupClassName() */



    /**
     * @param object $args
     *
     *      {
     *          userModelCBID: CBID
     *      }
     *
     * @return object
     *
     *      {
     *          CBUser_publicProfile_fullName: string
     *          CBUser_publicProfile_prettyUsername: string
     *      }
     */
    static function
    CBAjax_fetchPublicProfileByUserModelCBID(
        stdClass $args
    ) {
        $targetUserModelCBID = CBModel::valueAsCBID(
            $args,
            'userModelCBID'
        );

        if (
            $targetUserModelCBID === null
        ) {
            throw new InvalidArgumentException(
                'userModelCBID'
            );
        }

        $targetUserModel = CBModels::fetchModelByCBID(
            $targetUserModelCBID
        );

        if (
            $targetUserModel === null ||

            CBModel::getClassName(
                $targetUserModel
            ) !== 'CBUser'
        ) {
            throw new InvalidArgumentException(
                'userModelCBID'
            );
        }

        $publicProfileIsEnabled = CBUser::getPublicProfileIsEnabled(
            $targetUserModel
        );

        if (
            $publicProfileIsEnabled !== true
        ) {
            $currentUserModelCBID = ColbyUser::getCurrentUserCBID();

            if (
                $currentUserModelCBID !== $targetUserModelCBID
            ) {
                $currentUserIsAnAdministrator = (
                    CBUserGroup::userIsMemberOfUserGroup(
                        $currentUserModelCBID,
                        'CBAdministratorsUserGroup'
                    )
                );

                if (
                    $currentUserIsAnAdministrator !== true
                ) {
                    throw new CBException(
                        CBConvert::stringToCleanLine(<<<EOT

                            The current user does not have permission to call
                            this ajax function.

                        EOT),
                        '',
                        '15cfbd719f11caeb1fab47699e9bf3e49eab33df'
                    );
                }
            }
        }

        $fullName = CBUser::getName(
            $targetUserModel
        );

        $prettyUsername = CBUser::getPrettyUsername(
            $targetUserModel
        );

        return (object)[
            'CBUser_publicProfile_fullName' => $fullName,
            'CBUser_publicProfile_prettyUsername' => $prettyUsername,
        ];
    }
    /* CBAjax_fetchPublicProfileByUserModelCBID() */



    static function
    CBAjax_fetchPublicProfileByUserModelCBID_getUserGroupClassName(
    ): string {
        return 'CBPublicUserGroup';
    }
    /* CBAjax_fetchPublicProfileByUserModelCBID_getUserGroupClassName() */



    /**
     * @param object $args
     *
     *      {
     *          targetUserCBID: CBID
     *      }
     *
     * @return bool
     */
    static function
    CBAjax_fetchPublicProfileIsEnabled(
        stdClass $args
    ): bool {
        $targetUserCBID = CBModel::valueAsCBID(
            $args,
            'targetUserCBID'
        );

        if ($targetUserCBID === null) {
            if ($targetUserCBID === null) {
                throw new CBExceptionWithValue(
                    'The "targetUserCBID" argument is not valid.',
                    $args,
                    'f0a4e82374632b7d408987b1b8cf6c7474141226'
                );
            }
        }

        $currentUserCBID = ColbyUser::getCurrentUserCBID();

        if (
            $targetUserCBID !== $currentUserCBID &&
            !CBUserGroup::userIsMemberOfUserGroup(
                $currentUserCBID,
                'CBAdministratorsUserGroup'
            )
        ) {
            throw new CBException(
                CBConvert::stringToCleanLine(<<<EOT

                    The current user does not have permission to call this ajax
                    function.

                EOT),
                '',
                'b9aa8abb2df46bc78cb836a9c7d4460e232c8579'
            );
        }

        $userModel = CBModels::fetchModelByCBID(
            $targetUserCBID
        );

        if (
            $userModel === null
        ) {
            return false;
        }

        $userModelClassName = CBModel::getClassName(
            $userModel
        );

        if (
            $userModelClassName !== 'CBUser'
        ) {
            throw new CBException(
                CBConvert::stringToCleanLine(<<<EOT

                    The target user CBID argument is not the CBID of a user
                    model.

                EOT),
                '',
                'c62382a924d00ffa6dce7c449cfb77e30b15d05a'
            );
        }

        $publicProfileIsEnabled = CBUser::getPublicProfileIsEnabled(
            $userModel
        );

        return $publicProfileIsEnabled;
    }
    /* CBAjax_fetchPublicProfileIsEnabled() */



    static function
    CBAjax_fetchPublicProfileIsEnabled_getUserGroupClassName(
    ): string {
        return 'CBPublicUserGroup';
    }
    /* CBAjax_fetchPublicProfileIsEnabled_getUserGroupClassName() */



    /**
     * @param object $args
     *
     *      {
     *          targetUserCBID: CBID
     *          newPublicProfileIsEnabledValue: bool
     *      }
     *
     * @return void
     */
    static function
    CBAjax_setPublicProfileIsEnabled(
        stdClass $args
    ): void {
        $targetUserCBID = CBModel::valueAsCBID(
            $args,
            'targetUserCBID'
        );

        if ($targetUserCBID === null) {
            if ($targetUserCBID === null) {
                throw new CBExceptionWithValue(
                    'The "targetUserCBID" argument is not valid.',
                    $args,
                    'bae1d8fd19962c5e705deb69f00a65987fc8331f'
                );
            }
        }

        $currentUserCBID = ColbyUser::getCurrentUserCBID();

        if (
            $targetUserCBID !== $currentUserCBID &&
            !CBUserGroup::userIsMemberOfUserGroup(
                $currentUserCBID,
                'CBAdministratorsUserGroup'
            )
        ) {
            throw new CBException(
                CBConvert::stringToCleanLine(<<<EOT

                    The current user does not have permission to call this ajax
                    function.

                EOT),
                '',
                'ee142cd80ca364bf5085d226f104fccf613c02bb'
            );
        }

        $userSpec = CBModels::fetchModelByCBID(
            $targetUserCBID
        );

        $userSpecClassName = CBModel::getClassName(
            $userSpec
        );

        if (
            $userSpecClassName !== 'CBUser'
        ) {
            throw new CBException(
                CBConvert::stringToCleanLine(<<<EOT

                    The target user CBID argument is not the CBID of a user
                    model.

                EOT),
                '',
                '65937defe670aeb837438f71458945962bb776f4'
            );
        }

        $newPublicProfileIsEnabledValue = CBModel::valueToBool(
            $args,
            'newPublicProfileIsEnabledValue'
        );

        CBUser::setPublicProfileIsEnabled(
            $userSpec,
            $newPublicProfileIsEnabledValue
        );

        CBDB::transaction(
            function () use (
                $userSpec
            ) {
                CBModels::save(
                    $userSpec
                );
            }
        );
    }
    /* CBAjax_setPublicProfileIsEnabled() */



    static function
    CBAjax_setPublicProfileIsEnabled_getUserGroupClassName(
    ): string {
        return 'CBPublicUserGroup';
    }
    /* CBAjax_setPublicProfileIsEnabled_getUserGroupClassName() */



    /**
     * @return void
     */
    static function
    CBAjax_signOut(
    ): void {
        ColbyUser::logoutCurrentUser();
    }
    /* CBAjax_signOut() */



    static function
    CBAjax_signOut_getUserGroupClassName(
    ): string {
        return 'CBPublicUserGroup';
    }
    /* CBAjax_signOut_getUserGroupClassName() */



    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



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
     * @return [[<name>,<value>]]
     */
    static function
    CBHTMLOutput_JavaScriptVariables(
    ): array
    {
        return
        [
            [
                'CBUser_currentUserModelCBID_jsvariable',
                ColbyUser::getCurrentUserCBID(),
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
            'CBAjax',
            'CBErrorHandler',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */



    /* -- CBInstall interfaces -- -- -- -- -- */



    /**
     * This function checks the settings object for developer account
     * information and then creates or updates the main developer user model.
     * This is how the first account is created after installing a website.
     *
     * @return void
     */
    static function
    CBInstall_configure(
    ): void {
        $settingsObject = Colby::getSettingsObject();


        /* email address */

        $developerEmailAddress = CBModel::valueAsEmail(
            $settingsObject,
            'developerEmailAddress'
        );

        if (
            $developerEmailAddress === null
        ) {
            return;
        }


        /* CBUser CBID */

        $foundUserCBID = CBUser::emailToUserCBID(
            $developerEmailAddress
        );

        if (
            $foundUserCBID === null
        ) {
            $userCBID = CBID::generateRandomCBID();
        } else {
            $userCBID = $foundUserCBID;
        }

        $userSpec = (object)[
            'className' => 'CBUser',
            'ID' => $userCBID,
            'email' => $developerEmailAddress,
        ];

        if (
            $foundUserCBID === null
        ) {

            /**
             * If the user is being created, we set the user's password. We
             * assume the password is valid because there is not much reason
             * that it shouldn't be.
             *
             * @TODO 2020_02_14
             *
             *      We may want to set a short expiration on this password to
             *      force the developer to set another password soon.
             */

            $developerPasswordHash = CBModel::valueToString(
                $settingsObject,
                'developerPasswordHash'
            );

            $userSpec->passwordHash = $developerPasswordHash;
        }


        /* create / save */

        CBModelUpdater::update(
            $userSpec
        );


        /* add to groups */

        CBUserGroup::addUsers(
            'CBAdministratorsUserGroup',
            $userCBID
        );

        CBUserGroup::addUsers(
            'CBDevelopersUserGroup',
            $userCBID
        );
    }
    /* CBInstall_configure() */



    /* -- CBModel interfaces -- -- -- -- -- */



    /**
     * @param object $spec
     *
     * @return object
     */
    static function
    CBModel_build(
        stdClass $spec
    ): stdClass {

        /* email */

        $email = CBModel::valueAsEmail(
            $spec,
            'email'
        );


        /* deprecated */

        $facebook = CBModel::valueAsObject(
            $spec,
            'facebook'
        );

        if ($facebook !== null) {
            $facebook = CBModel::clone($facebook);
        }


        /* Facebook user ID */

        $facebookUserID = CBModel::valueAsInt(
            $spec,
            'facebookUserID'
        );

        if (
            $facebookUserID !== null &&
            $facebookUserID <= 0
        ) {
            throw CBException::createWithValue(
                'The Facebook user ID is invalid.',
                $facebookUserID,
                '2417ee1efc46f05e3ae75cd6050ea4c52c719a65'
            );
        }


        /* password hash */

        $passwordHash = CBModel::valueToString(
            $spec,
            'passwordHash'
        );


        /* validation */

        if (
            $email === null &&
            $facebookUserID === null
        ) {
            throw new CBExceptionWithValue(
                CBConvert::stringToCleanLine(<<<EOT

                    A CBUser spec must have at least one of the "email" or
                    "facebookUserID" properties set.

                EOT),
                $spec,
                '44f5f16eb31531b6f6caf00ca23fd6472f5f623c'
            );
        }


        $userModel =
        (object)
        [
            'description' =>
            trim(
                CBModel::valueToString(
                    $spec,
                    'description'
                )
            ),

            'email' =>
            $email,

            /**
             * @deprecated 2019_11_12
             *
             *      This property has been replaced by the facebookName and
             *      facebookUserID properties. When deprecated,
             *      CBModel_upgrade() was changed to extract the values of this
             *      object from the spec. Therefore this property can be
             *      completely removed in a few months.
             */
            'facebook' =>
            $facebook,

            'facebookUserID' =>
            $facebookUserID,

            'lastLoggedIn' =>
            CBModel::valueAsInt(
                $spec,
                'lastLoggedIn'
            ),

            'passwordHash' =>
            $passwordHash,
        ];

        CBUser::setFacebookAccessToken(
            $userModel,
            CBUser::getFacebookAccessToken(
                $spec
            )
        );

        CBUser::setFacebookName(
            $userModel,
            CBUser::getFacebookName(
                $spec
            )
        );

        CBUser::setName(
            $userModel,
            CBUser::getName(
                $spec
            )
        );

        CBUser::setPublicProfileIsEnabled(
            $userModel,
            CBUser::getPublicProfileIsEnabled(
                $spec
            )
        );

        CBUser::setPrettyUsername(
            $userModel,
            CBUser::getPrettyUsername(
                $spec
            )
        );

        return $userModel;
    }
    /* CBModel_build() */



    /**
     * @param object $momentModel
     *
     * @return string
     */
    static function
    CBModel_getTitle(
        stdClass $momentModel
    ): string {
        return CBUser::getName(
            $momentModel
        );
    }
    /* CBModel_getTitle() */



    /**
     * @param object $userModel
     *
     * @return string
     */
    static function
    CBModel_toSearchText(
        stdClass $userModel
    ): string
    {
        $searchTextStrings =
        [];

        $fullName =
        CBUser::getName(
            $userModel
        );

        array_push(
            $searchTextStrings,
            $fullName
        );

        $facebookName =
        CBUser::getFacebookName(
            $userModel
        );

        array_push(
            $searchTextStrings,
            $facebookName
        );

        $prettyUsername =
        CBUser::getPrettyUsername(
            $userModel
        );

        array_push(
            $searchTextStrings,
            "@${prettyUsername}"
        );

        $emailAddress =
        CBUser::getEmailAddress(
            $userModel
        );

        array_push(
            $searchTextStrings,
            $emailAddress
        );

        $searchText =
        CBConvert::stringToCleanLine(
            implode(
                ' ',
                $searchTextStrings
            )
        );

        return $searchText;
    }
    /* CBModel_toSearchText() */



    /**
     * @param object $userModel
     *
     * @return string
     */
    static function
    CBModel_toURLPath(
        stdClass $userModel
    ): string {
        $publicProfileIsEnabled = CBUser::getPublicProfileIsEnabled(
            $userModel
        );

        if (
            $publicProfileIsEnabled !== true
        ) {
            return '';
        }

        $prettyUsername = CBUser::getPrettyUsername(
            $userModel
        );

        return "/user/{$prettyUsername}/";
    }
    /* CBModel_toURLPath() */



    /**
     * @param object $originalSpec
     *
     * @return object
     */
    static function
    CBModel_upgrade(
        stdClass $originalSpec
    ): stdClass
    {
        /**
         * version 675 upgrades can be removed in version 676
         */

        $upgradedSpec =
        CBModel::clone(
            $originalSpec
        );


        /* facebook.id -> facebookUserID */

        if (
            !isset($upgradedSpec->facebookUserID)
        ) {
            $upgradedSpec->facebookUserID =
            CBModel::valueAsInt(
                $upgradedSpec,
                'facebook.id'
            );
        }


        /* facebook.name -> facebookName */

        if (
            !isset($upgradedSpec->facebookName)
        ) {
            $upgradedSpec->facebookName =
            CBModel::valueToString(
                $upgradedSpec,
                'facebook.name'
            );
        }


        /* remove facebook property */

        unset(
            $upgradedSpec->facebook
        );



        /**
         * @NOTE 2022_01_15
         *
         *      If the user doesn't have a username, generate one for them.
         */

        $userModelCBID =
        CBModel::getCBID(
            $originalSpec
        );

        $currentUsername =
        CBUser::getPrettyUsername(
            $originalSpec
        );

        if (
            $currentUsername === ''
        ) {
            $randomUsername =
            CBUser::generateRandomAvailablePrettyUsername(
                $userModelCBID
            );

            CBUser::setPrettyUsername(
                $upgradedSpec,
                $randomUsername
            );
        }



        /**
         * The model version is updated when every single model with this class
         * name needs to be updated. Document the reason for each change in this
         * comment.
         *
         * 2022_03_30
         *
         *      When users would change their username, the old username
         *      association was not being removed. The bug was fixed and
         *      re-saving will fix any user models that are affected.
         *
         * 2022_06_10_1654869456
         *
         *      The search text has been updated.
         */

        $upgradedSpec->CBUser_versionDate_property =
        '2022_06_10_1654869456';



        /* done */

        return
        $upgradedSpec;
    }
    /* CBModel_upgrade() */



    /**
     * @param [object] $userModelCBIDs
     */
    static function
    CBModels_willDelete(
        array $userModelCBIDs
    ) {
        foreach (
            $userModelCBIDs as $userModelCBID
        ) {
            $userModelCBIDAsSQL = CBID::toSQL(
                $userModelCBID
            );


            /* remove username reservation */

            CBModelAssociations::delete(
                $userModelCBID,
                'CBUser_username_association'
            );


            /* dele0te ColbyUsers row */

            $SQL = <<<EOT

                DELETE FROM
                ColbyUsers

                WHERE
                hash = {$userModelCBIDAsSQL}

            EOT;

            Colby::query(
                $SQL
            );


            /* remove user group memberships */

            CBModelAssociations::delete(
                null,
                'CBUserGroup_CBUser',
                $userModelCBID
            );

            CBModelAssociations::delete(
                $userModelCBID,
                'CBUser_CBUserGroup',
                null
            );
        }
    }
    /* CBModels_willDelete */



    /**
     * @return void
     */
    static function
    CBModels_willSave(
        array $userModels
    ): void
    {
        foreach (
            $userModels as $userModel
        ) {
            $userModelCBID =
            CBModel::getCBID(
                $userModel
            );

            $userModelCBIDAsSQL =
            CBID::toSQL(
                $userModelCBID
            );



            /**
             * @NOTE 2022_03_30
             *
             *      It's important to know that we are inside a transaction when
             *      this function is called because the queries below are very
             *      risky if we are not.
             */

            $prettyUsername =
            CBUser::getPrettyUsername(
                $userModel
            );

            /**
             * Delete previous username. This actually works to delete
             * multiple usernames that may be associated with a user due
             * to a bug (that has been fixed).
             */

            CBModelAssociations::delete(
                $userModelCBID,
                'CBUser_username_association'
            );

            /**
             * User models will soon be required to have usernames but
             * at this time they aren't.
             */

            if (
                $prettyUsername !== ''
            ) {
                $potentialUsernameCBID =
                CB_Username::prettyUsernameToUsernameModelCBID(
                    $prettyUsername
                );

                $existingUsernameAssociation =
                CBModelAssociations::fetchOne(
                    null,
                    'CBUser_username_association',
                    $potentialUsernameCBID
                );

                /**
                 * Since we've already delete any username associations for this
                 * user if a username association exists it means the username is
                 * associated with another user and is therefore not available.
                 */

                if (
                    $existingUsernameAssociation !== null
                ) {
                    throw
                    new CBExceptionWithValue(
                        CBConvert::stringToCleanLine(<<<EOT

                            The username in this user model has already been
                            associated with another user.

                        EOT),
                        $userModel,
                        '6b9e8d649985a7fdd993eaefd94220a8de53bebb'
                    );
                }

                $newAssociation = CBModel::createSpec(
                    'CB_ModelAssociation'
                );

                CB_ModelAssociation::setFirstCBID(
                    $newAssociation,
                    $userModelCBID
                );

                CB_ModelAssociation::setAssociationKey(
                    $newAssociation,
                    'CBUser_username_association'
                );

                CB_ModelAssociation::setSecondCBID(
                    $newAssociation,
                    $potentialUsernameCBID
                );

                CBModelAssociations::insertOrUpdate(
                    $newAssociation
                );
            }



            /* email */

            $userEmail = CBModel::valueAsEmail(
                $userModel,
                'email'
            );

            if (
                $userEmail === null
            ) {
                $userEmailAsSQL = 'NULL';
            } else {
                $userEmailAsSQL = CBDB::stringToSQL(
                    $userEmail
                );
            }


            /* facebookUserID */

            $userFacebookUserID = CBModel::valueAsInt(
                $userModel,
                'facebookUserID'
            );

            $userFacebookUserIDAsSQL = $userFacebookUserID ?? 'NULL';

            /**
             * The facebookName column holds the users preferred name. It will
             * eventually be either renamed or removed. For now, it holds the
             * value of the "title" property which is set to the Facebook name
             * if it has not been set on the spec.
             */

            $userFullName = CBModel::valueToString(
                $userModel,
                'title'
            );

            $userFullNameAsSQL = CBDB::stringToSQL(
                $userFullName
            );


            /* update row */

            $SQL = <<<EOT

                UPDATE
                ColbyUsers

                SET
                email = {$userEmailAsSQL},
                facebookId = {$userFacebookUserIDAsSQL},
                facebookName = {$userFullNameAsSQL}

                WHERE
                hash = {$userModelCBIDAsSQL}

            EOT;

            Colby::query(
                $SQL
            );

            if (
                CBDB::countOfRowsMatched() === 1
            ) {
                return;
            }

            $SQL = <<<EOT

                INSERT INTO
                ColbyUsers

                (
                    hash,
                    email,
                    facebookId,
                    facebookName
                )

                VALUES
                (
                    {$userModelCBIDAsSQL},
                    {$userEmailAsSQL},
                    {$userFacebookUserIDAsSQL},
                    {$userFullNameAsSQL}
                )

            EOT;

            Colby::query(
                $SQL
            );
        }
    }
    /* CBModels_willSave() */



    /* -- accessors -- */



    /**
     * @param object $userModel
     *
     * @return string
     */
    static function
    getEmailAddress(
        stdClass $userModel
    ): string {
        return CBModel::valueToString(
            $userModel,
            'email'
        );
    }
    /* getEmailAddress() */



    /**
     * @param object $userModel
     * @param string $emailAddress
     *
     * @return void
     */
    static function
    setEmailAddress(
        stdClass $userModel,
        string $emailAddress
    ): void {
        $userModel->email = $emailAddress;
    }
    /* setEmailAddress() */



    /**
     * @param object $userModel
     *
     * @return string
     *
     *      Returns an empty string if no access token is available.
     */
    static function
    getFacebookAccessToken(
        stdClass $userModel
    ): string
    {
        $facebookAccessToken =
        trim(
            CBModel::valueToString(
                $userModel,
                'facebookAccessToken'
            )
        );

        return $facebookAccessToken;
    }
    /* getFacebookAccessToken() */



    /**
     * @param object $userModel
     * @param string $newFacebookAccessToken
     *
     * @return void
     */
    static function
    setFacebookAccessToken(
        stdClass $userModel,
        string $newFacebookAccessToken
    ): void
    {
        $userModel->facebookAccessToken =
        $newFacebookAccessToken;
    }
    /* setFacebookAccessToken() */



    /**
     * @param object $userModel
     *
     * @return string
     */
    static function
    getFacebookName(
        stdClass $userModel
    ): string {
        $userFacebookName = trim(
            CBModel::valueToString(
                $userModel,
                'facebookName'
            )
        );

        return $userFacebookName;
    }
    /* getFacebookName() */



    /**
     * @param object $userModel
     * @param string $facebookName
     *
     * @return void
     */
    static function
    setFacebookName(
        stdClass $userModel,
        string $facebookName
    ): void {
        $userModel->facebookName = $facebookName;
    }
    /* setFacebookName() */



    /**
     * @param object $userModel
     *
     * @return string
     */
    static function
    getName(
        stdClass $userModel
    ): string {
        $userName = trim(
            CBModel::valueToString(
                $userModel,
                'title'
            )
        );

        if ($userName === '') {
            $userName = CBUser::getFacebookName(
                $userModel
            );
        }

        return $userName;
    }
    /* getName() */



    /**
     * @param object $userModel
     * @param string $name
     *
     * @return void
     */
    static function
    setName(
        stdClass $userModel,
        string $name
    ): void {
        $userModel->title = $name;
    }
    /* setName() */



    /**
     * @param object $userModel
     *
     * @return string
     */
    static function
    getPasswordHash(
        stdClass $userModel
    ): string {
        return CBModel::valueToString(
            $userModel,
            'passwordHash'
        );
    }
    /* getPasswordHash() */



    /**
     * @param object $userModel
     * @param string $passwordHash
     *
     * @return void
     */
    static function
    setPasswordHash(
        stdClass $userModel,
        string $passwordHash
    ): void {
        $userModel->passwordHash = $passwordHash;
    }
    /* setPasswordHash() */



    /**
     * @param object $userModel
     *
     * @return string
     *
     *      If the user model doesn't have a pretty username, this function will
     *      return an empty string.
     */
    static function
    getPrettyUsername(
        stdClass $userModel
    ): string {
        return CBModel::valueToString(
            $userModel,
            'CBUser_prettyUsername_property'
        );
    }
    /* getPrettyUsername() */



    /**
     * @param object $userModel
     * @param string $passwordHash
     *
     * @return void
     */
    static function
    setPrettyUsername(
        stdClass $userModel,
        string $prettyUsername
    ): void {
        if (
            $prettyUsername !== ''
        ) {
            $prettyUsernameIsValid = CB_Username::isPrettyUsernameValid(
                $prettyUsername
            );

            if (
                $prettyUsernameIsValid !== true
            ) {
                throw new CBExceptionWithValue(
                    CBConvert::stringToCleanLine(<<<EOT

                        The prettyUsername argument is not a valid pretty
                        username.

                    EOT),
                    $prettyUsername,
                    'bd58123f8ee2e8c45ac8014cdf30dd6a472b8813'
                );
            }
        }

        $userModel->CBUser_prettyUsername_property = $prettyUsername;
    }
    /* setPrettyUsername() */



    /**
     * @param object $userModel
     *
     * @return bool
     */
    static function
    getPublicProfileIsEnabled(
        stdClass $userModel
    ): bool {
        return CBModel::valueToBool(
            $userModel,
            'CBUser_publicProfileIsEnabled'
        );
    }
    /* getPublicProfileIsEnabled() */



    /**
     * @param object $userModel
     * @param bool $newPublicProfileIsEnabledValue
     *
     * @return void
     */
    static function
    setPublicProfileIsEnabled(
        stdClass $userModel,
        bool $newPublicProfileIsEnabledValue
    ): void {
        $userModel->CBUser_publicProfileIsEnabled = (
            $newPublicProfileIsEnabledValue
        );
    }
    /* setPublicProfileIsEnabled() */



    /* -- functions -- -- -- -- -- */



    /**
     * @param string $email
     *
     * @return CBID|null
     */
    static function
    emailToUserCBID(
        string $email
    ): ?string {
        $emailAsSQL = CBDB::stringToSQL(
            $email
        );

        $SQL = <<<EOT

            SELECT
            LOWER(HEX(hash))

            FROM
            ColbyUsers

            WHERE
            email = {$emailAsSQL}

        EOT;

        $result = CBDB::SQLToValue(
            $SQL
        );

        if (
            CBID::valueIsCBID(
                $result
            )
        ) {
            return $result;
        } else {
            return null;
        }
    }
    /* emailToUserCBID() */



    /**
     * @param string $email
     *
     * @return CBID|null
     */
    static function
    facebookUserIDToUserCBID(
        int $facebookUserID
    ): ?string {
        $SQL = <<<EOT

            SELECT
            LOWER(HEX(hash))

            FROM
            ColbyUsers

            WHERE
            facebookId = {$facebookUserID}

        EOT;

        $result = CBDB::SQLToValue(
            $SQL
        );

        if (
            CBID::valueIsCBID(
                $result
            )
        ) {
            return $result;
        } else {
            return null;
        }
    }
    /* facebookUserIDToUserCBID() */



    /**
     * @return string
     *
     *      A random username that has been confirmed to be available.
     */
    static function
    generateRandomAvailablePrettyUsername(
    ): string {
        $randomCBID = CBID::generateRandomCBID();

        while (
            true
        ) {
            $randomPrettyUsername = (
                'user' .
                mb_substr(
                    $randomCBID,
                    0,
                    25
                )
            );

            $userModelCBID = CBUser::prettyUsernameToUserModelCBID(
                $randomPrettyUsername
            );


            if (
                $userModelCBID === null
            ) {
                return $randomPrettyUsername;
            }
        }
    }
    /* generateRandomAvailablePrettyUsername() */



    /**
     * @param string|null $destinationURL
     *
     * @return string
     */
    static function
    getCreateAccountPageURL(
        ?string $destinationURL = null
    ): string {
        if (
            empty(
                $destinationURL
            )
        ) {
            $destinationURL = $_SERVER['REQUEST_URI'];
        }

        $state = (object)[
            'destinationURL' => $destinationURL,
        ];

        $stateAsJSON = json_encode(
            $state
        );

        $signInPageURL = (
            cbsiteurl() .
            '/colby/user/create-account/?state=' .
            urlencode($stateAsJSON)
        );

        return $signInPageURL;
    }
    /* getCreateAccountPageURL() */



    /**
     * @param string|null $destinationURL
     *
     * @return string
     */
    static function
    getSignInPageURL(
        ?string $destinationURL = null
    ): string {
        if (
            empty(
                $destinationURL
            )
        ) {
            $destinationURL = $_SERVER['REQUEST_URI'];
        }

        $state = (object)[
            'destinationURL' => $destinationURL,
        ];

        $stateAsJSON = json_encode(
            $state
        );

        $signInPageURL = (
            cbsiteurl() .
            '/signin/?state=' .
            urlencode($stateAsJSON)
        );

        return $signInPageURL;
    }
    /* getSignInPageURL() */



    /**
     * @param string $password
     *
     * @return string|null
     *
     *      If the password has no issues, null is returned.
     */
    static function
    passwordIssues(
        string $password
    ): ?string {
        $issues = [];

        if (
            trim($password) !== $password
        ) {
            $issues[] = 'Your password begins or ends with white space.';
        }

        if (
            strlen($password) < 8
        ) {
            $issues[] = 'Your password has less than 8 characters.';
        }

        if (
            count($issues) > 0
        ) {
            return implode(' ', $issues);
        } else {
            return null;
        }
    }
    /* passwordIssues() */



    /**
     * @param string $prettyUsername
     *
     * @return CBID|null
     */
    static function
    prettyUsernameToUserModelCBID(
        string $prettyUsername
    ): ?string {
        $usernameCBID = CB_Username::prettyUsernameToUsernameModelCBID(
            $prettyUsername
        );

        $association = CBModelAssociations::fetchOne(
            null,
            'CBUser_username_association',
            $usernameCBID
        );

        if (
            $association === null
        ) {
            return null;
        } else {
            return $association->ID;
        }
    }
    /* prettyUsernameToUserModelCBID() */



    /**
     * @param string $emailAddress
     * @param string $password
     * @param bool $shouldKeepSignedIn
     *
     *      This value should be determined by the user and should only be true
     *      if the user feels their device is trusted and not openly
     *      compromised.
     *
     * @return object
     *
     *      The properties of the returned object will not always be set. They
     *      should be accessed with CBModel functions.
     *
     *      {
     *          succeeded: bool
     *
     *              This will be true if the user was logged in; otherwise
     *              false.
     *
     *          cbmessage: string
     *
     *              This will only be returned if succeeded is false.
     *      }
     */
    static function
    signIn(
        string $emailAddress,
        string $password,
        bool $shouldKeepSignedIn = false
    ): stdClass {
        $emailAddress = CBConvert::valueAsEmail(
            $emailAddress
        );

        if (
            $emailAddress === null
        ) {
            return (object)[
                'cbmessage' => 'Your email address is not valid.'
            ];
        }

        $userCBID = CBUser::emailToUserCBID(
            $emailAddress
        );

        if (
            $userCBID === null
        ) {
            return (object)[
                'cbmessage' => <<<EOT

                    No user exists with this email address.

                EOT,
            ];
        }

        $userModel = CBModels::fetchModelByIDNullable(
            $userCBID
        );

        $passwordHash = CBModel::valueToString(
            $userModel,
            'passwordHash'
        );

        $passwordIsVerified = password_verify(
            $password,
            $passwordHash
        );

        if (
            $passwordIsVerified !== true
        ) {
            return (object)[
                'cbmessage' => <<<EOT

                    Your password is not correct.

                EOT,
            ];
        }

        $userSpecUpdates = (object)[
            'ID' => $userCBID,
            'lastLoggedIn' => time(),
        ];

        CBModelUpdater::update(
            $userSpecUpdates
        );

        ColbyUser::loginUser(
            $userCBID,
            $shouldKeepSignedIn
        );

        return (object)[
            'succeeded' => true,
        ];
    }
    /* signIn() */

}
