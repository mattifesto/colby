<?php

/**
 * This class holds context data needed by views and subviews of a class when
 * the view tree is being walked for rendering or pre-processing.
 *
 * The information may be helpful in the following circumstances:
 *  - A view needs to render the title, description, or publication date.
 *  - A view needs to load or save a file in the page's data store.
 */
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
