<?php

class CBPage
{
    protected $ID;

    /**
     * @return instance type
     */
    private function __construct()
    {
    }

    /**
     * @return instance type
     */
    public static function init()
    {
        $page       = new static();
        $page->ID   = Colby::random160();

        return $page;
    }

    /**
     * @param string $ID
     *
     *  A string holding a hexadecimal representation of a 160-bit number
     *  that is a unique identifier for the page instance returned.
     *
     *  This string will often be used as a data store ID, but that is not
     *  a requirement.
     *
     * @return instance type
     */
    public static function initWithID($ID)
    {
        $page       = new static();
        $page->ID   = $ID;

        return $page;
    }

    /**
     *
     */
    public function renderHTML()
    {
    }
}
