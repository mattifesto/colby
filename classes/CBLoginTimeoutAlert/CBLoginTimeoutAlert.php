<?php

/**
 * When included as a required class for a page, a visitor of that page will
 * receive an alert when their login times out.
 */
final class CBLoginTimeoutAlert {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v529.js', cbsysurl()),
        ];
    }


    /**
     * @return [[string, mixed]]
     */
    static function CBHTMLOutput_JavaScriptVariables(): array {
        return [
            [
                'CBLoginTimeoutAlert_userIsLoggedIn',
                ColbyUser::getCurrentUserID() !== null,
            ],
        ];
    }


    /**
     * @return string
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBUIPanel',
            'Colby',
        ];
    }
}
