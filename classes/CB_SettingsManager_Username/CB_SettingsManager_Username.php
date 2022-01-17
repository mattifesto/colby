<?php

final class
CB_SettingsManager_Username {

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
                'v675.50.js',
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
            'CB_Brick_Button',
            'CB_Brick_HorizontalBar10',
            'CB_Brick_KeyValue',
            'CB_Brick_Padding10',
            'CB_Brick_Text',
            'CB_Brick_TextContainer',
            'CBAjax',
            'CBContentStyleSheet',
            'CBConvert',
            'CBErrorHandler',
            'CBException',
            'CBModel',
            'CBUINavigationView',
            'CBUI',
            'CBUIStringEditor2',
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
            1
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
        if (
            ColbyUser::getCurrentUserCBID() === $targetUserCBID
        ) {
            return true;
        }

        $currentUserIsAnAdministrator = CBUserGroup::userIsMemberOfUserGroup(
            ColbyUser::getCurrentUserCBID(),
            'CBAdministratorsUserGroup'
        );

        return $currentUserIsAnAdministrator;
    }
    /* CBUserSettingsManager_currentUserCanViewForTargetUser() */

}
