<?php

class ColbyRect
{
    public $x = 0;
    public $y = 0;
    public $width  = 0;
    public $height = 0;

    /**
     * @return ColbyRect
     */
    public static function destinationRectToFitRequestedSize($sourceSize, $requestedSize)
    {
        $rect = new ColbyRect();

        // source width and height are less than or equal to requested width and height

        if (   $sourceSize[0] <= $requestedSize[0]
            && $sourceSize[1] <= $requestedSize[1])
        {
            $rect->width  = $sourceSize[0];
            $rect->height = $sourceSize[1];

            return $rect;
        }

        $sourceAspectRatio = $sourceSize[0] / $sourceSize[1];
        $requestedAspectRatio = $requestedSize[0] / $requestedSize[1];

        // source rect is narrower than the requested rect

        if ($sourceAspectRatio < $requestedAspectRatio)
        {
            $rect->width  = $requestedSize[1] * $sourceAspectRatio;
            $rect->height = $requestedSize[1];

            return $rect;
        }

        // source rect is wider than the requested rect

        $rect->width  = $requestedSize[0];
        $rect->height = $requestedSize[0] / $sourceAspectRatio;

        return $rect;
    }

    /**
     * @return ColbyRect
     */
    public static function sourceRectToFillRequestedSize($sourceSize, $requestedSize)
    {
        $rect = new ColbyRect();

        $sourceAspectRatio = $sourceSize[0] / $sourceSize[1];
        $requestedAspectRatio = $requestedSize[0] / $requestedSize[1];

        // the source size is wider than the requested size

        if ($sourceAspectRatio > $requestedAspectRatio)
        {
            $rect->width  = $sourceSize[1] * $requestedAspectRatio;
            $rect->height = $sourceSize[1];

            // center the rect horizontally inside the source size

            $rect->x = ($sourceSize[0] - $rect->width) / 2;

            return $rect;
        }

        // the source size is narrower than the requested size

        $rect->width  = $sourceSize[0];
        $rect->height = $sourceSize[0] / $requestedAspectRatio;

        $rect->y = ($sourceSize[1] - $rect->height) / 2;

        return $rect;
    }
}
