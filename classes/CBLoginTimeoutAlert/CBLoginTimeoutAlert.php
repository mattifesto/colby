<?php

/**
 * When included as a required class for a page, a visitor of that page will
 * receive an alert when their login times out.
 */
final class CBLoginTimeoutAlert {

    /**
     * @return [string]
     */
    static function requiredCSSURLs() {
        return [/*Colby::flexnameForCSSForClass(CBSystemURL, __CLASS__)*/];
    }

    /**
     * @return [[string, mixed]]
     */
    static function requiredJavaScriptVariables() {
        return [
            ['CBUserIsLoggedIn', empty(ColbyUser::currentUserId()) ? '' : 'yes'],
        ];
    }

    /**
     * @return [string]
     */
    static function requiredJavaScriptURLs() {
        return [Colby::flexnameForJavaScriptForClass(CBSystemURL, __CLASS__)];
    }
}
