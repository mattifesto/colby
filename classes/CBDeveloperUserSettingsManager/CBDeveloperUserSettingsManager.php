<?php

final class CBDeveloperUserSettingsManager {

    /* -- CBAjax interfaces -- -- -- -- -- */



    /**
     * @param object $args
     *
     *      {
     *          userCBID: CBID
     *      }
     */
    static function CBAjax_switchToUser(
        stdClass $args
    ): void {
        $userCBID = CBModel::valueAsCBID(
            $args,
            'userCBID'
        );

        if ($userCBID === null) {
            throw new CBExceptionWithValue(
                'The "userCBID" property is not valid.',
                $args,
                'f2bcfdc1b2911d797c4177d538ab8b3bfaccc0aa'
            );
        }

        $userModel = CBModels::fetchModelByIDNullable(
            $userCBID
        );

        if ($userModel->className !== 'CBUser') {
            throw new CBExceptionWithValue(
                (
                    'The "className" property of this model was ' .
                    'expected to be "CBUser".'
                ),
                $userModel,
                '7a93bbce7a0aea1045864c6c7a0b61340e4bcac4'
            );
        }

        ColbyUser::loginUser(
            $userModel->ID
        );
    }
    /* CBAjax_switchToUser() */



    /**
     * @return string
     */
    static function CBAjax_switchToUser_getUserGroupClassName(): string {
        return 'CBDevelopersUserGroup';
    }



    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v610.js', cbsysurl()),
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
