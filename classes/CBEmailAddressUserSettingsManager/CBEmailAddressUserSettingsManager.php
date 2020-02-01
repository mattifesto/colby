<?php

final class CBEmailAddressUserSettingsManager {

    /* -- CBAjax interfaces -- -- -- -- -- */



    /**
     * @param object $args
     *
     *      {
     *          targetUserCBID: CBID
     *      }
     *
     * @return object
     *
     *      {
     *          accessWasDenied: bool
     *          currentUserCanChange: bool
     *          targetUserEmailAddress: string
     *      }
     */
    static function CBAjax_fetchTargetUserData(
        stdClass $args
    ): stdClass {
        $targetUserCBID = CBModel::valueAsCBID(
            $args,
            'targetUserCBID'
        );

        if ($targetUserCBID === null) {
            if ($targetUserCBID === null) {
                throw new CBExceptionWithValue(
                    'The "targetUserCBID" argument is not valid.',
                    $args,
                    'f028e5151f58ebe4486153fc12875ce1a9545d8f'
                );
            }
        }

        $canView = CBUserSettingsManager::currentUserCanViewForTargetUser(
            __CLASS__,
            $targetUserCBID
        );

        if ($canView !== true) {
            return (object)[
                'accessWasDenied' => true,
            ];
        }

        $targetUserModel = CBModelCache::fetchModelByID(
            $targetUserCBID
        );

        $targetUserEmailAddress = CBModel::valueToString(
            $targetUserModel,
            'email'
        );

        return (object)[
            'currentUserCanChange' => (
                ColbyUser::getCurrentUserCBID() === $targetUserCBID
            ),

            'targetUserEmailAddress' => $targetUserEmailAddress,
        ];
    }
    /* CBAjax_fetchTargetUserData() */



    /**
     * @return string
     */
    static function CBAjax_fetchTargetUserData_getUserGroupClassName(): string {
        return 'CBPublicUserGroup';
    }



    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v570.js', cbsysurl()),
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBErrorHandler',
            'CBException',
            'CBModel',
            'CBUI',
            'CBUINavigationView',
            'CBUIPanel',
            'CBUIStringEditor',
            'Colby',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */



    /* -- CBInstall interfaces -- -- -- -- -- */



    /**
     * @return void
     */
    static function CBInstall_install(): void {
        CBUserSettingsManagerCatalog::installUserSettingsManager(
            __CLASS__,
            6
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
        }

        $currentUserIsAnAdministrator = CBUserGroup::userIsMemberOfUserGroup(
            ColbyUser::getCurrentUserCBID(),
            'CBAdministratorsUserGroup'
        );

        return $currentUserIsAnAdministrator;
    }
    /* CBUserSettingsManager_currentUserCanViewForTargetUser() */

}
