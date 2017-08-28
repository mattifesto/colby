<?php

/**
 * @NOTE 2017.07.20
 *
 * This class has updated the way it accomplishes image styling. Older instances
 * will still work but will be updated to the new method if resaved.
 *
 * A few features have been completely removed:
 *
 *      - Themes: these have been deprecated in Colby for a while now.
 *      - backgroundImage: this property was used for the background gradent
 *          propery in the editor was rarely used and should be moved to local
 *          CSS
 *      - backgroundPositionY: this property should be moved to local CSS
 */
final class CBContainerView {

    /**
     * @deprecated use CBContainerView::modelToImageCSS()
     *
     * @param hex160 $imageThemeID
     *
     * @return string
     */
    public static function imageThemeIDToStyleSheetURL($imageThemeID) {
        return CBDataStore::toURL([
            'ID' => $imageThemeID,
            'filename' => 'CBContainerViewImageSetTheme.css',
        ]);
    }

    /**
     * @param object $model
     * @param string $CSSClassName
     *
     * @return string
     */
    static function modelToImageCSS(stdClass $model, $CSSClassName) {
        $blocks = [];
        $maxWidth = null;
        $function = function ($CSSClassName, $image, $maxWidth = null) {
            $imageHeight = $image->height / 2;
            $imageWidth = $image->width / 2;
            $URL = CBDataStore::flexpath($image->ID, "original.{$image->extension}", CBSiteURL);
            $CSS = <<<EOT

.{$CSSClassName} {
    background-image: url({$URL});
    background-size: {$imageWidth}px {$imageHeight}px;
    min-height: {$imageHeight}px;
}

EOT;

            if (!empty($maxWidth)) {
                $CSS = <<<EOT

@media (max-width: {$maxWidth}px) {
{$CSS}
}

EOT;
            }

            return $CSS;
        };

        if (!empty($model->largeImage)) {
            $blocks[] = call_user_func($function, $CSSClassName, $model->largeImage);
        }

        if (!empty($model->mediumImage)) {
            if (!empty($blocks)) {
                $maxWidth = 1068;
            }

            $blocks[] = call_user_func($function, $CSSClassName, $model->mediumImage, $maxWidth);
        }

        if (!empty($model->smallImage)) {
            if (!empty($blocks)) {
                $maxWidth = 735;
            }

            $blocks[] = call_user_func($function, $CSSClassName, $model->smallImage, $maxWidth);
        }

        if (!empty($blocks)) {
            array_unshift($blocks, '/* CBContainerView Images */');
        }

        return implode("\n\n", $blocks);
    }

    /**
     * @param object $model
     *
     * @return string
     */
    static function CBModel_toSearchText(stdClass $model) {
        $subviews = CBModel::valueAsObjects($model, 'subviews');
        $strings = array_map('CBModel::toSearchText', $subviews);
        $strings = array_filter($strings);
        return implode(' ', $strings);
    }

    /**
     * @param object $model
     *
     * @return null
     */
    static function CBView_render(stdClass $model) {
        $classes = ['CBContainerView'];
        $tagName = empty($model->tagName) ? 'div' : $model->tagName;

        if ($tagName === 'a' && !empty($model->HREFAsHTML)) {
            $HREF = " href=\"{$model->HREFAsHTML}\"";
        } else {
            $HREF = null;
        }

        if (empty($model->imageThemeID)) {
            $CSSClassName = 'ID_' . CBHex160::random();
            CBHTMLOutput::addCSS(CBContainerView::modelToImageCSS($model, $CSSClassName));
            $classes[] = $CSSClassName;
        } else {
            CBHTMLOutput::addCSSURL(CBContainerView::imageThemeIDToStyleSheetURL($model->imageThemeID));
            $classes[] = "T{$model->imageThemeID} useImageHeight";
        }

        /**
         * @NOTE 2017.07.20
         *
         * $model->styleID is deprecated and has the class name for the local
         * styles will be included in CSSClassNames by specToModel(). This code
         * should stay while the old instances age out. A resave updates to the
         * new method.
         */

        if (!empty($model->stylesID)) {
            $classes[] = "T{$model->stylesID}";
        }

        /* CSS class names */

        if (!empty($model->CSSClassNames) && is_array($model->CSSClassNames)) {
            array_walk($model->CSSClassNames, 'CBHTMLOutput::requireClassName');

            $classes = array_unique(array_merge($classes, $model->CSSClassNames));
        }

        /* render */

        $classes = implode(' ', $classes);
        $styles = [];
        if (!empty($model->backgroundColor)) { $styles[] = "background-color: {$model->backgroundColor}"; }
        $styles = empty($styles) ? '' : ' style="' . implode('; ', $styles) . '"';

        if (!empty($model->stylesCSS)) {
            CBHTMLOutput::addCSS($model->stylesCSS);
        }

        ?><<?= $tagName, $HREF ?> class="<?= $classes ?>"<?= $styles ?>><?php
            array_walk($model->subviews, 'CBView::render');
        ?></<?= $tagName ?>><?php
    }

    /**
     * @return [string]
     */
    static function requiredCSSURLs() {
        return [Colby::flexnameForCSSForClass(CBSystemURL, __CLASS__)];
    }

    /**
     * @param object $spec
     *
     * @return object
     */
    static function CBModel_toModel(stdClass $spec) {
        $model = (object)[
            'className' => __CLASS__,
            'largeImage' => CBModel::valueAsSpecToModel($spec, 'largeImage', 'CBImage'),
            'mediumImage' => CBModel::valueAsSpecToModel($spec, 'mediumImage', 'CBImage'),
            'smallImage' => CBModel::valueAsSpecToModel($spec, 'smallImage', 'CBImage'),
            'subviews' => CBModel::valueToModels($spec, 'subviews'),
        ];
        $model->backgroundColor = CBModel::value($spec, 'backgroundColor', null, 'CBConvert::stringToCSSColor');
        $model->HREF = CBModel::value($spec, 'HREF');
        $model->HREFAsHTML = cbhtml($model->HREF);
        $model->tagName = CBModel::value($spec, 'tagName');

        switch ($model->tagName) {
            case 'article':
            case 'section':
            case 'a':
                break;
            default:
                unset($model->tagName);
                break;
        }

        /* CSS class names */

        $CSSClassNames = CBModel::value($spec, 'CSSClassNames', '');
        $CSSClassNames = preg_split('/[\s,]+/', $CSSClassNames, null, PREG_SPLIT_NO_EMPTY);

        if ($CSSClassNames === false) {
            throw new RuntimeException("preg_split() returned false");
        }

        $model->CSSClassNames = $CSSClassNames;

        // localCSS (stylesTemplate)
        // This code differs from standard for backward compatability reasons.
        $localCSSTemplate = CBModel::value($spec, 'stylesTemplate', '', 'trim');

        if (!empty($localCSSTemplate)) {
            $localCSSClassName = 'ID_' . CBHex160::random();
            $model->CSSClassNames[] = $localCSSClassName;
            $model->stylesCSS = CBView::localCSSTemplateToLocalCSS($localCSSTemplate, 'view', ".{$localCSSClassName}");
        }

        return $model;
    }
}
