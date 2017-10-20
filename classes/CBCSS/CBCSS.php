<?php

final class CBCSS {

    /**
     * @param string $className
     *
     *      An array of CSS class names. This may be a mix of class names that
     *      are modifiers for a view and class names that are actually classes
     *      that will include a CSS file.
     *
     * @return bool
     *
     *      This function will return true if the class name is 'custom' or if
     *      the class name is of an actual class that implements the
     *      CBCSS_isCustom interface to return a truthy value.
     */
    static function isCustom($className) {
        if ($className === 'custom') {
            return true;
        }

        if (is_callable($function = "{$className}::CBCSS_isCustom")) {
            return !!call_user_func($function);
        } else {
            return false;
        }
    }
}
