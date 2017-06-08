<?php

final class CBRemoteAdministration {

    /**
     * @return null
     */
    static function fetchPublicInformationForAjax() {
        $response = new CBAjaxResponse();

        $response->siteName = CBSitePreferences::siteName();
        $response->isAdministrator = ColbyUser::currentUserIsMemberOfGroup('Administrators');
        $response->isDeveloper = ColbyUser::currentUserIsMemberOfGroup('Developers');

        $response->wasSuccessful = true;
        $response->send();
    }

    /**
     * @return stdClass
     */
    static function fetchPublicInformationForAjaxPermissions() {
        return (object)['group' => 'Public'];
    }
}
