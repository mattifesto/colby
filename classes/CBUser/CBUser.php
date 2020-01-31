<?php

final class CBUser {

    /* -- CBAjax interfaces -- -- -- -- -- */



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

        $passwordIssues = CBuser::passwordIssues($password);

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
    static function CBAjax_addEmailAddress_getUserGroupClassName(): string {
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
    static function CBAjax_changeEmailAddress(
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
    static function CBAjax_changeEmailAddress_getUserGroupClassName(): string {
        return 'CBPublicUserGroup';
    }



    /**
     * @param object $args
     *
     *      {
     *          emailAddress: string
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
    static function CBAjax_signIn(
        stdClass $args
    ): stdClass {
        $emailAddress = CBModel::valueAsEmail(
            $args,
            'emailAddress'
        );

        if (
            $emailAddress === null
        ) {
            return (object)[
                'cbmessage' => 'Your email address is not valid.'
            ];
        }

        $password = CBModel::valueToString(
            $args,
            'password'
        );

        $userCBID = CBUser::emailToUserCBID($emailAddress);

        if ($userCBID === null) {
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

        if ($passwordIsVerified !== true) {
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
            $userCBID
        );

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBAjax_signIn() */



    /**
     * @return string
     */
    static function CBAjax_signIn_getUserGroupClassName(): string {
        return 'CBPublicUserGroup';
    }



    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v500.js', cbsysurl()),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */



    /* -- CBModel interfaces -- -- -- -- -- */



    /**
     * @param object $spec
     *
     * @return object
     */
    static function CBModel_build(
        stdClass $spec
    ): stdClass {
        $userNumericID = CBModel::valueAsInt(
            $spec,
            'userNumericID'
        );


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


        /* Facebook name */

        $facebookName = trim(
            CBModel::valueToString(
                $spec,
                'facebookName'
            )
        );


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


        /* title */

        $title = trim(
            CBModel::valueToString(
                $spec,
                'title'
            )
        );

        if ($title === '') {
            $title = $facebookName;
        }


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


        /* return model */

        return (object)[
            'description' => trim(
                CBModel::valueToString($spec, 'description')
            ),

            'email' => $email,

            /**
             * @deprecated 2019_11_12
             *
             *      This property has been replaced by the facebookName and
             *      facebookUserID properties. When deprecated,
             *      CBModel_upgrade() was changed to extract the values of this
             *      object from the spec. Therefore this property can be
             *      completely removed in a few months.
             */
            'facebook' => $facebook,

            'facebookAccessToken' => CBModel::valueToString(
                $spec,
                'facebookAccessToken'
            ),

            'facebookName' => $facebookName,

            'facebookUserID' => $facebookUserID,

            'lastLoggedIn' => CBModel::valueAsInt(
                $spec,
                'lastLoggedIn'
            ),

            'passwordHash' => $passwordHash,

            'title' => $title,

            'userNumericID' => $userNumericID,
        ];
    }
    /* CBModel_build() */



    /**
     * @param object $originalSpec
     *
     * @return object
     */
    static function CBModel_upgrade(
        stdClass $originalSpec
    ): stdClass {
        $upgradedSpec = CBModel::clone($originalSpec);


        /* userID -> userNumericID */

        if (
            !isset($upgradedSpec->userNumericID)
        ) {
            $upgradedSpec->userNumericID = CBModel::valueAsInt(
                $upgradedSpec,
                'userID'
            );
        }


        /* remove userID property */

        unset($upgradedSpec->userID);


        /* facebook.id -> facebookUserID */

        if (
            !isset($upgradedSpec->facebookUserID)
        ) {
            $upgradedSpec->facebookUserID = CBModel::valueAsInt(
                $upgradedSpec,
                'facebook.id'
            );
        }


        /* facebook.name -> facebookName */

        if (
            !isset($upgradedSpec->facebookName)
        ) {
            $upgradedSpec->facebookName = CBModel::valueToString(
                $upgradedSpec,
                'facebook.name'
            );
        }


        /* remove facebook property */

        unset($upgradedSpec->facebook);


        /* done */

        return $upgradedSpec;
    }
    /* CBModel_upgrade() */



    /**
     * @param [object] $models
     */
    static function CBModels_willDelete(array $userCBIDs) {
        foreach ($userCBIDs as $userCBID) {
            $userCBIDAsSQL = CBID::toSQL($userCBID);

            $SQL = <<<EOT

                DELETE FROM     ColbyUsers

                WHERE           hash = {$userCBIDAsSQL}

            EOT;

            Colby::query($SQL);

            CBModelAssociations::delete(
                null,
                'CBUserGroup_CBUser',
                $userCBID
            );

            CBModelAssociations::delete(
                $userCBID,
                'CBUser_CBUserGroup',
                null
            );
        }
    }
    /* CBModels_willDelete */



    /**
     * @return void
     */
    static function CBModels_willSave(
        array $userModels
    ): void {
        foreach ($userModels as $userModel) {
            $userCBIDAsSQL = CBID::toSQL(
                CBModel::valueAsCBID(
                    $userModel,
                    'ID'
                )
            );


            /* email */

            $userEmail = CBModel::valueAsEmail(
                $userModel,
                'email'
            );

            if ($userEmail === null) {
                $userEmailAsSQL = 'NULL';
            } else {
                $userEmailAsSQL = CBDB::stringToSQL($userEmail);
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

            $userFullNameAsSQL = CBDB::stringToSQL($userFullName);


            /* update row */

            $SQL = <<<EOT

                UPDATE      ColbyUsers

                SET         email = {$userEmailAsSQL},
                            facebookId = {$userFacebookUserIDAsSQL},
                            facebookName = {$userFullNameAsSQL}

                WHERE       hash = {$userCBIDAsSQL}

            EOT;

            Colby::query($SQL);

            if (CBDB::countOfRowsMatched() === 1) {
                return;
            }

            $SQL = <<<EOT

                INSERT INTO ColbyUsers
                (
                    hash,
                    email,
                    facebookId,
                    facebookName
                )
                VALUES
                (
                    {$userCBIDAsSQL},
                    {$userEmailAsSQL},
                    {$userFacebookUserIDAsSQL},
                    {$userFullNameAsSQL}
                )

            EOT;

            Colby::query($SQL);
        }
    }
    /* CBModels_willSave() */



    /* -- functions -- -- -- -- -- */



    /**
     * @param string $email
     *
     * @return CBID|null
     */
    static function emailToUserCBID(
        string $email
    ): ?string {
        $emailAsSQL = CBDB::stringToSQL($email);

        $SQL = <<<EOT

            SELECT      LOWER(HEX(hash))

            FROM        ColbyUsers

            WHERE       email = {$emailAsSQL}

        EOT;

        $result = CBDB::SQLToValue($SQL);

        if (CBID::valueIsCBID($result)) {
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
    static function facebookUserIDToUserCBID(
        int $facebookUserID
    ): ?string {
        $SQL = <<<EOT

            SELECT      LOWER(HEX(hash))

            FROM        ColbyUsers

            WHERE       facebookId = {$facebookUserID}

        EOT;

        $result = CBDB::SQLToValue($SQL);

        if (CBID::valueIsCBID($result)) {
            return $result;
        } else {
            return null;
        }
    }
    /* facebookUserIDToUserCBID() */



    /**
     * This function is called once per website. After the first user has signed
     * in they are assumed to belong to the groups CBAdministratorsUserGroup and
     * CBDevelopersUserGroup.
     *
     * @param CBID $userCBID
     *
     * @return void
     */
    static function initializeFirstUser(
        string $userCBID
    ): void {
        CBUserGroup::addUsers(
            'CBAdministratorsUserGroup',
            $userCBID
        );

        CBUserGroup::addUsers(
            'CBDevelopersUserGroup',
            $userCBID
        );
    }
    /* initializeFirstUser() */



    /**
     * @param string $password
     *
     * @return string|null
     *
     *      If the password has no issues, null is returned.
     */
    static function passwordIssues(
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

        if (count($issues) > 0) {
            return implode(' ', $issues);
        } else {
            return null;
        }
    }
    /* passwordIssues() */

}
