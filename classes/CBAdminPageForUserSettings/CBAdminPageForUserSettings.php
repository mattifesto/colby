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
        $userHash = $_GET['hash'];
        $userData = ColbyUser::fetchUserDataByHash($userHash);

        CBHTMLOutput::setTitleHTML('User');
        CBHTMLOutput::setDescriptionHTML('Tools for viewing and editing a user\'s settings.');

        $userPhotoURL = CBFacebook::userImageURL($userData->facebookId);

        ?>

        <div class="identity">
            <img src="<?= $userPhotoURL ?>">
            <div><?= $userData->facebookName ?></div>
        </div>

        <?php

        CBGroupUserSettings::renderUserSettings($userData, 'Administrators');
        CBGroupUserSettings::renderUserSettings($userData, 'Developers');

        $classNamesForUserSettings = CBSitePreferences::classNamesForUserSettings();

        foreach ($classNamesForUserSettings as $className) {
            CBHTMLOutput::requireClassName($className);

            if (is_callable($function = "{$className}::renderUserSettings")) {
                call_user_func($function, $userData);
            }
        }
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
    static function requiredCSSURLs() {
        return [Colby::flexnameForCSSForClass(CBSystemURL, __CLASS__)];
    }
}
