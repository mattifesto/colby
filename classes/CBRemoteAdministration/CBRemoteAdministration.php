<?php

final class CBRemoteAdministration {

    /**
     * @return null
     */
    static function fetchPublicInformationForAjax() {
        $response = new CBAjaxResponse();

        $response->siteName = CBSitePreferences::siteName();
        $response->isLoggedIn = (ColbyUser::currentUserHash() !== null);
        $response->isAdministrator = ColbyUser::currentUserIsMemberOfGroup('Administrators');
        $response->isDeveloper = ColbyUser::currentUserIsMemberOfGroup('Developers');

        $response->wasSuccessful = true;
        $response->send();
    }

    /**
     * @return object
     */
    static function fetchPublicInformationForAjaxPermissions() {
        return (object)['group' => 'Public'];
    }

    /**
     * @return null
     */
    static function fetchStatisticsForAjax() {
        $response = new CBAjaxResponse();

        $SQL = <<<EOT

            SELECT  COUNT(*)
            FROM    `ColbyPages`

EOT;

        $response->pageCount = (int)CBDB::SQLToValue($SQL);
        $response->wasSuccessful = true;
        $response->send();
    }

    /**
     * @return object
     */
    static function fetchStatisticsForAjaxPermissions() {
        return (object)['group' => 'Administrators'];
    }

    /**
     * @return null
     */
    static function logoutForAjax() {
        $response = new CBAjaxResponse();

        ColbyUser::logoutCurrentUser();

        $response->wasSuccessful = true;
        $response->send();
    }

    /**
     * @return object
     */
    static function logoutForAjaxPermissions() {
        return (object)['group' => 'Public'];
    }
}
