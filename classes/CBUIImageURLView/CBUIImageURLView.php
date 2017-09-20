<?php

/**
 * @deprecated use CBUIImageSizeView
 *
 * This is a class for viewing an image in a URL property on a spec. This is to
 * support older views that track an image URL rather than an image spec.
 *
 * Although this is deprecated, it can and will be used until all views use
 * image specs rather than image URLs.
 *
 * Classes that use this view:
 *  - CBImageLinkViewEditor
 */
final class CBUIImageURLView {

    /**
     * @return [string]
     */
    static function requiredClassNames() {
        return [];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [CBUIImageURLView::URL('CBUIImageURLView.css')];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [CBUIImageURLView::URL('CBUIImageURLView.js')];
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
