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
    static function CBModel_toSearchText(stdClass $model) {
        return "{$model->title} {$model->contentAsMarkaround}";
    }

    /**
     * @param bool? $model->center;
     * @param string? $model->contentAsHTML
     * @param string? $model->titleAsHTML
     * @param string? $model->URLAsHTML
     * @param bool? $model->useLightTextColors
     *
     * @return null
     */
    static function CBView_render(stdClass $model) {
        if (empty($model->titleAsHTML) && empty($model->contentAsHTML)) {
            return;
        }

        $class = "CBThemedTextView CBThemedTextView_default";

        if (!empty($model->stylesID)) {
            $stylesClass = "T{$model->stylesID}";
            $class = "{$class} {$stylesClass}";
        }

        if (!empty($model->useLightTextColors)) {
            $class = "{$class} light";
        }

        CBHTMLOutput::addCSS(CBModel::value($model, 'stylesCSS'));

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

        echo $open, $title, $content, $close;
    }

    /**
     * @param stdClass $spec
     *
     * @return stdClass
     */
    static function CBModel_toModel(stdClass $spec) {
        $model = (object)[
            'className' => __CLASS__,
            'useLightTextColors' => CBModel::value($spec, 'useLightTextColors', false, 'boolval'),
        ];

        $model->center = CBModel::value($spec, 'center', false, 'boolval');
        $model->contentAsMarkaround = isset($spec->contentAsMarkaround) ? trim($spec->contentAsMarkaround) : '';
        $model->contentAsHTML = CBMarkaround::markaroundToHTML($model->contentAsMarkaround);
        $model->contentColor = CBModel::value($spec, 'contentColor', null, 'CBConvert::stringToCSSColor');
        $model->titleAsMarkaround = isset($spec->titleAsMarkaround) ? trim($spec->titleAsMarkaround) : '';
        $model->title = CBMarkaround::paragraphToText($model->titleAsMarkaround);
        $model->titleAsHTML = CBMarkaround::paragraphToHTML($model->titleAsMarkaround);
        $model->titleColor = CBModel::value($spec, 'titleColor', null, 'CBConvert::stringToCSSColor');
        $model->URL = isset($spec->URL) ? trim($spec->URL) : '';
        $model->URLAsHTML = ColbyConvert::textToHTML($model->URL);

        if (!empty($stylesTemplate = CBModel::value($spec, 'stylesTemplate', '', 'trim'))) {
            $model->stylesID = CBHex160::random();
            $localCSSClassName = "T{$model->stylesID}";
            $model->stylesCSS = CBView::localCSSTemplateToLocalCSS($stylesTemplate, 'view', ".{$localCSSClassName}");
        }

        return $model;
    }

    /**
     * @return string
     */
    static function CBHTMLOutput_CSSURLs() {
        return [Colby::flexpath(__CLASS__, 'css', cbsysurl())];
    }
}
