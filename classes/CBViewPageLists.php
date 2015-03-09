<?php

/**
 * This class exists to provide the view page editor with the list of available
 * page lists for this website. Copy this file to the website's `classes`
 * directory to customize the page lists available for a website.
 */
class CBViewPageLists {

    /**
     * @return array
     */
    public static function availableListNames() {

        /**
         * Check for existence of deprecated global variable.
         */

        global $CBPageEditorAvailablePageListClassNames;

        if ($CBPageEditorAvailablePageListClassNames) {
            throw new RuntimeException('Convert the `CBPageEditorAvailablePageListClassNames` global variable to a `CBViewPageLists` class.');
        }

        return array();
    }
}
