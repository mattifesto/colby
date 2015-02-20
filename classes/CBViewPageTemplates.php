<?php

/**
 * This class exists to provide the view page editor with the list of available
 * page templates for this website. Copy this file to the website's `classes`
 * directory to customize the page templates available for a website.
 */
class CBViewPageTemplates {

    /**
     * array
     */
    public static function availableTemplateClassNames() {
        return array('CBPageTemplate');
    }
}
