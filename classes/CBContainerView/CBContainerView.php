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
 *      - localCSS (stylesTemplate): no longer uses the CBTheme class
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
    public static function imageThemeIDToStyleSheetFilepath($imageThemeID) {
        return CBDataStore::filepath([
            'ID' => $imageThemeID,
            'filename' => 'CBContainerViewImageSetTheme.css',
        ]);
    }

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
     * @deprecated transitioning to CBDataStore::flexpath()
     *
     * @param stdClass $image
     * @param string? $filename
     *
     * @return string
     */
    private static function imageToURL(stdClass $image, $filename = null) {
        if ($filename === null) {
            if (empty($image->filename)) {
                // NOTE: 2017.01.30 Old backward compatability with 'base',
                //       figure out when we can remove.
                $filename = $image->base;
            } else {
                $filename = $image->filename;
            }
        }

        $basename = "{$filename}.{$image->extension}";

        return CBDataStore::flexpath($image->ID, $basename, CBSitePreferences::siteURL());
    }

    /**
     * @deprecated use CBContainerView::modelToImageCSS()
     *
     * @param stdClass $spec
     *
     * @return null
     */
    private static function makeImagesForSpec(stdClass $spec) {
        if (!empty($spec->smallImage->ID)) {
            CBImages::reduceImage($spec->smallImage->ID, $spec->smallImage->extension, 's0.5');
        }

        if (!empty($spec->mediumImage->ID)) {
            CBImages::reduceImage($spec->mediumImage->ID, $spec->mediumImage->extension, 's0.5');
        }

        if (!empty($spec->largeImage->ID)) {
            CBImages::reduceImage($spec->largeImage->ID, $spec->largeImage->extension, 's0.5');

            if ($spec->largeImage->width > 3840) {
                CBImages::reduceImage($spec->largeImage->ID, $spec->largeImage->extension, 'cwc3840');
                CBImages::reduceImage($spec->largeImage->ID, $spec->largeImage->extension, 'cwc3840s0.5');
            }
        }
    }

    /**
     * @deprecated use CBContainerView::mediaRuleRetinaOnly()
     *
     * 2016.03.30
     *
     * I am not modifying this function because I have decided to use only
     * retina images. In the future there will be no non-retina displays and
     * raising the compression on a retina image often creates a better images
     * of equal size. However, if this decision backfires I don't want to have
     * to rewrite this code so I am leaving this function here for now.
     *
     * @return string
     */
    private static function mediaRule(array $args) {
        $class = $maxWidth = $imageURL1x = $imageURL2x = $width = $height = null;
        extract($args, EXTR_IF_EXISTS);

        $rule = <<<EOT

            .{$class} {
                background-image: url({$imageURL1x});
                background-size: {$width}px {$height}px;
            }

            .{$class}.useImageHeight {
                min-height: {$height}px;
            }

EOT;

        $rule2x = <<<EOT

            .{$class} {
                background-image: url({$imageURL2x});
            }

EOT;

        $comment = '/* rule for unlimited width */';
        $mediaRuleForPixelRatio = '(-webkit-min-device-pixel-ratio: 1.5)';
        $mediaRuleForResolution = '(min-resolution: 144dpi)';

        if (isset($maxWidth)) {
            $comment = "/* rule for max-width: {$maxWidth}px */";
            $mediaRuleForWidth = "(max-width: {$maxWidth}px)";
            $mediaRuleForPixelRatio = "{$mediaRuleForWidth} and {$mediaRuleForPixelRatio}";
            $mediaRuleForResolution = "{$mediaRuleForWidth} and {$mediaRuleForResolution}";

            $rule = "@media {$mediaRuleForWidth} {\n{$rule}\n}";
        }

        $mediaRuleFor2x = "{$mediaRuleForPixelRatio}, {$mediaRuleForResolution}";
        $rule2x = "@media {$mediaRuleFor2x} {\n{$rule2x}\n}";

        return "{$comment}\n{$rule}\n\n{$rule2x}";
    }

    /**
     * @return string
     */
    private static function mediaRuleRetinaOnly(array $args) {
        $class = $maxWidth = $imageURL1x = $imageURL2x = $width = $height = null;
        extract($args, EXTR_IF_EXISTS);

        $rule = <<<EOT

            .{$class} {
                background-image: url({$imageURL2x});
                background-size: {$width}px {$height}px;
            }

            .{$class}.useImageHeight {
                min-height: {$height}px;
            }

EOT;

        $comment = '/* rule for unlimited width */';

        if (isset($maxWidth)) {
            $comment = "/* rule for max-width: {$maxWidth}px */";
            $mediaRuleForWidth = "(max-width: {$maxWidth}px)";

            $rule = "@media {$mediaRuleForWidth} {\n{$rule}\n}";
        }

        return "{$comment}\n{$rule}";
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
     * @param stdClass $model
     *
     * @return string
     */
    public static function modelToSearchText(stdClass $model) {
        if (isset($model->subviews)) {
            $texts = array_map('CBView::modelToSearchText', $model->subviews);
            $texts = array_filter($texts, function ($text) { return !empty($text); });
            return implode(' ', $texts);
        }

        return '';
    }

    /**
     * @param [object]? $model->subviews;
     *
     * @return null
     */
    static function renderModelAsHTML(stdClass $model) {
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
            $classes[] = "T{$model->imageThemeID}";
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
            array_walk($model->subviews, 'CBView::renderModelAsHTML');
        ?></<?= $tagName ?>><?php
    }

    /**
     * @return [string]
     */
    static function requiredCSSURLs() {
        return [Colby::flexnameForCSSForClass(CBSystemURL, __CLASS__)];
    }

    /**
     * @param stdClass $spec
     *
     * @return stdClass
     */
    public static function specToModel(stdClass $spec) {
        $model = (object)[
            'className' => __CLASS__,
            'largeImage' => CBModel::valueAsSpecToModel($spec, 'largeImage', 'CBImage'),
            'mediumImage' => CBModel::valueAsSpecToModel($spec, 'mediumImage', 'CBImage'),
            'smallImage' => CBModel::valueAsSpecToModel($spec, 'smallImage', 'CBImage'),
            'subviews' => CBModel::namedSpecArrayToModelArray($spec, 'subviews'),
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

    /**
     * @deprecated use CBContainerView::modelToImageCSS()
     *
     * @return string?
     */
    public static function specToImageThemeCSS(stdClass $spec, array $args) {
        $imageThemeID = null;
        extract($args, EXTR_IF_EXISTS);

        $class = "T{$imageThemeID}";
        $rules = [];

        if (isset($spec->largeImage)) {
            $imageURL2x = CBContainerView::imageToURL($spec->largeImage);
            $imageURL1x = CBContainerView::imageToURL($spec->largeImage, 's0.5');
            $width = intval($spec->largeImage->width / 2);
            $height = intval($spec->largeImage->height / 2);

            $rules[] = [
                'class' => $class,
                'maxWidth' => 2560,
                'imageURL1x' => $imageURL1x,
                'imageURL2x' => $imageURL2x,
                'width' => $width,
                'height' => $height,
            ];

            if ($spec->largeImage->width > 3380) {
                $imageURL2x = CBContainerView::imageToURL($spec->largeImage, 'cwc3840');
                $imageURL1x = CBContainerView::imageToURL($spec->largeImage, 'cwc3840s0.5');
                $width = 1920;

                $rules[] = [
                    'class' => $class,
                    'maxWidth' => 1920,
                    'imageURL1x' => $imageURL1x,
                    'imageURL2x' => $imageURL2x,
                    'width' => $width,
                    'height' => $height,
                ];
            }
        }

        if (isset($spec->mediumImage)) {
            $imageURL2x = CBContainerView::imageToURL($spec->mediumImage);
            $imageURL1x = CBContainerView::imageToURL($spec->mediumImage, 's0.5');
            $width = intval($spec->mediumImage->width / 2);
            $height = intval($spec->mediumImage->height / 2);

            $rules[] = [
                'class' => $class,
                'maxWidth' => 1068,
                'imageURL1x' => $imageURL1x,
                'imageURL2x' => $imageURL2x,
                'width' => $width,
                'height' => $height,
            ];
        }

        if (isset($spec->smallImage)) {
            $imageURL2x = CBContainerView::imageToURL($spec->smallImage);
            $imageURL1x = CBContainerView::imageToURL($spec->smallImage, 's0.5');
            $width = intval($spec->smallImage->width / 2);
            $height = intval($spec->smallImage->height / 2);

            $rules[] = [
                'class' => $class,
                'maxWidth' => 735,
                'imageURL1x' => $imageURL1x,
                'imageURL2x' => $imageURL2x,
                'width' => $width,
                'height' => $height,
            ];
        }

        $rules[0]['maxWidth'] = null;
        $rules = array_map('CBContainerView::mediaRuleRetinaOnly', $rules);

        return implode("\n\n", $rules);
    }

    /**
     * @deprecated use CBContainerView::modelToImageCSS()
     *
     * return hex160?
     */
    public static function specToImageThemeID(stdClass $spec) {
        $largeImageID = isset($spec->largeImage->ID) ? $spec->largeImage->ID : null;
        $mediumImageID = isset($spec->mediumImage->ID) ? $spec->mediumImage->ID : null;
        $smallImageID = isset($spec->smallImage->ID) ? $spec->smallImage->ID : null;

        if (isset($largeImageID) || isset($mediumImageID) || isset($smallImageID)) {
            return sha1("CBContainerView Large: {$largeImageID} Medium: {$mediumImageID} Small: {$smallImageID}");
        } else {
            return null;
        }
    }

    /**
     * @deprecated use CBContainerView::modelToImageCSS()
     *
     * @return null
     */
    public static function updateStylesForAjax() {
        $response = new CBAjaxResponse();
        $spec = json_decode($_POST['specAsJSON']);

        CBContainerView::makeImagesForSpec($spec);

        if ($imageThemeID = CBContainerView::specToImageThemeID($spec)) {
            $filepath = CBContainerView::imageThemeIDToStyleSheetFilepath($imageThemeID);

            CBDataStore::makeDirectoryForID($imageThemeID);

            file_put_contents($filepath, CBContainerView::specToImageThemeCSS($spec, [
                'imageThemeID' => $imageThemeID,
            ]));
        }

        $response->imageThemeID = $imageThemeID;
        $response->wasSuccessful = true;
        $response->send();
    }

    /**
     * @deprecated use CBContainerView::modelToImageCSS()
     *
     * @return null
     */
    public static function updateStylesForAjaxPermissions() {
        return (object)['group' => 'Administrators'];
    }
}
