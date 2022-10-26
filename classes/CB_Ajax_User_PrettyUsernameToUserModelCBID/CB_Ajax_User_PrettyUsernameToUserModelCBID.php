<?php

final class
CB_Ajax_User_PrettyUsernameToUserModelCBID
{
    // -- CBAjax interfaces



    /**
     * @param object $executorArguments
     *
     *      {
     *          CB_Ajax_User_PrettyUsernameToUserModelCBID_prettyUsername_argument:
     *          <string>
     *      }
     *
     * @param CBID|null $callingUserModelCBID
     *
     * @return object
     *
     *      {
     *          CB_Ajax_User_PrettyUsernameToUserModelCBID_userModelCBID:
     *          <CBID>|null
     *      }
     */
    static function
    CBAjax_execute(
        stdClass $executorArguments,
        ?string $callingUserModelCBID = null
    ): stdClass
    {
        $prettyUsername =
        CBModel::valueToString(
            $executorArguments,
            'CB_Ajax_User_PrettyUsernameToUserModelCBID_prettyUsername_argument'
        );

        $prettyUsernameIsValid =
        CB_Username::isPrettyUsernameValid(
            $prettyUsername
        );

        if (
            $prettyUsernameIsValid
        ) {
            $userModelCBID =
            CBUser::prettyUsernameToUserModelCBID(
                $prettyUsername
            );
        }

        else
        {
            $userModelCBID =
            null;
        }

        $returnValue =
        (object)
        [
            'CB_Ajax_User_PrettyUsernameToUserModelCBID_userModelCBID' =>
            $userModelCBID,
        ];

        return $returnValue;
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
        return true;
    }
    // CBAjax_userModelCBIDCanExecute()

}
