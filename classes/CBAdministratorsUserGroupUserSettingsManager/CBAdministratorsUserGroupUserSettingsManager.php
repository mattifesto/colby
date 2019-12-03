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

        $userHasAuthority = CBUserGroup::userIsMemberOfUserGroup(
            ColbyUser::getCurrentUserCBID(),
            'CBAdministratorsUserGroup'
        );

        if ($userHasAuthority) {
            CBView::render(
                (object)[
                    'className' => 'CBUserGroupMembershipToggleView',
                    'userCBID' => $targetUserCBID,
                    'userGroupClassName' => 'CBAdministratorsUserGroup',
                ]
            );
        }

        echo '</div>';
    }
    /* CBUserSettingsManager_render() */

}
