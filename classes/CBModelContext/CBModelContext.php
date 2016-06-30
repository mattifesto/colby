<?php

final class CBModelContext {

    private static $contexts = [];

    /**
     * @param string $message
     *
     * @return null
     */
    public static function appendError($message) {
        $context = end(CBModelContext::$contexts);

        if ($context) {
            $context->errors = $message;
        }
    }

    /**
     * @param string $message
     *
     * @return null
     */
    public static function appendWarning($message) {
        $context = end(CBModelContext::$contexts);

        if ($context) {
            $context->warnings = $message;
        }
    }

    /**
     * @return null
     */
    public static function push() {
        $context = (object)[
            'errors' => [],
            'warnings' => [],
        ];

        CBModelContext::$contexts[] = $context;
    }

    /**
     * @return stdObject
     */
    public static function pop() {
        if (empty(CBModelContext::$contexts)) {
            throw new RuntimeException('There are no pushed model contexts.');
        }

        return array_pop(CBModelContext::$contexts);
    }
}
