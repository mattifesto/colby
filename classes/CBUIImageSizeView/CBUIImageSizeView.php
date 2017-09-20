<?php

final class CBUIImageSizeView {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [CBUIImageSizeView::URL('CBUIImageSizeView.css')];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
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
