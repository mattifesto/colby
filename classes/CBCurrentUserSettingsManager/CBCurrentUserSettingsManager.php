<?php

/**
 * This user settings manager shows settings only applicable when the current
 * user is viewing their own settings.
 */
final class CBCurrentUserSettingsManager {

    /* -- CBInstall interfaces -- -- -- -- -- */



    /**
     * @return void
     */
    static function CBInstall_install(): void {
        CBUserSettingsManagerCatalog::installUserSettingsManager(
            __CLASS__,
            1000
        );
    }



    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return [
            'CBUserSettingsManagerCatalog'
        ];
    }



    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v622.js', cbsysurl()),
        ];
    }



    /**
     * @return [[<name>, <value>]]
     */
    static function CBHTMLOutput_JavaScriptVariables(): array {
        $currentUserModel = CBModelCache::fetchModelByID(
            ColbyUser::getCurrentUserCBID()
        );

        $currentUserEmailAddress = CBModel::valueAsEmail(
            $currentUserModel,
            'email'
        );

        return [
            [
                'CBCurrentUserSettingsManager_currentUserEmailAddress',
                $currentUserEmailAddress
            ],
        ];
    }
    /* CBHTMLOutput_JavaScriptVariables() */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBConvert',
            'CBErrorHandler',
            'CBUI',
            'CBUser',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */



    /* -- CBUserSettingsManager interfaces -- -- -- -- -- */



    /**
     * @param CBID $targetUserCBID
     *
     * @return bool
     */
    static function CBUserSettingsManager_currentUserCanViewForTargetUser(
        string $targetUserCBID
    ): bool {
        if (ColbyUser::getCurrentUserCBID() === $targetUserCBID) {
            return true;
        } else {
            return false;
        }
    }
    /* CBUserSettingsManager_currentUserCanViewForTargetUser() */

}
