<?php

final class CBAdministratorsUserGroupUserSettingsManager {

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        CBUserSettingsManagerCatalog::installUserSettingsManager(__CLASS__, 10);
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
        echo '<div class="CBAdministratorsUserGroupUserSettingsManager">';

        $targetUserModel = CBModels::fetchModelByID($targetUserID);
        $targetUserNumericID = CBModel::valueAsInt($targetUserModel, 'userID');

        if (
            $targetUserNumericID !== null &&
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
