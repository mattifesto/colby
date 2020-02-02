<?php

final class CBAdministratorsUserGroupUserSettingsManager {

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
                    '094c7f4deb9ad2240023c74ccf91072648214eb6'
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
            'CBAdministratorsUserGroup'
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
            'CBUIBooleanSwitchPart',
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
            10
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
        $currentUserIsAnAdministrator = CBUserGroup::userIsMemberOfUserGroup(
            ColbyUser::getCurrentUserCBID(),
            'CBAdministratorsUserGroup'
        );

        return $currentUserIsAnAdministrator;
    }
    /* CBUserSettingsManager_currentUserCanViewForTargetUser() */

}
