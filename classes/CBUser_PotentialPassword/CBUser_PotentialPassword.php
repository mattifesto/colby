<?php

final class CBUser_PotentialPassword {

    /* -- CBAjax interfaces -- -- -- -- -- */



    /**
     * @param object $args
     *
     *      {
     *          emailAddress: string
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

        $userCBID = CBUser::emailToUserCBID($emailAddress);

        if ($userCBID === null) {
            return (object)[
                'cbmessage' => <<<EOT

                    There is no user account with the email address.

                EOT,
            ];
        }

        $userModel = CBModelCache::fetchModelByID(
            $userCBID
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

        $potentialPasswordCBID = CBID::generateRandomCBID();

        $oneTimePassword = CBOneTimePassword::generate();

        $oneTimePasswordHash = password_hash(
            $oneTimePassword,
            PASSWORD_DEFAULT
        );

        if ($oneTimePasswordHash === false) {
            throw new CBException(
                'An error occurred while hashing a one time password',
                '',
                'ce70aa72f5e17c3766c7a7c79a4f8063487cfc1d'
            );
        }

        $potentialPasswordSpect = (object)[
            'className' => 'CBUser_PotentialPassword',
            'ID' => $potentialPasswordCBID,
            'emailAddress' => $emailAddress,
            'oneTimePasswordHash' => $oneTimePasswordHash,
            'passwordHash' => $passwordHash,
            'timestamp' => time(),
        ];

        CBDB::transaction(
            function () use ($potentialPasswordSpect) {
                CBModels::save($potentialPasswordSpect);
            }
        );

        $cbmessage = <<<EOT

            The one time password that will allow you to change the password on
            the account for this email address is:

            --- blockquote
            {$oneTimePassword}
            ---

        EOT;

        $fullName = CBModel::valueToString(
            $userModel,
            'title'
        );

        CBEmail::sendCBMessage(
            $emailAddress,
            $fullName,
            'OTP',
            $cbmessage
        );

        return (object)[
            'succeeded' => true,
            'potentialPasswordCBID' => $potentialPasswordCBID,
        ];
    }
    /* CBAjax_create() */



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
     *          potentialPasswordCBID: CBID
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
        $potentialPasswordCBID = CBModel::valueAsCBID(
            $args,
            'potentialPasswordCBID'
        );

        if ($potentialPasswordCBID === null) {
            throw new CBException(
                'The "potentialPasswordCBID" argument is not valid.',
                '',
                '74abd58b1f6b65de4c60d3219924ba438cadff48'
            );
        }

        $potentialPasswordModel = CBModelCache::fetchModelByID(
            $potentialPasswordCBID
        );

        if ($potentialPasswordModel === null) {
            throw new CBException(
                'The CBUser_PotentialPassword model does not exist.',
                '',
                '15e4ef03e2bdad58a547343a464f2e76e991171a'
            );
        }

        $oneTimePasswordHash = CBModel::valueToString(
            $potentialPasswordModel,
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

        $emailAddress = CBModel::valueAsEmail(
            $potentialPasswordModel,
            'emailAddress'
        );

        if ($emailAddress === null) {
            throw new CBExceptionWithValue(
                CBConvert::stringToCleanLine(<<<EOT

                    The "emailAddress" property of this CBUser_PotentialPassword
                    model is not valid.

                EOT),
                $potentialPasswordModel,
                'f8332897a58509efe44747dab41c600db18f0746'
            );
        }

        $userCBID = CBUser::emailToUserCBID($emailAddress);

        if ($userCBID === null) {
            throw CBExceptionWithValue(
                CBConvert::stringToCleanLine(<<<EOT

                    No user exists for the "emailAddress" property on this
                    CBUser_PotentialPassword model.

                EOT),
                $potentialPasswordModel,
                '179a4b86bc71f0d5cfd3395aabc2f63077be874b'
            );
        }

        $userSpec = CBModels::fetchSpecByIDNullable(
            $userCBID
        );

        $userSpec->passwordHash = CBModel::valueToString(
            $potentialPasswordModel,
            'passwordHash'
        );

        CBDB::transaction(
            function () use ($userSpec, $potentialPasswordCBID) {
                CBModels::save($userSpec);
                CBModels::deleteByID($potentialPasswordCBID);
            }
        );

        ColbyUser::loginUser(
            $userCBID
        );

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBAjax_verify() */



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
                'The "emailAddress" property on this spec is not valid.',
                $spec,
                'e803eed89e29b64794b49690599c873f841f889b'
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
                'a997dfd837a9222c98e185254778e035dd4ac062'
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
                '47f3aa0bc82dc5e797ab3e9b9115c52b3a6942cb'
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
                'a701f1ac003ed63979cd90d346c2b068a85d3268'
            );
        }

        return (object)[
            'emailAddress' => $emailAddress,

            'oneTimePasswordHash' => $oneTimePasswordHash,

            'passwordHash' => $passwordHash,

            'timestamp' => $timestamp,
        ];
    }
    /* CBModel_build() */

}
