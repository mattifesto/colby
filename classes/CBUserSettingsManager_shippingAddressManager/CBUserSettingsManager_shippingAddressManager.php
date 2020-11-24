<?php

final class CBUserSettingsManager_shippingAddressManager {

    /* -- CBHTMLOutput interfaces -- */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs(
    ): array {
        return [
            Colby::flexpath(
                __CLASS__,
                'v671.js',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_requiredClassNames(
    ): array {
        return [
            'CBUI',
            'CBUINavigationView',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */



    /* -- CBInstall interfaces -- */



    /**
     * @return void
     */
    static function
    CBInstall_install(
    ): void {
        CBUserSettingsManagerCatalog::installUserSettingsManager(
            __CLASS__,
        );
    }
    /* CBInstall_install() */



    /* -- CBUserSettingsManager interfaces -- */



    /**
     * @NOTE 2020_11_23
     *
     *      This class is currently in development. For now, only administrators
     *      can see the user interface, but eventually all users will be able to
     *      see the user interface for their own user account.
     *
     * @param CBID $targetUserCBID
     *
     * @return bool
     */
    static function
    CBUserSettingsManager_currentUserCanViewForTargetUser(
        string $targetUserCBID
    ): bool {
        $currentUserCBID = ColbyUser::getCurrentUserCBID();

        /*
        if ($targetUserCBID === $currentUserCBID) {
            return true;
        }
        */

        $currentUserIsAnAdministrator = CBUserGroup::userIsMemberOfUserGroup(
            $currentUserCBID,
            'CBAdministratorsUserGroup'
        );

        return $currentUserIsAnAdministrator;
    }
    /* CBUserSettingsManager_currentUserCanViewForTargetUser() */

}
