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
    static function CBHTMLOutput_CSSURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'css', cbsysurl()),
        ];
    }


    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v503.js', cbsysurl()),
        ];
    }
}
