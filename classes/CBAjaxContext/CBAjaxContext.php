<?php

final class CBAjaxContext {

    private static $warnings = [];

    /**
     * @param string $warning
     *
     * @return null
     */
    public static function appendWarning($warning) {
        CBAjaxContext::$warnings[] = $warning;
    }

    /**
     * @return [string]
     */
    public static function warnings() {
        return CBAjaxContext::$warnings;
    }
}
