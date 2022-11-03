<?php

/**
 * @BUG 2022_11_03_1667512785
 *
 *      This class does not have a valid class name. The P in perform should be
 *      capitalized.
 */
final class
CB_Ajax_ModelSearch_performSearch
{
    // -- CBAjax interfaces



    /**
     * @param object $executorArguments
     *
     *      {
     *          CB_Ajax_ModelSearch_performSearch_modelClassName: string,
     *          CB_Ajax_ModelSearch_performSearch_searchQuery: string,
     *      }
     *
     * @param CBID|null $callingUserModelCBID
     *
     * @return [object]
     */
    static function
    CBAjax_execute(
        stdClass $executorArguments,
        ?string $callingUserModelCBID = null
    ): array
    {
        $modelClassName =
        CBModel::valueToString(
            $executorArguments,
            'CB_Ajax_ModelSearch_performSearch_modelClassName'
        );

        $searchQuery =
        CBModel::valueToString(
            $executorArguments,
            'CB_Ajax_ModelSearch_performSearch_searchQuery'
        );

        $foundModelCBIDs =
        CBModels::fetchModelCBIDsBySearch(
            $searchQuery,
            $modelClassName,
        );

        $searchResults =
        CBModels::fetchAdministrativeSearchResults(
            $foundModelCBIDs
        );

        return
        $searchResults;
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
        return
        CBUserGroup::userIsMemberOfUserGroup(
            $callingUserModelCBID,
            'CBAdministratorsUserGroup'
        );
    }
    /* CBAjax_userModelCBIDCanExecute() */

}
