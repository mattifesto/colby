<?php

final class CBDevelopersUserGroupUserSettingsManager {

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
     *          targetUserIsMemberOfUserGroup: bool
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
                    '12c5ca3809e0f6b4a86e899cc7bd6bc06d1618a2'
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

        $targetUserIsMemberOfUserGroup = CBUserGroup::userIsMemberOfUserGroup(
            $targetUserCBID,
            'CBDevelopersUserGroup'
        );

        return (object)[
            'currentUserCanChange' => true,

            'targetUserIsMemberOfUserGroup' => (
                $targetUserIsMemberOfUserGroup
            ),
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
            Colby::flexpath(__CLASS__, 'v571.js', cbsysurl()),
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
            'CBUIBooleanSwitchPart',
            'CBUIPanel',
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
        $currentUserIsADeveloper = CBUserGroup::userIsMemberOfUserGroup(
            ColbyUser::getCurrentUserCBID(),
            'CBDevelopersUserGroup'
        );

        return $currentUserIsADeveloper;
    }
    /* CBUserSettingsManager_currentUserCanViewForTargetUser() */

}
