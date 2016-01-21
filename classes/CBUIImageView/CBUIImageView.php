<?php

final class CBUIImageView {

    /**
     * @return [string]
     */
    public static function requiredCSSURLs() {
        return [CBUIImageView::URL('CBUIImageView.css')];
    }

    /**
     * @return [string]
     */
    public static function requiredJavaScriptURLs() {
        return [CBUIImageView::URL('CBUIImageView.js')];
    }

    /**
     * @param string $filename
     *
     * @return string
     */
    public static function URL($filename) {
        $className = __CLASS__;
        return CBSystemURL . "/classes/{$className}/{$filename}";
    }
}
