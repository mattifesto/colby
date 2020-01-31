<?php

final class CBFacebookAccountUserSettingsManager {

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
     *
     *              This property will be set to true if the current user is not
     *              allowed to access this information about the target user.
     *
     *          facebookName: string|null
     *
     *              This property will be set to null if the current user does
     *              not have a Facebook account associated with their user
     *              account.
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
            throw new CBExceptionWithValue(
                'The "targetUserCBID" argument is not valid.',
                $args,
                '9a1d68f9242ed271b73d98d8f22a3dc58d248140'
            );
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

        $targetUserFacebookUserID = CBModel::valueAsInt(
            $targetUserModel,
            'facebookUserID'
        );

        if ($targetUserFacebookUserID === null) {
            $targetUserFacebookName = null;
        } else {
            $targetUserFacebookName = CBModel::valueToString(
                $targetUserModel,
                'facebookName'
            );
        }

        return (object)[
            'facebookName' => $targetUserFacebookName,
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
            Colby::flexpath(__CLASS__, 'v569.js', cbsysurl()),
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
            20
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
