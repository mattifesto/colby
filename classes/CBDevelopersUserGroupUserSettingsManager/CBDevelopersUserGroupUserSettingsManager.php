<?php

final class CBDevelopersUserGroupUserSettingsManager {

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
                'bcef3acf68826a2c22a8ff55c4b72de6be6fa57e'
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
                '63ef0218d057cb05c5e1bbf44c8076800c70842e'
            );
        }

        if (
            ColbyUser::currentUserIsMemberOfGroup('Developers')
        ) {
            $targetUserData = (object)[
                'id' => $targetUserNumericID,
            ];

            CBGroupUserSettings::renderUserSettings(
                $targetUserData,
                'Developers'
            );
        }

        echo '</div>';
    }
    /* CBUserSettingsManager_render() */

}
