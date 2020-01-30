<?php

final class CBAdminPageForUserSettings {

    /* -- CBAdmin interfaces -- -- -- -- -- */



    /**
     * @return string
     */
    static function CBAdmin_getUserGroupClassName(): string {
        return 'CBAdministratorsUserGroup';
    }



    /**
     * @return [string]
     */
    static function CBAdmin_menuNamePath(): array {
        return [
            'general',
            'users',
        ];
    }
    /* CBAdmin_menuNamePath() */



    /**
     * @return void
     */
    static function CBAdmin_render(): void {
        $targetUserCBID = $_GET['hash'];

        $userModel = CBModelCache::fetchModelByID(
            $targetUserCBID
        );

        if ($userModel === null) {
            CBAdminPageForUserSettings::CBAdmin_render_notFound(
                $targetUserCBID
            );

            return;
        }

        CBAdminPageForUserSettings::CBAdmin_render_user(
            $userModel
        );

        CBUserSettingsManagerCatalog::renderUserSettingsManagerViews(
            $targetUserCBID
        );
    }
    /* CBAdmin_render() */



    /**
     * @param string $targetUserID
     *
     * @return void
     */
    static function CBAdmin_render_notFound(
        string $targetUserID
    ): void {
        $targetUserIDAsMessage = CBMessageMarkup::stringToMessage(
            $targetUserID
        );

        ?>

        <div class="CBAdminPageForUserSettings_notFound">

            <?php

            CBView::renderSpec(
                (object)[
                    'className' => 'CBMessageView',
                    'markup' => <<<EOT

                        There is no user with the ID
                        {$targetUserIDAsMessage}.

                    EOT,
                ]
            );

            ?>

        </div>

        <?php
    }
    /* CBAdmin_render_notFound() */



    /**
     * @param object $userModel
     *
     * @return void
     */
    private static function CBAdmin_render_user(
        stdClass $userModel
    ): void {
        $userTitle = CBModel::valueToString(
            $userModel,
            'title'
        );

        CBHTMLOutput::pageInformation()->title = (
            "User Administration ({$userTitle})"
        );

        $userFacebookID = CBModel::valueToString(
            $userModel,
            'facebookUserID'
        );

        $userPhotoURL = CBFacebook::userImageURL($userFacebookID);

        ?>

        <div class="CBAdminPageForUserSettings_user">
            <img src="<?= $userPhotoURL ?>">
            <div><?= $userTitle ?></div>
        </div>

        <?php
    }
    /* CBAdmin_render_user() */



    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v545.css', cbsysurl()),
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v569.js', cbsysurl()),
        ];
    }



    /**
     * @return [[<name>, <value>]]
     */
    static function CBHTMLOutput_JavaScriptVariables(): array {
        $targetUserCBID = cb_query_string_value('hash');

        return [
            [
                'CBAdminPageForUserSettings_currentUserIsDeveloper',
                CBUserGroup::userIsMemberOfUserGroup(
                    ColbyUser::getCurrentUserCBID(),
                    'CBDevelopersUserGroup'
                ),
            ],
            [
                'CBAdminPageForUserSettings_userCBID',
                $targetUserCBID,
            ],
            [
                'CBAdminPageForUserSettings_userSettingsManagerClassNames',
                CBUserSettingsManagerCatalog::getListOfClassNames(
                    $targetUserCBID
                ),
            ],
        ];
    }
    /* CBHTMLOutput_JavaScriptVariables() */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        $targetUserCBID = cb_query_string_value('hash');

        $userSettingsManagerClassNames = (
            CBUserSettingsManagerCatalog::getListOfClassNames(
                $targetUserCBID
            )
        );

        return array_merge(
            $userSettingsManagerClassNames,
            [
                'CBErrorHandler',
                'CBUI',
                'CBUserSettingsManager',
                'Colby',
            ]
        );
    }
    /* CBHTMLOutput_requiredClassNames() */

}
