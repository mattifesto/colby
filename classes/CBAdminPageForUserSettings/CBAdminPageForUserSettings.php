<?php

final class CBAdminPageForUserSettings {

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
        $targetUserID = $_GET['hash'];
        $userData = ColbyUser::fetchUserDataByHash($targetUserID);

        CBHTMLOutput::pageInformation()->title = (
            "User Administration ({$userData->facebookName})"
        );

        $userPhotoURL = CBFacebook::userImageURL($userData->facebookId);

        ?>

        <div class="identity">
            <img src="<?= $userPhotoURL ?>">
            <div><?= $userData->facebookName ?></div>
        </div>

        <?php

        CBUserSettingsManagerCatalog::renderUserSettingsManagerViews(
            $targetUserID
        );
    }
    /* CBAdmin_render() */


    /* -- CBHTMLOutput interfaces -- -- -- -- -- */

    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [
            Colby::flexpath(__CLASS__, 'css', cbsysurl()),
        ];
    }


    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBGroupUserSettings',
        ];
    }
}
