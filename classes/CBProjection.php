<?php

/**
 * This class is used to calculate source and target rectangles for reducing
 * and cropping images. It's projection in that the source image is being
 * projected onto the target image.
 */
class CBProjection {

    /**
     * TODO: 2015.06.04 All op codes need to be added
     *
     * @return {stdClass} (projection)
     */
    public static function applyOpString($projection, $opString) {
        preg_match_all("/([a-z]+)([0-9]+(\.[0-9]+)?)/", $opString, $matches, PREG_SET_ORDER);

        foreach($matches as $op) {
            $code   = $op[1];
            $value  = $op[2];

            switch ($code) {
                case 'chc':
                    $projection = CBProjection::cropHeightFromCenter($projection, $value);
                    break;

                case 'clc':
                    $projection = CBProjection::cropLongEdgeFromCenter($projection, $value);
                    break;

                case 'cwc':
                    $projection = CBProjection::cropWidthFromCenter($projection, $value);
                    break;

                case 'rh':
                    $projection = CBProjection::reduceHeight($projection, $value);
                    break;

                case 'rl':
                    $projection = CBProjection::reduceLongEdge($projection, $value);
                    break;

                case 'rs':
                    $projection = CBProjection::reduceShortEdge($projection, $value);
                    break;

                case 'rw':
                    $projection = CBProjection::reduceWidth($projection, $value);
                    break;

                case 's':
                    $projection = CBProjection::scale($projection, $value);
                    break;

                default:
                    throw new InvalidArgumentException("The code \"{$code}\" is unknown.");
            }
        }

        return $projection;
    }

    /**
     * @return stdClass (projection)
     */
    public static function copyProjection($projection) {
        $p              = new stdClass();
        $p->source      = CBRect::copyRect($projection->source);
        $p->destination = CBRect::copyRect($projection->destination);

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
     * @param stdClass $projection
     * @param number $width
     * @param number $height
     *
     * @return bool
     */
    static function isNoOpForSize($projection, $width, $height) {
        return CBRect::areEqual($projection->source, $projection->destination) &&
               CBRect::areEqual($projection->source, CBRect::withSize($width, $height))
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

    /**
     * @return stdClass (projection)
     */
    public static function scale($projection, $factor) {
        $p = new stdClass();
        $p->source = CBRect::copyRect($projection->source);
        $p->destination = CBRect::copyRect($projection->destination);
        $p->destination->height *= $factor;
        $p->destination->width *= $factor;

        return $p;
    }

    /**
     * @return stdClass (projection)
     */
    public static function withSize($width, $height) {
        $p              = new stdClass();
        $p->source      = CBRect::withSize($width, $height);
        $p->destination = CBRect::withSize($width, $height);

        return $p;
    }
}
