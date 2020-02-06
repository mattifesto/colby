<?php

final class CBUserSettingsManager {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v572.js', cbsysurl()),
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBException',
            'CBModel',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */



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

}
