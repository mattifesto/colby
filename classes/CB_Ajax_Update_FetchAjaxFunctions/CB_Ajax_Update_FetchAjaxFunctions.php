<?php

final class
CB_Ajax_Update_FetchAjaxFunctions
{
    // -- CBAjax interfaces



    /**
     * @return [object]
     */
    static function
    CBAjax_execute(
    ): array
    {
        $arrayOfAjaxFunctions =
        [
            (object)
            [
                'CB_Ajax_Update_FetchAjaxFunctions_functionName_property' =>
                'CB_Ajax_Update_PHPComposer',
            ],
            (object)
            [
                'CB_Ajax_Update_FetchAjaxFunctions_functionName_property' =>
                'CB_Ajax_Update_General',
            ],
        ];

        return $arrayOfAjaxFunctions;
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
        if (
            $callingUserModelCBID ===
            null
        ) {
            return false;
        }

        $userIsAnAdministrator =
        CBUserGroup::userIsMemberOfUserGroup(
            $callingUserModelCBID,
            'CBDevelopersUserGroup'
        );

        return $userIsAnAdministrator;
    }
    // CBAjax_userModelCBIDCanExecute()

}
