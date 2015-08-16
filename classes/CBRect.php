<?php

class CBRect {

    /**
     * @return stdClass (rect)
     */
    public static function copyRect($rect) {
        $r          = new stdClass();
        $r->x       = $rect->x;
        $r->y       = $rect->y;
        $r->width   = $rect->width;
        $r->height  = $rect->height;

        return $r;
    }

    /**
     * @return stdClass (rect)
     */
    public static function withSize($width, $height) {
        $r          = new stdClass();
        $r->x       = 0;
        $r->y       = 0;
        $r->width   = $width;
        $r->height  = $height;

        return $r;
    }

    /**
     * @return stdClass (rect)
     */
    public static function cropHeightFromCenter($rect, $height) {
        $r = self::copyRect($rect);

        if ($r->height > $height) {
            $r->y      += ($r->height - $height) / 2;
            $r->height  = $height;
        }

        return $r;
    }

    /**
     * @return stdClass (rect)
     */
    public static function cropWidthFromCenter($rect, $width) {
        $r = self::copyRect($rect);

        if ($r->width > $width) {
            $r->x      += ($r->width - $width) / 2;
            $r->width   = $width;
        }

        return $r;
    }

    /**
     * This function reduces the height to the specified height and reduces
     * the width proportionally.
     *
     * @return stdClass (rect)
     */
    public static function reduceHeight($rect, $height) {
        $r = self::copyRect($rect);

        if ($r->height > $height) {
            $r->width   = $r->width * ($height / $r->height);
            $r->height  = $height;
        }

        return $r;
    }

    /**
     * This function reduces the width to the specified width and reduces
     * the height proportionally.
     *
     * @return stdClass (rect)
     */
    public static function reduceWidth($rect, $width) {
        $r = self::copyRect($rect);

        if ($r->width > $width) {
            $r->height  = $r->height * ($width / $r->width);
            $r->width   = $width;
        }

        return $r;
    }
}
