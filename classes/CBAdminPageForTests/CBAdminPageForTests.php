<?php

final class CBAdminPageForTests {

    /**
     * The data store ID of the test image.
     */
    const imageID = '3dd8e721048bbe8ea5f0c043fab73277a0b0044c';

    /**
     * @return [string]
     */
    static function adminPageMenuNamePath() {
        return ['test', 'test'];
    }

    /**
     * @return stdClass
     */
    static function adminPagePermissions() {
        return (object)['group' => 'Developers'];
    }

    /**
     * @return null
     */
    static function adminPageRenderContent() {
        CBHTMLOutput::setTitleHTML('Tests');
        CBHTMLOutput::setDescriptionHTML('Run website unit tests.');
    }

    /**
     * @return [string]
     */
    static function requiredCSSURLs() {
        return [Colby::flexnameForCSSForClass(CBSystemURL, __CLASS__)];
    }

    /**
     * @return [string]
     */
    static function requiredJavaScriptURLs() {
        return [Colby::flexnameForJavaScriptForClass(CBSystemURL, __CLASS__)];
    }
}
