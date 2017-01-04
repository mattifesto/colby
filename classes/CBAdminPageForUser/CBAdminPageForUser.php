<?php

final class CBAdminPageForUser {

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
        $userRow = ColbyUser::fetchUserRowByHash($userHash);

        CBHTMLOutput::setTitleHTML('User');
        CBHTMLOutput::setDescriptionHTML('Tools for viewing and editing a user\'s settings.');

        echo "<p>{$userRow->facebookName}";

    }
}
