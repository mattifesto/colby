<?php

final class CBThemedTextView {

    /**
     * @deprecated use CBPageTitleAndDescriptionView along with
     * CBStandardModels::CBThemeIDForCBPageTitleAndDescriptionView
     */
    const standardPageHeaderThemeID = '2a5eb6c836914ef8f33b15f0853ac61df554505e';

    /**
     * @deprecated use CBPageTitleAndDescriptionView
     *
     * @return null
     */
    public static function install() {

        // Ensure the standard page header theme exists.

        $spec = CBModels::fetchSpecByID(CBThemedTextView::standardPageHeaderThemeID);

        if ($spec === false) {
            $spec = (object)[
                'ID' => CBThemedTextView::standardPageHeaderThemeID,
                'className' => 'CBTheme',
                'classNameForKind' => 'CBTextView',
                'title' => 'Standard Page Header',
            ];

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

        CBHTMLOutput::addCSSURL(CBThemedTextView::URL('CBThemedTextView.css'));
        CBHTMLOutput::addCSSURL(CBTheme::IDToCSSURL($themeID));

        $style = empty($model->center) ? '' : ' style="text-align: center"';

        if (empty($model->URLAsHTML)) {
            $open   = "<section class=\"{$class}\"{$style}>";
            $close  = '</section>';
        } else {
            $open   = "<a href=\"{$model->URLAsHTML}\" class=\"{$class}\"{$style}>";
            $close  = '</a>';
        }

        if (empty($model->titleAsHTML)) {
            $title = '';
        } else {
            $style = empty($model->titleColor) ? '' : " style=\"color: {$model->titleColor}\"";
            $title = "<h1{$style}>{$model->titleAsHTML}</h1>";
        }

        if (empty($model->contentAsHTML)) {
            $content = '';
        } else {
            $style = empty($model->contentColor) ? '' : " style=\"color: {$model->contentColor}\"";
            $content = "<div{$style}>{$model->contentAsHTML}</div>";
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
        $model->center = CBModel::value($spec, 'center', false, 'boolval');
        $model->contentAsMarkaround = isset($spec->contentAsMarkaround) ? trim($spec->contentAsMarkaround) : '';
        $model->contentAsHTML = CBMarkaround::markaroundToHTML($model->contentAsMarkaround);
        $model->contentColor = CBModel::value($spec, 'contentColor', null, 'CBConvert::stringToCSSColor');
        $model->themeID = isset($spec->themeID) ? $spec->themeID : false;
        $model->titleAsMarkaround = isset($spec->titleAsMarkaround) ? trim($spec->titleAsMarkaround) : '';
        $model->title = CBMarkaround::paragraphToText($model->titleAsMarkaround);
        $model->titleAsHTML = CBMarkaround::paragraphToHTML($model->titleAsMarkaround);
        $model->titleColor = CBModel::value($spec, 'titleColor', null, 'CBConvert::stringToCSSColor');
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
