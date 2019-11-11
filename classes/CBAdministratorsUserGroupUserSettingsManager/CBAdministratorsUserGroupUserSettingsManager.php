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
     * @param string $targetUserID
     *
     * @return void
     */
    static function CBUserSettingsManager_render(string $targetUserID): void {
        echo '<div class="CBAdministratorsUserGroupUserSettingsManager">';

        $targetUserModel = CBModelCache::fetchModelByID($targetUserID);

        $targetUserNumericID = CBModel::valueAsInt(
            $targetUserModel,
            'userNumericID'
        );

        if ($targetUserNumericID === null) {
            throw new CBException(
                'The user numeric ID is not valid.',
                '',
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
