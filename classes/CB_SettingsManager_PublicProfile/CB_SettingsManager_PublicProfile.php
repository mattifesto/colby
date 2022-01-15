<?php

final class
CB_SettingsManager_PublicProfile {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs(
    ): array {
        return [
            Colby::flexpath(
                __CLASS__,
                'v675.38.js',
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
            'CB_Brick_OnOff',
            'CB_Brick_Padding10',
            'CB_Brick_TextContainer',
            'CBAjax',
            'CBConvert',
            'CBErrorHandler',
            'CBException',
            'CBModel',
            'CBUI',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */



    /* -- CBInstall interfaces -- -- -- -- -- */



    /**
     * @return void
     */
    static function
    CBInstall_install(
    ): void {
        CBUserSettingsManagerCatalog::installUserSettingsManager(
            __CLASS__,
            8
        );
    }
    /* CBInstall_install() */



    /**
     * @return [string]
     */
    static function
    CBInstall_requiredClassNames(
    ): array {
        return [
            'CBUserSettingsManagerCatalog',
        ];
    }
    /* CBInstall_requiredClassNames() */



    /* -- CBUserSettingsManager interfaces -- -- -- -- -- */



    /**
     * @param CBID $targetUserCBID
     *
     * @return bool
     */
    static function
    CBUserSettingsManager_currentUserCanViewForTargetUser(
        string $targetUserCBID
    ): bool {
        $currentUserIsAnAdministrator = CBUserGroup::userIsMemberOfUserGroup(
            ColbyUser::getCurrentUserCBID(),
            'CBAdministratorsUserGroup'
        );

        return $currentUserIsAnAdministrator;
    }
    /* CBUserSettingsManager_currentUserCanViewForTargetUser() */

}
