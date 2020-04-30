<?php

final class CBFullNameUserSettingsManager {

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
     *          fullName: string
     *      }
     */
    static function CBAjax_fetchFullName(
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
                '829e496c9402cab10109ddcc346da2043df09d1a'
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

        $targetUserFullName = CBModel::valueToString(
            $targetUserModel,
            'title'
        );

        return (object)[
            'targetUserFullName' => $targetUserFullName,
        ];
    }
    /* CBAjax_fetchFullName() */



    /**
     * @return string
     */
    static function CBAjax_fetchFullName_getUserGroupClassName(): string {
        return 'CBPublicUserGroup';
    }



    /**
     * @NOTE 2020_01_04
     *
     *      This is the only way that a user can update their name.
     *
     * @param object $args
     *
     *      {
     *          targetUserCBID: CBID
     *          targetUserFullName: string
     *      }
     *
     * @return void
     */
    static function CBAjax_updateFullName(
        stdClass $args
    ): void {
        $currentUserCBID = ColbyUser::getCurrentUserCBID();

        if ($currentUserCBID === null) {
            throw new CBExceptionWithValue(
                'You are not currently signed in.',
                $args,
                '06498193dfefff590fe3bdc87ae994a67b285ee3'
            );
        }

        $targetUserCBID = CBModel::valueAsCBID(
            $args,
            'targetUserCBID'
        );

        if ($currentUserCBID !== $targetUserCBID) {
            throw new CBExceptionWithValue(
                'A full name can only be changed by the user.',
                $args,
                'f5c981aea96460c6915bf089cfd297918f3d385d'
            );
        }

        $targetUserFullName = CBConvert::stringToCleanLine(
            CBModel::valueToString(
                $args,
                'targetUserFullName'
            )
        );

        CBModelUpdater::update(
            (object)[
                'ID' => $targetUserCBID,
                'title' => $targetUserFullName
            ]
        );
    }
    /* CBAjax_updateFullName() */



    /**
     * @return string
     */
    static function CBAjax_updateFullName_getUserGroupClassName(): string {
        return 'CBPublicUserGroup';
    }



    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v611.js', cbsysurl()),
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBException',
            'CBModel',
            'CBUI',
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
            5
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
