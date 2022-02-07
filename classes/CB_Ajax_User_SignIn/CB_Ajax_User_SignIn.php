<?php

final class
CB_Ajax_User_SignIn {

    /* -- CBAjax interfaces -- */



    /**
     * @param object $executorArguments
     *
     *      {
     *          CB_Ajax_User_SignIn_emailAddress: string,
     *          CB_Ajax_User_SignIn_password: string,
     *          CB_Ajax_User_SignIn_shouldKeepSignedIn: bool,
     *      }
     *
     * @param CBID|null $callingUserModelCBID
     *
     * @return object
     *
     *      {
     *          CB_Ajax_User_SignIn_cbmessage: string,
     *
     *              This will only be returned if there is an issue.
     *      }
     */
    static function
    CBAjax_execute(
        stdClass $executorArguments,
        ?string $callingUserModelCBID = null
    ): stdClass {
        $aUserIsAlreadySignedIn = ColbyUser::getCurrentUserCBID() !== null;

        if (
            $aUserIsAlreadySignedIn
        ) {
            return (object)[
                'CB_Ajax_User_SignIn_cbmessage' => <<<EOT

                    A user is already signed in.

                EOT,
            ];
        }



        $emailAddress = CBModel::valueToString(
            $executorArguments,
            'CB_Ajax_User_SignIn_emailAddress'
        );

        $password = CBModel::valueToString(
            $executorArguments,
            'CB_Ajax_User_SignIn_password'
        );

        $shouldKeepSignedIn = CBModel::valueToBool(
            $executorArguments,
            'CB_Ajax_User_SignIn_shouldKeepSignedIn'
        );



        $result = CBUser::signIn(
            $emailAddress,
            $password,
            $shouldKeepSignedIn
        );

        if (
            isset($result->cbmessage)
        ) {
            return (object)[
                'CB_Ajax_User_SignIn_cbmessage' => $result->cbmessage,
            ];
        }



        return (object)[];
    }
    /* CBAjax_execute() */



    /**
     * @param CBID userModelCBID
     *
     * @return bool
     */
    static function
    CBAjax_userModelCBIDCanExecute(
        ?string $callingUserModelCBID = null
    ): bool {

        /**
         * Any user can attempt to sign in.
         */

        return true;
    }
    /* CBAjax_userModelCBIDCanExecute() */

}
