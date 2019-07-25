<?php

final class CBDevelopersUserGroupUserSettingsManager {

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        CBUserSettingsManagerCatalog::installUserSettingsManager(__CLASS__, 20);
    }

    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return ['CBUserSettingsManagerCatalog'];
    }


    /**
     * @param string $targetUserID
     *
     * @return void
     */
    static function CBUserSettingsManager_render(string $targetUserID): void {
        echo '<div class="CBDevelopersUserGroupUserSettingsManager">';

        $targetUserModel = CBModels::fetchModelByID($targetUserID);
        $targetUserNumericID = CBModel::valueAsInt($targetUserModel, 'userID');

        if (
            $targetUserNumericID !== null &&
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
