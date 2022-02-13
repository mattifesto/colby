<?php

final class
CB_Ajax_StandardPageFrame_SetLeftSidebarPage {

    /* -- CBAjax interfaces -- */



    /**
     * @param object $executorArguments
     *
     *      {
     *          CB_Ajax_StandardPageFrame_SetLeftSidebarPage_pageModelCBID: string,
     *      }
     *
     * @param CBID|null $callingUserModelCBID
     *
     * @return void
     */
    static function
    CBAjax_execute(
        stdClass $executorArguments,
        ?string $callingUserModelCBID = null
    ): void {
        $pageModelCBID = CBModel::valueAsCBID(
            $executorArguments,
            'CB_Ajax_StandardPageFrame_SetLeftSidebarPage_pageModelCBID'
        );

        if (
            $pageModelCBID === null
        ) {
            // TODO throw exception
        }

        CB_StandardPageFrame::setLeftSidebarPageModelCBID(
            $pageModelCBID
        );
    }
    /* CBAjax_execute() */



    /**
     * @param CBID callingUserModelCBID
     *
     * @return bool
     */
    static function
    CBAjax_userModelCBIDCanExecute(
        ?string $callingUserModelCBID = null
    ): bool {
        $userIsAnAdministrator = (
            $callingUserModelCBID !== null &&
            CBUserGroup::userIsMemberOfUserGroup(
                $callingUserModelCBID,
                'CBAdministratorsUserGroup'
            )
        );

        return $userIsAnAdministrator;
    }
    /* CBAjax_userModelCBIDCanExecute() */

}
