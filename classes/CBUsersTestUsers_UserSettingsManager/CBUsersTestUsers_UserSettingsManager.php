<?php

final class CBUsersTestUsers_UserSettingsManager {

    /**
     * @param string $targetUserID
     *
     * @return void
     */
    static function CBUserSettingsManager_render(string $targetUserID): void {
        echo '<div class="CBUsersTestUsers_UserSettingsManager">';

        $targetUserModel = CBModels::fetchModelByID($targetUserID);

        $targetUserNumericID = CBModel::valueAsInt(
            $targetUserModel,
            'userID'
        );

        if (
            $targetUserNumericID !== null &&
            ColbyUser::currentUserIsMemberOfGroup('Developers')
        ) {
            $targetUserData = (object)[
                'id' => $targetUserNumericID,
            ];

            CBGroupUserSettings::renderUserSettings(
                $targetUserData,
                'CBUsersTestUsers'
            );
        }

        echo '</div>';
    }
    /* CBUserSettingsManager_render() */
}
