<?php

/**
 * This class creates a spec that represents and empty CBViewPage.
 */
class CBEmptyPageTemplate {

    /**
     * @return object
     */
    static function CBModelTemplate_spec() {
        return (object)[
            'className' => 'CBViewPage'
        ];
    }

    /**
     * @return string
     */
    static function CBModelTemplate_title() {
        return 'Empty Page';
    }
}
