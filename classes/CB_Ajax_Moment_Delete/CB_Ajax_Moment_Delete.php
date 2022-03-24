<?php

final class
CB_Ajax_Moment_Delete
{
    // -- CBAjax interfaces



    /**
     * @param object $executorArguments
     * @param CBID|null $callingUserModelCBID
     *
     *      This parameter is used by internal callers, such as tests, to
     *      specify a user other than the current user. In true Ajax calls this
     *      value will always be null and the currenty logged in user will be
     *      used.
     */
    static function
    CBAjax_execute(
        stdClass $executorArguments,
        ?string $callingUserModelCBID = null
    ): void
    {
        if (
            $callingUserModelCBID === null
        ) {
            $callingUserModelCBID =
            ColbyUser::getCurrentUserCBID();
        }

        $momentModelCBID =
        CBModel::valueAsCBID(
            $executorArguments,
            'CB_Ajax_Moment_Delete_momentModelCBID'
        );

        if (
            $momentModelCBID === null
        ) {
            throw new CBExceptionWithValue(
                CBConvert::stringToCleanLine(<<<EOT

                    The value of CB_Ajax_Moment_Delete_momentModelCBID is not
                    valid.

                EOT),
                $executorArguments,
                '8223b56825f405324d10329b8247bff1a4212781'
            );
        }

        $momentModel =
        CBModels::fetchModelByCBID(
            $momentModelCBID
        );

        if (
            $momentModel === null ||
            CBModel::getClassName(
                $momentModel
            ) !== 'CB_Moment'
        ) {
            throw new CBExceptionWithValue(
                CBConvert::stringToCleanLine(<<<EOT

                    There is no moment model with the moment model CBID
                    provided.

                EOT),
                $executorArguments,
                '66f0ec8d473e9958ed2de96d99e2c4264053d0eb'
            );
        }

        $authorUserModelCBID =
        CB_Moment::getAuthorUserModelCBID(
            $momentModel
        );

        if (
            $authorUserModelCBID !== $callingUserModelCBID &&
            !CBUserGroup::userIsMemberOfUserGroup(
                $callingUserModelCBID,
                'CBAdministratorsUserGroup'
            )
        ) {
            $authorUserModelCBIDAsJSON = json_encode(
                $authorUserModelCBID
            );

            $callingUserModelCBIDAsJSON = json_encode(
                $callingUserModelCBID
            );

            throw new CBExceptionWithValue(
                CBConvert::stringToCleanLine(<<<EOT

                    The calling user model CBID, ${callingUserModelCBIDAsJSON}
                    does not match the moment author user model CBID,
                    ${authorUserModelCBIDAsJSON}.

                EOT),
                $executorArguments,
                'c6949e6ff495487f14521cce13bd857eed4e0c87'
            );
        }

        CBDB::transaction(
            function () use (
                $momentModelCBID
            ) {
                CBModels::deleteByID(
                    $momentModelCBID
                );
            }
        );
    }
    // CBAjax_execute()



    /**
     * @param CBID callingUserModelCBID
     *
     * @return bool
     */
    static function
    CBAjax_userModelCBIDCanExecute(
        ?string $callingUserModelCBID = null
    ): bool
    {
        $currentUserModelCBID =
        ColbyUser::getCurrentUserCBID();

        if (
            $currentUserModelCBID !== null
        ) {
            return true;
        }

        else {
            return false;
        }
    }
    // CBAjax_userModelCBIDCanExecute()

}
