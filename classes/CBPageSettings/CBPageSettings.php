<?php

final class CBPageSettings {

    /**
     * @return [string]
     */
    static function htmlElementClassNames(array $pageSettingsClassNames): array {
        $htmlElementClassNames = [];

        foreach ($pageSettingsClassNames as $className) {
            if (is_callable($function = "{$className}::CBPageSettings_htmlElementClassNames")) {
                $classNames = call_user_func($function);
                $htmlElementClassNames = array_merge($htmlElementClassNames, $classNames);
            } error_log(json_encode($htmlElementClassNames));
        }

        return array_values(array_unique($htmlElementClassNames));
    }
}
