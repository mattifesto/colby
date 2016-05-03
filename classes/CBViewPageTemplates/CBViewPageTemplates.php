<?php

/**
 * Colby implementation
 *
 * Reimplement this class to customize the available page templates.
 */
class CBViewPageTemplates {

    /**
     * @return [string]
     *  The list of page templates available to the page editor.
     */
    public static function availableTemplateClassNames() {
        return ['CBPageTemplate'];
    }
}
