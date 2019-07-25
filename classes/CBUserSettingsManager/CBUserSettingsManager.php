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

    /**
     * @param string $managerClassName
     * @param string $targetUserID
     *
     * @return void
     */
    static function render(
        string $managerClassName,
        string $targetUserID
    ): void {
        CBHTMLOutput::requireClassName($managerClassName);

        $functionName = "{$managerClassName}::CBUserSettingsManager_render";

        if (is_callable($functionName)) {
            call_user_func($functionName, $targetUserID);
        }
    }
}
