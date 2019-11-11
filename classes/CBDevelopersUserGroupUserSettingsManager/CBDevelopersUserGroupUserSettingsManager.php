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
     * @param string $targetUserID
     *
     * @return void
     */
    static function CBUserSettingsManager_render(string $targetUserID): void {
        echo '<div class="CBDevelopersUserGroupUserSettingsManager">';

        $targetUserModel = CBModelCache::fetchModelByID($targetUserID);

        $targetUserNumericID = CBModel::valueAsInt(
            $targetUserModel,
            'userNumericID'
        );

        if ($targetUserNumericID === null) {
            throw new CBException(
                'The user numeric ID is not valid.',
                '',
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
