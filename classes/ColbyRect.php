<?php

class ColbyRect
{
    public $x = 0;
    public $y = 0;
    public $width = 0;
    public $height= 0;

    //
    //
    //
    public static function /* ColbyRect */ destinationRect($sourceSize, $destinationSize)
    {
        $rect = new ColbyRect();

        $rect->width = $sourceSize[0];
        $rect->height = $sourceSize[1];

        return $rect;
    }

    //
    //
    //
    public static function /* ColbyRect */ sourceRect($sourceSize, $destinationSize)
    {
        $rect = new ColbyRect();

        $rect->width = $sourceSize[0];
        $rect->height = $sourceSize[1];

        return $rect;
    }
}
