<?php

final class CBUIImageSizeView {

    /**
     * @return [string]
     */
    static function requiredCSSURLs() {
        return [CBUIImageSizeView::URL('CBUIImageSizeView.css')];
    }

    /**
     * @return [string]
     */
    static function requiredJavaScriptURLs() {
        return [CBUIImageSizeView::URL('CBUIImageSizeView.js')];
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
