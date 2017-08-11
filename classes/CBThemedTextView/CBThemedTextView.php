<?php

final class CBThemedTextView {

    /**
     * @deprecated use CBPageTitleAndDescriptionView
     *
     * @return null
     */
    public static function install() {
        /* deprecate the standard page header theme if it exists */
        $deprecatedStandardPageHeaderThemeID = '2a5eb6c836914ef8f33b15f0853ac61df554505e';
        $spec = CBModels::fetchSpecByID($deprecatedStandardPageHeaderThemeID);

        if ($spec === false) {
            return;
        }

        $originalSpec = clone $spec;

        /* reset properties */
        $spec->className = 'CBTheme';
        $spec->classNameForKind = 'CBTextView';
        $spec->description = 'Use CBPageTitleAndDescriptionView with its default theme instead.';
        $spec->title = 'Deprecated (Standard Page Header)';

        if ($spec != $originalSpec) {
            CBModels::save([$spec]);
        }
    }

    /**
     * @param stdClass $model
     *
     * @return string
     */
    public static function modelToSearchText(stdClass $model) {
        return "{$model->title} {$model->contentAsMarkaround}";
    }

    /**
     * @param string $themeID
     *
     * @return null|hex160
     */
    static function parseThemeID($themeID) {
        if (empty($themeID)) {
            return CBWellKnownThemeForContent::ID;
        } else if ('none' == $themeID) {
            return null;
        } else {
            return $themeID;
        }
    }

    /**
     * @param bool? $model->center;
     * @param string? $model->contentAsHTML
     * @param hex160? $model->themeID
     *  empty or unset - default theme
     *  "none" - no theme
     *  hex160 - themeID
     * @param string? $model->titleAsHTML
     * @param string? $model->URLAsHTML
     * @param bool? $model->useLightTextColors
     *
     * @return null
     */
    public static function renderModelAsHTML(stdClass $model) {
        if (empty($model->titleAsHTML) && empty($model->contentAsHTML)) {
            return;
        }

        $themeID = CBModel::value($model, 'themeID', CBWellKnownThemeForContent::ID, 'CBThemedTextView::parseThemeID');

        CBHTMLOutput::addCSSURL(CBThemedTextView::URL('CBThemedTextView.css'));
        CBTheme::useThemeWithID($themeID);

        $class = implode(' ', CBTheme::IDToCSSClasses($themeID));
        $class = "CBThemedTextView {$class}";

        if (!empty($model->stylesID)) {
            $stylesClass = CBTheme::IDToCSSClass($model->stylesID);
            $class = "{$class} {$stylesClass}";
        }

        if (!empty($model->useLightTextColors)) {
            $class = "{$class} light";
        }

        if (empty($model->stylesID)) {
            $styleElement = null;
        } else {
            $styleElement = "<style>{$model->stylesCSS}</style>";
        }

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
            $title = "<div class=\"title\"><h1{$style}>{$model->titleAsHTML}</h1></div>";
        }

        if (empty($model->contentAsHTML)) {
            $content = '';
        } else {
            $style = empty($model->contentColor) ? '' : " style=\"color: {$model->contentColor}\"";
            $content = "<div class=\"content\" {$style}>{$model->contentAsHTML}</div>";
        }

        echo $open, $styleElement, $title, $content, $close;
    }

    /**
     * @param stdClass $spec
     *
     * @return stdClass
     */
    public static function specToModel(stdClass $spec) {
        $model = (object)[
            'className' => __CLASS__,
            'useLightTextColors' => CBModel::value($spec, 'useLightTextColors', false, 'boolval'),
        ];

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

        /* view styles */

        $stylesTemplate = empty($spec->stylesTemplate) ? '' : trim($spec->stylesTemplate);

        if (!empty($stylesTemplate)) {
            $model->stylesID = CBHex160::random();
            $model->stylesCSS = CBTheme::stylesTemplateToStylesCSS($stylesTemplate, $model->stylesID);
        }

        return $model;
    }

    /**
     * @return [stdClass]
     */
    static function themeOptions() {
        return [
            (object)[
                'title' => 'No Theme',
                'description' => '',
                'value' => 'none',
            ],
        ];
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
