<?php

class ColbyPoint
{
    private $x = 0;
    private $y = 0;

    public function __construct($x, $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    public function x()
    {
        return $this->x;
    }

    public function y()
    {
        return $this->y;
    }
}

class ColbySize
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

class ColbyRect
{
    private $origin;
    private $size;

    public function __construct($x, $y, $width, $height)
    {
        $this->origin = new ColbyPoint($x, $y);
        $this->size = new ColbySize($width, $height);
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