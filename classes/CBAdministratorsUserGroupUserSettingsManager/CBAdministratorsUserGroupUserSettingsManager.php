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
     * @param ID $targetUserID
     *
     * @return void
     */
    static function CBUserSettingsManager_render(string $targetUserID): void {
        $targetUserModel = CBModels::fetchModelByID($targetUserID);
        $targetUserNumber = CBModel::valueAsInt($targetUserModel, 'userID');

        if (empty($targetUserNumber)) {
            return;
        }

        $targetUserData = (object)[
            'id' => $targetUserNumber,
        ];

        if (ColbyUser::currentUserIsMemberOfGroup('Administrators')) {
            CBGroupUserSettings::renderUserSettings($targetUserData, 'Administrators');
        }
    }
}
