<?php

final class CBUsersTestUsers_UserSettingsManager {

    /* -- CBUserSettingsManager interfaces -- -- -- -- -- */



    /**
     * @param CBID $targetUserID
     *
     * @return void
     */
    static function CBUserSettingsManager_render(string $targetUserID): void {
        echo '<div class="CBUsersTestUsers_UserSettingsManager">';

        $targetUserModel = CBModelCache::fetchModelByID($targetUserID);

        $targetUserNumericID = CBModel::valueAsInt(
            $targetUserModel,
            'userNumericID'
        );

        if ($targetUserNumericID === null) {
            throw new CBException(
                'The user numeric ID is not valid.',
                '',
                '456326a81b55cb54fc4c9fd874f19a06d1c242ca'
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
                'CBUsersTestUsers'
            );
        }

        echo '</div>';
    }
    /* CBUserSettingsManager_render() */

}
