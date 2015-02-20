<?php

/**
 * This class exists to provide the view page editor with the list of available
 * views for this website. Copy this file to the website's `classes` directory
 * to customize the views available for a website.
 */
class CBViewPageViews {

    /**
     * array
     */
    public static function availableViewClassNames() {
        return array('CBBackgroundView', 'CBImageView', 'CBMarkaroundView');
    }
}
