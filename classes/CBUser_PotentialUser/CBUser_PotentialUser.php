<?php

final class
CBUser_PotentialUser {

    /* -- CBAjax interfaces -- -- -- -- -- */



    /**
     * This function creates a CBUser_PotentialUser model and sends an email
     * with a one time password that can be used to convert the
     * CBUser_PotentialUser model into a CBUser model.
     *
     * @param object $args
     *
     *      {
     *          emailAddress: string
     *          fullName: string
     *          password1: string
     *          password2: string
     *      }
     *
     * @return object
     *
     *      {
     *          succeeded: bool
     *          cbmessage: string
     *
     *              This will only be used if succeeded is false.
     *      }
     */
    static function
    CBAjax_create(
        stdClass $args
    ): stdClass {
        if (
            ColbyUser::getCurrentUserCBID() !== null
        ) {
            return (object)[
                'cbmessage' => 'You are already signed in.',
            ];
        }

        $emailAddress = CBModel::valueAsEmail(
            $args,
            'emailAddress'
        );

        if ($emailAddress === null) {
            return (object)[
                'cbmessage' => <<<EOT

                    The email address is not valid.

                EOT,
            ];
        }

        if (CBUser::emailToUserCBID($emailAddress) !== null) {
            return (object)[
                'cbmessage' => <<<EOT

                    An account already exists using this email address.

                EOT,
            ];
        }

        $fullName = trim(
            CBModel::valueToString(
                $args,
                'fullName'
            )
        );

        $password1 = CBModel::valueToString(
            $args,
            'password1'
        );

        $passwordIssues = CBUser::passwordIssues($password1);

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

        if ($password2 !== $password1) {
            return (object)[
                'cbmessage' => <<<EOT

                    Your passwords don't match.

                EOT,
            ];
        }

        $passwordHash = password_hash(
            $password1,
            PASSWORD_DEFAULT
        );

        if ($passwordHash === false) {
            return (object)[
                'cbmessage' => 'An error occured while hashing your password.',
            ];
        }

        $potentialUserCBID = CBID::generateRandomCBID();

        $oneTimePassword = CBOneTimePassword::generate();

        $oneTimePasswordHash = password_hash(
            $oneTimePassword,
            PASSWORD_DEFAULT
        );

        if ($oneTimePasswordHash === false) {
            throw new CBException(
                'An error occurred while hashing a one time password',
                '',
                'bb7f2b075baf3952ecc60fae00664f2d55546b2b'
            );
        }

        $potentialUserSpec = (object)[
            'ID' => $potentialUserCBID,
            'className' => 'CBUser_PotentialUser',
            'emailAddress' => $emailAddress,
            'fullName' => $fullName,
            'oneTimePasswordHash' => $oneTimePasswordHash,
            'passwordHash' => $passwordHash,
            'timestamp' => time(),
        ];

        CBDB::transaction(
            function () use ($potentialUserSpec) {
                CBModels::save($potentialUserSpec);
            }
        );

        $siteName = CBSitePreferences::siteName();

        $cbmessage = <<<EOT

            The one time password that will allow you to create a user account
            on the {$siteName} website for this email address is:

            --- blockquote
            ({$oneTimePassword} (code))
            ---

        EOT;

        CBEmail::sendCBMessage(
            $emailAddress,
            $fullName,
            "{$siteName} One Time Password",
            $cbmessage
        );

        return (object)[
            'succeeded' => true,
            'potentialUserCBID' => $potentialUserCBID,
        ];
    }
    /* CBAjax_createPotentialUser() */



    /**
     * @return string
     */
    static function CBAjax_create_getUserGroupClassName(): string {
        return 'CBPublicUserGroup';
    }



    /**
     * @param object $args
     *
     *      {
     *          potentialUserCBID: CBID
     *          oneTimePassword: string
     *      }
     *
     * @return object
     *
     *      {
     *          succeeded: bool
     *          cbmessage: string
     *
     *              This will only be used if succeeded is false.
     *      }
     */
    static function CBAjax_verify(
        stdClass $args
    ): stdClass {
        $potentialUserCBID = CBModel::valueAsCBID(
            $args,
            'potentialUserCBID'
        );

        if ($potentialUserCBID === null) {
            throw new CBException(
                'The "potentialUserCBID" argument is not valid.',
                '',
                '6edcb18ea869e4083aa924bc6d68df53486f4be6'
            );
        }

        $potentialUserModel = CBModelCache::fetchModelByID(
            $potentialUserCBID
        );

        if ($potentialUserModel === null) {
            throw new CBException(
                'The CBUser_PotentialUser model does not exist.',
                '',
                'f1c99add8e8c2ed9c9a8202fcc96f92b95f3171f'
            );
        }

        $oneTimePasswordHash = CBModel::valueToString(
            $potentialUserModel,
            'oneTimePasswordHash'
        );

        $oneTimePassword = CBModel::valueToString(
            $args,
            'oneTimePassword'
        );

        $oneTimePasswordIsVerified = password_verify(
            $oneTimePassword,
            $oneTimePasswordHash
        );

        if ($oneTimePasswordIsVerified !== true) {
            return (object)[
                'cbmessage' => 'The one time password is not correct.',
            ];
        }

        $userCBID = CBID::generateRandomCBID();

        $userSpec = (object)[
            'className' => 'CBUser',

            'ID' => $userCBID,

            'email' => CBModel::valueToString(
                $potentialUserModel,
                'emailAddress'
            ),

            'title' => CBModel::valueToString(
                $potentialUserModel,
                'fullName'
            ),

            'passwordHash' => CBModel::valueToString(
                $potentialUserModel,
                'passwordHash'
            ),
        ];

        CBDB::transaction(
            function () use ($userSpec, $potentialUserCBID) {
                CBModels::save($userSpec);
                CBModels::deleteByID($potentialUserCBID);
            }
        );

        ColbyUser::loginUser(
            $userCBID
        );

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBAjax_verifyPotentialUser() */



    /**
     * @return string
     */
    static function CBAjax_verify_getUserGroupClassName(): string {
        return 'CBPublicUserGroup';
    }



    /* -- CBModel interfaces -- -- -- -- -- */



    /**
     * @param object $spec
     *
     * @return object
     */
    static function CBModel_build(
        stdClass $spec
    ): stdClass {
        $emailAddress = CBModel::valueAsEmail(
            $spec,
            'emailAddress'
        );

        if ($emailAddress === null) {
            throw new CBExceptionWithValue(
                CBConvert::stringToCleanLine(<<<EOT

                    The "emailAddress" property on this spec is not valid.

                EOT),
                $spec,
                '3fd11a88b7ff8db85ae58fef6f561948388f95cf'
            );
        }

        $oneTimePasswordHash = CBModel::valueToString(
            $spec,
            'oneTimePasswordHash'
        );

        if ($oneTimePasswordHash === '') {
            throw new CBExceptionWithValue(
                CBConvert::stringToCleanLine(<<<EOT

                    The "oneTimePasswordHash" property on this spec is not
                    valid.

                EOT),
                $spec,
                '10f7c52580542a6bf92a52c769b2479fba8dbb67'
            );
        }

        $passwordHash = CBModel::valueToString(
            $spec,
            'passwordHash'
        );

        if ($passwordHash === '') {
            throw new CBExceptionWithValue(
                CBConvert::stringToCleanLine(<<<EOT

                    The "passwordHash" property on this spec is not valid.

                EOT),
                $spec,
                'a182862901bd2b50cdf6391f56e636c1f791a278'
            );
        }

        $timestamp = CBModel::valueAsInt(
            $spec,
            'timestamp'
        );

        if ($timestamp === null) {
            throw new CBExceptionWithValue(
                'The "timestamp" property on this spec is not valid.',
                $spec,
                '8345bd7a9ac4beaa460c91faaf8f762674c9118a'
            );
        }

        return (object)[
            'emailAddress' => $emailAddress,

            'fullName' => trim(
                CBModel::valueToString(
                    $spec,
                    'fullName'
                )
            ),

            'oneTimePasswordHash' => $oneTimePasswordHash,

            'passwordHash' => $passwordHash,

            'timestamp' => $timestamp,
        ];
    }
    /* CBModel_build() */

}
