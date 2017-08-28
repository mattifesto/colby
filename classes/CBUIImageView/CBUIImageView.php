<?php

final class CBUIImageView {

    /**
     * @return [string]
     */
    static function requiredCSSURLs() {
        return [CBUIImageView::URL('CBUIImageView.css')];
    }

    /**
     * @return [string]
     */
    static function requiredJavaScriptURLs() {
        return [CBUIImageView::URL('CBUIImageView.js')];
    }

    /**
     * @param string $filename
     *
     * @return string
     */
    static function URL($filename) {
        $className = __CLASS__;
        return CBSystemURL . "/classes/{$className}/{$filename}";
    }
}
