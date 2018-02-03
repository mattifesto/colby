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
    static function pop() {
        return array_pop(self::$contexts);
    }

    /**
     * @param [name => value] $args
     *
     *      [
     *          description: string?
     *          descriptionAsHTML: string? (deprecated)
     *          ID: hex160?
     *          imageURL: string?
     *          publishedTimestamp: int?
     *          selectedMainMenuItemName: string?
     *          title: string?
     *          titleAsHTML: string? (deprecated)
     *      ]
     *
     * @return null
     */
    static function push($args) {
        $description = $descriptionAsHTML = $ID = $imageURL =
        $selectedMainMenuItemName = $title = $titleAsHTML = '';

        $publishedTimestamp = null;

        extract($args, EXTR_IF_EXISTS);

        array_push(self::$contexts, (object)[
            'description' => $description,
            'descriptionAsHTML' => $descriptionAsHTML, /* deprecated */
            'ID' => $ID,
            'imageURL' => $imageURL,
            'publishedTimestamp' => $publishedTimestamp,
            'selectedMainMenuItemName' => $selectedMainMenuItemName,
            'title' => $title,
            'titleAsHTML' => $titleAsHTML, /* deprecated */
        ]);
    }

    /**
     * @return stdClass|false
     */
    static function current() {
        $context = end(self::$contexts);

        if (empty($context)) {
            return false;
        } else {
            return clone $context;
        }
    }
}
