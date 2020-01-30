<?php

/**
 * To show settings on individual user admin pages, creating a class that
 * implements the CBUserSettingsManager_render() interface. The function may
 * choose to only display settings interface elements if the administrative user
 * is allowed to set them, for instance if only developers are allowed to change
 * the settings for users. The function may choose to only display settings for
 * users that can have them set, such as only for wholesale customer users.
 */
final class CBUserSettingsManager {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v569.js', cbsysurl()),
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBModel',
        ];
    }



    /* -- functions -- -- -- -- -- */



    /**
     * When we render a page that shows user settings managers for a user, we
     * pass a list of user settings manager class names that will render user
     * interfaces via JavaScript.
     *
     * We only include the user settings manager class names that return true
     * from the CBUserSettingsManager_currentUserCanViewForTargetUser()
     * interface.
     *
     * @NOTE
     *
     *      This is not how security is implemented, it is just to reduce user
     *      interface clutter. A skilled user can easily force inclusion of a
     *      class name. The classes should be sure to not show data for the
     *      current user related to the target user if they manually force a
     *      class name to be included on the client side.
     *
     * @return bool
     */
    static function currentUserCanViewForTargetUser(
        string $managerClassName,
        string $targetUserCBID
    ): bool {
        $functionName = (
            $managerClassName .
            '::CBUserSettingsManager_currentUserCanViewForTargetUser'
        );

        if (is_callable($functionName)) {
            return call_user_func(
                $functionName,
                $targetUserCBID
            );
        } else {
            return false;
        }
    }
    /* currentUserCanViewForTargetUser() */



    /**
     * @deprecated 2020_01_29
     *
     *      All user settings manager user interface should be created in
     *      JavaScript. Classes should replace the implementation of
     *      CBUserSettingsManager_render() with JavaScript.
     *
     * @param string $managerClassName
     * @param string $targetUserCBID
     *
     * @return void
     */
    static function render(
        string $managerClassName,
        string $targetUserCBID
    ): void {
        $functionName = (
            $managerClassName .
            '::CBUserSettingsManager_render'
        );

        if (is_callable($functionName)) {
            CBHTMLOutput::requireClassName($managerClassName);

            call_user_func(
                $functionName,
                $targetUserCBID
            );
        }
    }
    /* render() */

}
