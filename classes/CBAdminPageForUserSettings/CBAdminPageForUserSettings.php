<?php

final class CBAdminPageForUserSettings {

    /**
     * @return [string]
     */
    static function adminPageMenuNamePath() {
        return ['general', 'users'];
    }

    /**
     * @return stdClass
     */
    static function adminPagePermissions() {
        return (object)['group' => 'Administrators'];
    }

    /**
     * @return void
     */
    static function adminPageRenderContent() {
        $targetUserID = $_GET['hash'];
        $userData = ColbyUser::fetchUserDataByHash($targetUserID);

        CBHTMLOutput::pageInformation()->title = "User Administration ({$userData->facebookName})";

        $userPhotoURL = CBFacebook::userImageURL($userData->facebookId);

        ?>

        <div class="identity">
            <img src="<?= $userPhotoURL ?>">
            <div><?= $userData->facebookName ?></div>
        </div>

        <?php

        CBUserSettingsManagerCatalog::render($targetUserID);
    }

    /**
     * @return [string]
     */
    static function requiredClassNames() {
        return ['CBGroupUserSettings'];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [Colby::flexnameForCSSForClass(CBSystemURL, __CLASS__)];
    }
}
