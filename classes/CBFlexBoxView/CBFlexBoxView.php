<?php

final class CBFlexBoxView {

    /**
     * @return [{string}]
     */
    public static function editorURLsForCSS() {
        return [
            CBSystemURL . '/javascript/CBImageEditorFactory.css',
            CBSystemURL . '/javascript/CBSpecArrayEditor.css',
            CBFlexBoxView::URL('CBFlexBoxViewEditor.css')
        ];
    }

    /**
     * @return [{string}]
     */
    public static function editorURLsForJavaScript() {
        return [
            CBSystemURL . '/javascript/CBImageEditorFactory.js',
            CBSystemURL . '/javascript/CBSpecArrayEditorFactory.js',
            CBSystemURL . '/javascript/CBStringEditorFactory.js',
            CBFlexBoxView::URL('CBFlexBoxViewEditorFactory.js')
        ];
    }

    /**
     * @return null
     */
    public static function renderModelAsHTML(stdClass $model) {
        $styles = [];

        if ($model->backgroundColor !== null) {
            $styles[] = "background-color: {$model->backgroundColor};";
        }

        $styles[]   = "background-position: {$model->backgroundPositionX} {$model->backgroundPositionY};";
        $styles[]   = "background-repeat: {$model->backgroundRepeatX} {$model->backgroundRepeatY};";

        if ($model->width !== null) {
            $styles[] = "width: {$model->width}px;";
        }

        if ($model->height !== null) {
            $styles[] = "height: {$model->height}px;";
        }

        if (!empty($model->imageURL)) {
            $styles[] = "background-image: url({$model->imageURL});";
        }

        $flexItemAlign  = CBIE10Flexbox::alignSelfToFlexItemAlign($model->flexAlignSelf);
        $styles[]       = "align-self: {$model->flexAlignSelf};";
        $styles[]       = "-ms-flex-item-align: {$flexItemAlign};";
        $styles[]       = "-webkit-align-self: {$model->flexAlignSelf};";

        $flexAlign  = CBIE10Flexbox::alignItemsToFlexAlign($model->flexAlignItems);
        $styles[]   = "align-items: {$model->flexAlignItems};";
        $styles[]   = "-ms-flex-align: {$flexAlign};";
        $styles[]   = "-webkit-align-items: {$model->flexAlignItems};";

        $styles[]   = "flex: {$model->flexFlex};";
        $styles[]   = "-webkit-flex: {$model->flexFlex};";
        $styles[]   = "-ms-flex: {$model->flexFlex};";

        $styles[]   = "flex-direction: {$model->flexDirection};";
        $styles[]   = "-ms-flex-direction: {$model->flexDirection};";
        $styles[]   = "-webkit-flex-direction: {$model->flexDirection};";

        $flexPack   = CBIE10Flexbox::justifyContentToFlexPack($model->flexJustifyContent);
        $styles[]   = "justify-content: {$model->flexJustifyContent};";
        $styles[]   = "-ms-flex-pack: {$flexPack};";
        $styles[]   = "-webkit-justify-content: {$model->flexJustifyContent};";

        $styles     = implode(' ', $styles);

        CBHTMLOutput::addCSSURL(CBFlexBoxView::URL('CBFlexBoxView.css'));

        echo "<{$model->type} class=\"CBFlexBoxView\" style=\"{$styles}\">";

        array_walk($model->subviews, 'CBView::renderModelAsHTML');

        echo "</{$model->type}>";
    }

    /**
     * Returns a CSS safe string for the flex property. The string is guaranteed
     * to be safe, but not guaranteed to be valid.
     *
     * @return {string}
     */
    private static function specToFlexFlex($spec) {
        $safeValue = '/^[ \\t0-9a-zA-Z.]*$/';

        if (isset($spec->flexFlex) && preg_match($safeValue, $spec->flexFlex)) {
            return trim($spec->flexFlex);
        } else {
            return '0 1 auto';
        }
    }
    /**
     * @return {stdClass}
     */
    public static function specToModel(stdClass $spec) {
        $model                  = CBModels::modelWithClassName(__CLASS__);
        $model->backgroundColor = isset($spec->backgroundColor) ?
                                  CBFlexBoxView::textToCSSValue($spec->backgroundColor) : null;
        $model->flexFlex        = CBFlexBoxView::specToFlexFlex($spec);
        $model->height          = isset($spec->height) ? CBFlexBoxView::valueToPixelExtent($spec->height) : null;
        $model->imageURL        = isset($spec->imageURL) ? CBFlexBoxView::URLToCSS($spec->imageURL) : '';
        $model->subviews        = isset($spec->subviews) ? array_map('CBView::specToModel', $spec->subviews) : [];
        $model->width           = isset($spec->width) ? CBFlexBoxView::valueToPixelExtent($spec->width) : null;
        $type                   = isset($spec->type) ? trim($spec->type) : '';

        switch ($type) {
            case 'article':
                $model->type = 'article';
                break;
            case 'main':
                $model->type = 'main';
                break;
            default:
                $model->type = 'div';
        }

        $backgroundPositionX    = isset($spec->backgroundPositionX) ? trim($spec->backgroundPositionX) : '';

        switch ($backgroundPositionX) {
            case 'left':
            case 'right':
                $model->backgroundPositionX = $backgroundPositionX;
                break;
            default:
                $model->backgroundPositionX = 'center';
        }

        $backgroundPositionY    = isset($spec->backgroundPositionY) ? trim($spec->backgroundPositionY) : '';

        switch ($backgroundPositionY) {
            case 'center':
            case 'bottom':
                $model->backgroundPositionY = $backgroundPositionY;
                break;
            default:
                $model->backgroundPositionY = 'top';
        }

        $backgroundRepeatX      = isset($spec->backgroundRepeatX) ? trim($spec->backgroundRepeatX) : '';

        switch ($backgroundRepeatX) {
            case 'repeat':
                $model->backgroundRepeatX = $backgroundRepeatX;
                break;
            default:
                $model->backgroundRepeatX = 'no-repeat';
        }

        $backgroundRepeatY      = isset($spec->backgroundRepeatY) ? trim($spec->backgroundRepeatY) : '';

        switch ($backgroundRepeatY) {
            case 'repeat':
                $model->backgroundRepeatY = $backgroundRepeatY;
                break;
            default:
                $model->backgroundRepeatY = 'no-repeat';
        }

        $flexAlignItems         = isset($spec->flexAlignItems) ? trim($spec->flexAlignItems) : '';

        switch ($flexAlignItems) {
            case 'flex-start':
            case 'flex-end':
            case 'center':
            case 'baseline':
                $model->flexAlignItems = $flexAlignItems;
                break;
            default:
                $model->flexAlignItems = 'stretch';
        }

        $flexAlignSelf          = isset($spec->flexAlignSelf) ? trim($spec->flexAlignSelf) : '';

        switch ($flexAlignSelf) {
            case 'flex-start':
            case 'flex-end':
            case 'center':
            case 'baseline':
            case 'stretch':
                $model->flexAlignSelf = $flexAlignSelf;
                break;
            default:
                $model->flexAlignSelf = 'auto';
        }

        $flexDirection          = isset($spec->flexDirection) ? trim($spec->flexDirection) : '';

        switch ($flexDirection) {
            case 'row-reverse':
            case 'column':
            case 'column-reverse':
                $model->flexDirection = $flexDirection;
                break;
            default:
                $model->flexDirection = 'row';
        }

        $flexJustifyContent     = isset($spec->flexJustifyContent) ? trim($spec->flexJustifyContent) : '';

        switch ($flexJustifyContent) {
            case 'flex-end':
            case 'center':
            case 'space-between':
            case 'space-around':
                $model->flexJustifyContent = $flexJustifyContent;
                break;
            default:
                $model->flexJustifyContent = 'flex-start';
        }

        return $model;
    }

    /**
     * @return {string} | null
     */
    public static function textToCSSValue($text) {
        $value = trim(str_replace(['<', '>', '"', '\'', ';'], '', $text));
        return empty($value) ? null : $value;
    }

    /**
     * @return {string}
     */
    public static function URL($filename) {
        return CBSystemURL . "/classes/CBFlexBoxView/{$filename}";
    }

    /**
     * This function detects the following characters in a URL:
     *
     *          '  "  <  >  &  (  )
     *
     * If the URL contains one or more of these characters it is considered
     * invalid for the purposes of this view due to the way it will be embedded
     * in the style property of the element and the encertainties on how those
     * characters should or even can be escaped propertly.  Otherise the URL is
     * trimmed and returned.
     *
     * @return {string}
     */
    public static function URLToCSS($URL) {
        if (preg_match('/[\'"<>&()]/', $URL)) {
            return '';
        } else {
            return trim($URL);
        }
    }

    /**
     * @return {int}|{float}|null
     */
    public static function valueToPixelExtent($value) {
        if (is_numeric($value)) {
            $number = $value + 0;
            if ($number > 0) {
                return $number;
            }
        }

        return null;
    }
}
