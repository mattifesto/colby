<?php

/**
 * This class creates a spec that represents and empty CBViewPage.
 */
class CBEmptyPageTemplate {

    /**
     * @return stdClass
     */
    static function model() {
        return (object)['className' => 'CBViewPage'];
    }

    /**
     * @return string
     */
    static function title() {
        return 'Empty Page';
    }
}
