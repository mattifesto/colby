<?php

class CBPoint
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
