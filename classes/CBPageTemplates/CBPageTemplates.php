<?php

/**
 * This class provides the pages admin area with a list of templates that will
 * generate new page specs for users of the site. Most websites will reimplement
 * this class to customize the list of page templates.
 */
final class CBPageTemplates {

    /**
     * @return [string]
     */
    static function templateClassNames() {
        return ['CBStandardPageTemplate', 'CBBlogPostPageTemplate', 'CBEmptyPageTemplate'];
    }
}
