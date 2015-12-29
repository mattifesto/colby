<?php

final class CBThemedTextView {

    const standardPageHeaderThemeID = '2a5eb6c836914ef8f33b15f0853ac61df554505e';

    /**
     * @return null
     */
    public static function install() {

        // Ensure the standard page header theme exists.

        $spec = CBModels::fetchSpecByID(CBThemedTextView::standardPageHeaderThemeID);

        if ($spec === false) {
            $spec = CBModels::modelWithClassName('CBThemedTextViewTheme', [
                'ID' => CBThemedTextView::standardPageHeaderThemeID
            ]);
            $spec->title = 'Standard Page Header';

            CBModels::save([$spec]);
        }
    }

    /**
     * @param {stdClass} $model
     *
     * @return {string}
     */
    public static function modelToSearchText(stdClass $model) {
        return "{$model->title} {$model->contentAsMarkaround}";
    }

    /**
     * @param string? $model->contentAsHTML
     * @param hex160? $model->themeID
     * @param string? $model->titleAsHTML
     * @param string? $model->URLAsHTML
     *
     * @return null
     */
    public static function renderModelAsHTML(stdClass $model) {
        $themeID = CBModel::value($model, 'themeID');
        $class = CBTheme::IDToCSSClass($themeID);
        $class = "CBThemedTextView {$class}";
        CBHTMLOutput::addCSSURL(CBTheme::IDToCSSURL($themeID));

        if (empty($model->URLAsHTML)) {
            $open   = "<section class=\"{$class}\">";
            $close  = '</section>';
        } else {
            $open   = "<a href=\"{$model->URLAsHTML}\" class=\"{$class}\">";
            $close  = '</a>';
        }

        if (empty($model->titleAsHTML)) {
            $title = '';
        } else {
            $title = "<h1>{$model->titleAsHTML}</h1>";
        }

        if (empty($model->contentAsHTML)) {
            $content = '';
        } else {
            $content = "<div>{$model->contentAsHTML}</div>";
        }

        echo $open, $title, $content, $close;
    }

    /**
     * @param {stdClass} $spec
     *
     * @return {stdClass}
     */
    public static function specToModel(stdClass $spec) {
        $model = CBModels::modelWithClassName(__CLASS__);
        $model->contentAsMarkaround = isset($spec->contentAsMarkaround) ? trim($spec->contentAsMarkaround) : '';
        $model->contentAsHTML = ColbyConvert::markaroundToHTML($model->contentAsMarkaround);
        $model->themeID = isset($spec->themeID) ? $spec->themeID : false;
        $model->titleAsMarkaround = isset($spec->titleAsMarkaround) ? trim($spec->titleAsMarkaround) : '';
        $model->title = CBMarkaround::paragraphToText($model->titleAsMarkaround);
        $model->titleAsHTML = CBMarkaround::paragraphToHTML($model->titleAsMarkaround);
        $model->URL = isset($spec->URL) ? trim($spec->URL) : '';
        $model->URLAsHTML = ColbyConvert::textToHTML($model->URL);

        return $model;
    }

    /**
     * @param {string} $filename
     *
     * @return {string}
     */
    public static function URL($filename) {
        $className = __CLASS__;
        return CBSystemURL . "/classes/{$className}/{$filename}";
    }
}
