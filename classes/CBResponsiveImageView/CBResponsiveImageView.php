<?php

final class CBResponsiveImageView {

    /**
     * @param stdClass $model
     *
     * @return null
     */
    public static function renderModelAsHTML(stdClass $model) {
        if (empty($model->themeID)) {
            $class = "";
        } else {
            CBHTMLOutput::addCSSURL(CBResponsiveImageView::themeIDToStyleSheetURL($model->themeID));
            $class = "T{$model->themeID}";
        }

        ?><figure class="CBResponsiveImageView <?= $class ?>">CBResponsiveImageView</figure><?php
    }

    /**
     * @param stdClass $spec
     *
     * @return stdClass
     */
    public static function specToModel(stdClass $spec) {
        $model = (object)['className' => __CLASS__];
        $model->themeID = CBModel::value($spec, 'themeID');

        return $model;
    }

    /**
     * @return string?
     */
    public static function specToThemeCSS(stdClass $spec, array $args) {
        $themeID = null;
        extract($args, EXTR_IF_EXISTS);

        $class = "T{$themeID}";

        return <<<EOT

.{$class} {
    background-color: red;
}

EOT;
    }

    /**
     * return hex160?
     */
    public static function specToThemeID(stdClass $spec) {
        if (!empty($spec->largeImage->ID) && !empty($spec->mediumImage->ID) && !empty($spec->smallImage->ID)) {
            return sha1("{$spec->largeImage->ID}{$spec->mediumImage->ID}{$spec->smallImage->ID}");
        } else {
            return null;
        }
    }

    /**
     * @param hex160 $themeID
     *
     * @return string
     */
    public static function themeIDToStyleSheetFilepath($themeID) {
        return CBDataStore::filepath([
            'ID' => $themeID,
            'filename' => 'CBResponsiveImageView.css',
        ]);
    }

    /**
     * @param hex160 $themeID
     *
     * @return string
     */
    public static function themeIDToStyleSheetURL($themeID) {
        return CBDataStore::toURL([
            'ID' => $themeID,
            'filename' => 'CBResponsiveImageView.css',
        ]);
    }

    /**
     * @return null
     */
    public static function updateStylesForAjax() {
        $response = new CBAjaxResponse();
        $spec = json_decode($_POST['specAsJSON']);

        if (!empty($spec->largeImage->ID) && !empty($spec->mediumImage->ID) && !empty($spec->smallImage->ID)) {
            $themeID = sha1("{$spec->largeImage->ID}{$spec->mediumImage->ID}{$spec->smallImage->ID}");
            $filepath = CBResponsiveImageView::themeIDToStyleSheetFilepath($themeID);

            CBDataStore::makeDirectoryForID($themeID);

            file_put_contents($filepath, CBResponsiveImageView::specToThemeCSS($spec, [
                'themeID' => $themeID,
            ]));

            $response->themeID = $themeID;
        }

        $response->wasSuccessful = true;
        $response->send();
    }

    /**
     * @return null
     */
    public static function updateStylesForAjaxPermissions() {
        return (object)['group' => 'Administrators'];
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
