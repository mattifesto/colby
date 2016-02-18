<?php

final class CBPageContext {

    private static $contexts = [];

    /**
     * @return stdClass
     */
    public static function pop() {
        return array_pop(self::$contexts);
    }

    /**
     * @param string $descriptionAsHTML
     * @param hex160 $ID
     * @param int $publishedBy
     * @param int $publishedTimestamp
     * @param string $titleAsHTML
     *
     * @return null
     */
    public static function push($args) {
        $descriptionAsHTML = $ID = $publishedTimestamp = $titleAsHTML = null;
        extract($args, EXTR_IF_EXISTS);

        array_push(self::$contexts, (object)[
            'descriptionAsHTML' => $descriptionAsHTML,
            'ID' => $ID,
            'publishedTimestamp' => $publishedTimestamp,
            'titleAsHTML' => $titleAsHTML,
        ]);
    }

    /**
     * @return stdClass|false
     */
    public static function current() {
        return clone end(self::$contexts);
    }
}
