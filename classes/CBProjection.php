<?php

/**
 * This class is used to calculate source and target rectangles for reducing
 * and cropping images. It's projection in that the source image is being
 * projected onto the target image.
 */
class CBProjection {

    /**
     * @return stdClass (projection)
     */
    public static function withSize($width, $height) {
        $p              = new stdClass();
        $p->source      = CBRect::withSize($width, $height);
        $p->destination = CBRect::withSize($width, $height);

        return $p;
    }

    /**
     * @return stdClass (projection)
     */
    public static function cropHeightFromCenter($projection, $height) {
        if ($projection->destination->height > $height) {
            $d          = CBRect::copyRect($projection->destination);
            $d->height  = $height;
            $ratio      = $height / $projection->destination->height;
            $h          = $projection->source->height * $ratio;
            $s          = CBRect::cropHeightFromCenter($projection->source, $h);

            $p              = new stdClass();
            $p->source      = $s;
            $p->destination = $d;
        } else {
            $p = CBProjection::copyProjection($projection);
        }

        return $p;
    }

    /**
     * @return stdClass (projection)
     */
    public static function cropLongEdgeFromCenter($projection, $length) {
        if ($projection->destination->height > $projection->destination->width) {
            return self::cropHeightFromCenter($projection, $length);
        } else {
            return self::cropWidthFromCenter($projection, $length);
        }
    }

    /**
     * @return stdClass (projection)
     */
    public static function cropShortEdgeFromCenter($projection, $length) {
        if ($projection->destination->height < $projection->destination->width) {
            return self::cropHeightFromCenter($projection, $length);
        } else {
            return self::cropWidthFromCenter($projection, $length);
        }
    }

    /**
     * @return stdClass (projection)
     */
    public static function cropWidthFromCenter($projection, $width) {
        if ($projection->destination->width > $width) {
            $d          = CBRect::copyRect($projection->destination);
            $d->width   = $width;
            $ratio      = $width / $projection->destination->width;
            $w          = $projection->source->width * $ratio;
            $s          = CBRect::cropWidthFromCenter($projection->source, $w);

            $p              = new stdClass();
            $p->source      = $s;
            $p->destination = $d;
        } else {
            $p = CBProjection::copyProjection($projection);
        }

        return $p;
    }

    /**
     * @return stdClass (projection)
     */
    public static function reduceHeight($projection, $height) {
        $p              = new stdClass();
        $p->source      = CBRect::copyRect($projection->source);
        $p->destination = CBRect::reduceHeight($projection->destination, $height);

        return $p;
    }

    /**
     * @return stdClass (projection)
     */
    public static function reduceLongEdge($projection, $length) {
        if ($projection->destination->height > $projection->destination->width) {
            return self::reduceHeight($projection, $length);
        } else {
            return self::reduceWidth($projection, $length);
        }
    }

    /**
     * @return stdClass (projection)
     */
    public static function reduceShortEdge($projection, $length) {
        if ($projection->destination->height < $projection->destination->width) {
            return self::reduceHeight($projection, $length);
        } else {
            return self::reduceWidth($projection, $length);
        }
    }

    /**
     * @return stdClass (projection)
     */
    public static function reduceWidth($projection, $width) {
        $p              = new stdClass();
        $p->source      = CBRect::copyRect($projection->source);
        $p->destination = CBRect::reduceWidth($projection->destination, $width);

        return $p;
    }
}
