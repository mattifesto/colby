<?php

final class CBUser_PotentialUser {

    /* -- CBAjax interfaces -- -- -- -- -- */



    /**
     * This function creates a CBUser_PotentialUser model and sends an email
     * with a one time password that can be used to convert the
     * CBUser_PotentialUser model into a CBUser model.
     *
     * @param object $args
     *
     *      {
     *          email: string
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
    static function CBAjax_create(
        stdClass $args
    ): stdClass {
        if (ColbyUser::getCurrentUserCBID() !== null) {
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

        $potentialUserSpec = (object)[
            'ID' => $potentialUserCBID,
            'className' => 'CBUser_PotentialUser',
            'email' => $emailAddress,
            'fullName' => $fullName,
            'oneTimePassword' => $oneTimePassword,
            'passwordHash' => $passwordHash,
        ];

        CBDB::transaction(
            function () use ($potentialUserSpec) {
                CBModels::save($potentialUserSpec);
            }
        );

        $cbmessage = <<<EOT

            code {$oneTimePassword}

        EOT;

        CBEmail::sendCBMessage(
            $emailAddress,
            $fullName,
            'OTP',
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

        $potentialUserModelOneTimePassword = CBModel::valueToString(
            $potentialUserModel,
            'oneTimePassword'
        );

        $oneTimePassword = CBModel::valueToString(
            $args,
            'oneTimePassword'
        );

        if ($oneTimePassword !== $potentialUserModelOneTimePassword) {
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
                'email'
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

        $countOfUsers = CBUsers::countOfUsers();

        if ($countOfUsers === 0) {
            CBUser::initializeFirstUser($userCBID);
        }

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
        $oneTimePassword = CBModel::valueToString(
            $spec,
            'oneTimePassword'
        );

        // @TODO validate one time password

        $passwordHash = CBModel::valueToString(
            $spec,
            'passwordHash'
        );

        return (object)[
            'email' => CBModel::valueToString(
                $spec,
                'email'
            ),

            'fullName' => trim(
                CBModel::valueToString(
                    $spec,
                    'fullName'
                )
            ),

            'oneTimePassword' => $oneTimePassword,

            'passwordHash' => $passwordHash,
        ];
    }
    /* CBModel_build() */

}
