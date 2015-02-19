<?php

/**
 * 2015.02.18
 * This class is deprecated.
 */
class CBSize
{
    private $width = 0;
    private $height = 0;

    public function __construct($width, $height)
    {
        $this->width = $width;
        $this->height = $height;
    }

    public function width()
    {
        return $this->width;
    }

    public function height()
    {
        return $this->height;
    }
}
