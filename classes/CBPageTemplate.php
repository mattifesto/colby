<?php

/**
 * This class creates a spec that represents and empty CBViewPage.
 */
class CBPageTemplate {

    /**
     * @return stdClass
     */
    public static function model() {
        return (object)['className' => 'CBViewPage'];
    }

    /**
     * @return string
     */
    public static function title() {
        return 'Blank Page';
    }
}
