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

        $userHasAuthority = CBUserGroup::userIsMemberOfUserGroup(
            ColbyUser::getCurrentUserCBID(),
            'CBDevelopersUserGroup'
        );

        if ($userHasAuthority) {
            CBView::render(
                (object)[
                    'className' => 'CBUserGroupMembershipToggleView',
                    'userCBID' => $targetUserCBID,
                    'userGroupClassName' => 'CBDevelopersUserGroup',
                ]
            );
        }

        echo '</div>';
    }
    /* CBUserSettingsManager_render() */

}
