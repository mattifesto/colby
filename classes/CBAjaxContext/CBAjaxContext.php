<?php

final class CBAjaxContext {

    private static $warnings = [];

    /**
     * @param string $warning
     *
     * @return null
     */
    static function appendWarning($warning) {
        CBAjaxContext::$warnings[] = $warning;
    }

    /**
     * @return [string]
     */
    static function warnings() {
        return CBAjaxContext::$warnings;
    }
}
