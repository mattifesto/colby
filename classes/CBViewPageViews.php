<?php

/**
 * This implementation is for the Laughing Elephant website.
 *
 * This class exists to provide the view page editor with the list of available
 * views for this website. Copy this file to the website's `classes` directory
 * to customize the views available for a website.
 */
final class CBViewPageViews {

    /**
     * Returns all of the views that are supported by the editor.
     *
     * @return {array}
     */
    public static function availableViewClassNames() {
        return ['CBBackgroundView', 'CBImageView', 'CBMarkaroundView'];
    }

    /**
     * Returns the subset of the views returned by `availableViewClassNames`
     * that will be used in the editor view menu. View that still need to be
     * supported but have been deprecated should not be included.
     *
     * @return {array}
     */
    public static function selectableViewClassNames() {
        return ['CBBackgroundView', 'CBImageView', 'CBMarkaroundView'];
    }
}
