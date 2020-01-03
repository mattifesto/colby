<?php

final class CBUser {

    /* -- CBAjax interfaces -- -- -- -- -- */



    /**
     * @param object $args
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
    static function CBAjax_createAccount(
        stdClass $args
    ): stdClass {
        if (ColbyUser::getCurrentUserCBID() !== null) {
            return (object)[
                'cbmessage' => 'You are already signed in.',
            ];
        }

        $userEmail = CBModel::valueAsEmail(
            $args,
            'email'
        );

        if ($userEmail === null) {
            throw new CBExceptionWithValue(
                'The "email" argument is not valid.',
                $args,
                '1e0aa3c561175db1e58f22b70e4ee7630e6ebc3b'
            );
        }

        if (CBUser::emailToUserCBID($userEmail) !== null) {
            return (object)[
                'cbmessage' => <<<EOT

                    An account already exists using this email address.

                EOT,
            ];
        }

        $userFullName = trim(
            CBModel::valueToString(
                $args,
                'fullName'
            )
        );

        /**
         * @TODO 2020_01_03
         *
         *      Verify password strength.
         */

        $userPassword = CBModel::valueToString(
            $args,
            'password'
        );

        $userPasswordIssues = CBUser::passwordIssues($userPassword);

        if (
            $userPasswordIssues !== null
        ) {
            return (object)[
                'cbmessage' => CBMessageMarkup::stringToMessage(
                    $userPasswordIssues
                ),
            ];
        }

        $userPasswordHash = password_hash(
            $userPassword,
            PASSWORD_DEFAULT
        );

        $userSpec = (object)[
            'className' => 'CBUser',
            'ID' => CBID::generateRandomCBID(),
            'title' => $userFullName,
            'email' => $userEmail,
            'lastLoggedIn' => time(),
            'passwordHash' => $userPasswordHash,
        ];

        $countOfUsers = CBUsers::countOfUsers();

        CBModels::save($userSpec);

        if ($countOfUsers === 0) {
            CBUser::initializeFirstUser($userCBID);
        }

        ColbyUser::loginUser(
            $userSpec->ID
        );

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBAjax_createUser() */



    /**
     * @return string
     */
    static function CBAjax_createAccount_getUserGroupClassName(): string {
        return 'CBPublicUserGroup';
    }



    /**
     * @param object $args
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
        $email = CBModel::valueAsEmail(
            $args,
            'email'
        );

        if (
            $email === null
        ) {
            return (object)[
                'cbmessage' => 'Your email address is not valid.'
            ];
        }

        $password = CBModel::valueToString(
            $args,
            'password'
        );

        $userCBID = CBUser::emailToUserCBID($email);

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



    /**
     * @param object $args
     *
     *      {
     *          userCBID: CBID
     *      }
     */
    static function CBAjax_switchToUser(
        stdClass $args
    ): void {
        $userCBID = CBModel::valueAsCBID(
            $args,
            'userCBID'
        );

        if ($userCBID === null) {
            throw new CBExceptionWithValue(
                'The "userCBID" property is not valid.',
                $args,
                'f2bcfdc1b2911d797c4177d538ab8b3bfaccc0aa'
            );
        }

        $userModel = CBModels::fetchModelByIDNullable(
            $userCBID
        );

        if ($userModel->className !== 'CBUser') {
            throw new Exception('foo');
        }

        ColbyUser::loginUser(
            $userModel->ID
        );
    }
    /* CBAjax_switchToUser() */



    /**
     * @return string
     */
    static function CBAjax_switchToUser_getUserGroupClassName(): string {
        return 'CBDevelopersUserGroup';
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

        if ($email === null && $facebookUserID === null) {
            throw new CBExceptionWithValue(
                CBConvert::stringToCleanLine(<<<EOT

                    This spec must have at least a valid "email" or
                    "facebookUserID" property.

                EOT),
                $spec,
                '44f5f16eb31531b6f6caf00ca23fd6472f5f623c'
            );
        }


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


            /* @TODO I don't think this is always updated */

            'lastLoggedIn' => CBModel::valueAsInt(
                $spec,
                'lastLoggedIn'
            ) ?? 0,

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
                    facebookName
                )
                VALUES
                (
                    {$userCBIDAsSQL},
                    {$userEmailAsSQL},
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
