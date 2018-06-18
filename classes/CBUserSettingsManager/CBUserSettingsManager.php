<?php

final class CBUserSettingsManager {

    /**
     * @param string $managerClassName
     * @param string $targetUserID
     *
     * @return void
     */
    static function render(string $managerClassName, string $targetUserID): void {
        CBHTMLOutput::requireClassName($managerClassName);

        if (is_callable($function = "{$managerClassName}::CBUserSettingsManager_render")) {
            call_user_func($function, $targetUserID);
        }
    }
}
