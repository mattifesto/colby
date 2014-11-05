<?php

class CBPage {

    /**
     * @return instance type
     */
    private function __construct() { }

    /**
     * @return instance type
     */
    public static function init() {

        $page = new static();

        return $page;
    }

    /**
     * @param string $ID
     *  A string holding a hexadecimal representation of a 160-bit number
     *  that is a unique identifier for the page instance to be returned.
     *
     *  This string will often be used as a data store ID, but that is not
     *  a requirement.
     *
     *  If a page does not already exist with for the ID, then this initializer
     *  will return `null`.
     *
     * @return instance type | null
     *  This is a failable initializer. This class does not currently have
     *  the understanding to determine whether a page exists or not so it
     *  always returns `null`. Subclasses should implement this method to
     *  return an instance if a page with the ID exists.
     */
    public static function initWithID($ID) {

        return null;
    }

    /**
     *
     */
    public function renderHTML() { }
}
