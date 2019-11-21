<?php

final class CBAdministratorsUserGroupUserSettingsManager {

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
     * @return void
     */
    static function CBUserSettingsManager_render(
        string $targetUserCBID
    ): void {
        echo '<div class="', __CLASS__, '">';

        $targetUserModel = CBModelCache::fetchModelByID(
            $targetUserCBID
        );

        if (
            $targetUserModel === null ||
            $targetUserModel->className !== 'CBUser'
        ) {
            throw new CBExceptionWithValue(
                'There is no CBUser model for this user CBID',
                (object)[
                    'user CBID' => $targetUserCBID,
                    'model' => $targetUserModel,
                ],
                '778f9adfef1891c4a9229a384d6f077a3bcf7001'
            );
        }

        $targetUserNumericID = CBModel::valueAsInt(
            $targetUserModel,
            'userNumericID'
        );

        if ($targetUserNumericID === null) {
            throw new CBExceptionWithValue(
                'The "userNumericID" property of this user model is invalid.',
                $targetUserModel,
                '95ef916ff7322d7d5cf441967bec15dae9324fe9'
            );
        }

        if (
            ColbyUser::currentUserIsMemberOfGroup('Administrators')
        ) {
            $targetUserData = (object)[
                'id' => $targetUserNumericID,
            ];

            CBGroupUserSettings::renderUserSettings(
                $targetUserData,
                'Administrators'
            );
        }

        echo '</div>';
    }
    /* CBUserSettingsManager_render() */

}
