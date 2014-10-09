<?php

class CBRect
{
    private $origin;
    private $size;

    public function __construct($x, $y, $width, $height)
    {
        $this->origin = new CBPoint($x, $y);
        $this->size = new CBSize($width, $height);
    }

    public function origin()
    {
        return $this->origin;
    }

    public function size()
    {
        return $this->size;
    }
}