<?php

final class CBContainerView {

    /**
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

        return CBDataStore::flexpath($image->ID, $basename, CBSiteURL);
    }

    /**
     * @param stdClass $spec
     *
     * @return null
     */
    private static function makeImagesForSpec(stdClass $spec) {
        if (!empty($spec->smallImage->ID)) {
            CBImages::makeImage($spec->smallImage->ID, 's0.5');
        }

        if (!empty($spec->mediumImage->ID)) {
            CBImages::makeImage($spec->mediumImage->ID, 's0.5');
        }

        if (!empty($spec->largeImage->ID)) {
            CBImages::makeImage($spec->largeImage->ID, 's0.5');

            if ($spec->largeImage->width > 3840) {
                CBImages::makeImage($spec->largeImage->ID, 'cwc3840');
                CBImages::makeImage($spec->largeImage->ID, 'cwc3840s0.5');
            }
        }
    }

    /**
     * @deprecated use mediaRuleRetinaOnly
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
     * @param [stdClass]? $model->subviews;
     * @param hex160? $model->themeID;
     * @param bool? $model->useImageHeight;
     *
     * @return null
     */
    public static function renderModelAsHTML(stdClass $model) {
        $themeID = empty($model->themeID) ? null : $model->themeID;

        CBHTMLOutput::addCSSURL(Colby::flexnameForCSSForClass(CBSystemURL, __CLASS__));
        CBHTMLOutput::addCSSURL(CBTheme::IDToCSSURL($themeID));

        $classes = ['CBContainerView'];
        $tagName = empty($model->tagName) ? 'div' : $model->tagName;

        if ($tagName === 'a' && !empty($model->HREFAsHTML)) {
            $HREF = " href=\"{$model->HREFAsHTML}\"";
        } else {
            $HREF = null;
        }

        if (!empty($model->imageThemeID)) {
            CBHTMLOutput::addCSSURL(CBContainerView::imageThemeIDToStyleSheetURL($model->imageThemeID));
            $classes[] = "T{$model->imageThemeID}";
        }

        if (!empty($model->useImageHeight)) {
            $classes[] = "useImageHeight";
        }

        /**
         * 2016.09.23
         * The line below should call CBTheme::IDToCSSClasses().
         * The line for the stylesID below that should call a different function
         * that only returns a class name for an ID. (No "NoTheme" class.)
         */

        $classes[] = CBTheme::IDToCSSClass($themeID);

        if (!empty($model->stylesID)) {
            $classes[] = CBTheme::IDToCSSClass($model->stylesID);
        }

        $classes = implode(' ', $classes);
        $styles = [];
        if (!empty($model->backgroundColor)) { $styles[] = "background-color: {$model->backgroundColor}"; }
        if (!empty($model->backgroundImage)) { $styles[] = "background-image: {$model->backgroundImage}"; }
        if (!empty($model->backgroundPositionY)) { $styles[] = "background-position: center {$model->backgroundPositionY}"; }
        $styles = empty($styles) ? '' : ' style="' . implode('; ', $styles) . '"';

        ?><<?= $tagName, $HREF ?> class="<?= $classes ?>"<?= $styles ?>><?php
            if (!empty($model->stylesCSS)) {
                echo "<style>{$model->stylesCSS}</style>";
            }

            array_walk($model->subviews, 'CBView::renderModelAsHTML');
        ?></<?= $tagName ?>><?php
    }

    /**
     * @param stdClass $spec
     *
     * @return stdClass
     */
    public static function specToModel(stdClass $spec) {
        $model = (object)['className' => __CLASS__];
        $model->backgroundColor = CBModel::value($spec, 'backgroundColor', null, 'CBConvert::stringToCSSColor');
        $model->backgroundImage = CBModel::value($spec, 'backgroundImage', null, 'CBConvert::stringToCSSBackgroundImage');
        $model->backgroundPositionY = CBModel::value($spec, 'backgroundPositionY', null, 'CBConvert::stringToCSSValue');
        $model->imageThemeID = CBModel::value($spec, 'imageThemeID');
        $model->HREF = CBModel::value($spec, 'HREF');
        $model->HREFAsHTML = cbhtml($model->HREF);
        $model->subviews = array_map('CBView::specToModel', isset($spec->subviews) ? $spec->subviews : []);
        $model->tagName = CBModel::value($spec, 'tagName');
        $model->themeID = CBModel::value($spec, 'themeID');
        $model->useImageHeight = CBModel::value($spec, 'useImageHeight', false, 'boolval');

        switch ($model->tagName) {
            case 'article':
            case 'section':
            case 'a':
                break;
            default:
                unset($model->tagName);
                break;
        }

        /* view styles */

        $stylesTemplate = empty($spec->stylesTemplate) ? '' : trim($spec->stylesTemplate);

        if (!empty($stylesTemplate)) {
            $model->stylesID = CBHex160::random();
            $model->stylesCSS = CBTheme::stylesTemplateToStylesCSS($stylesTemplate, $model->stylesID);
        }

        return $model;
    }

    /**
     * @return string?
     */
    public static function specToImageThemeCSS(stdClass $spec, array $args) {
        $imageThemeID = null;
        extract($args, EXTR_IF_EXISTS);

        $class = "T{$imageThemeID}";
        $rules = [];

        if (isset($spec->largeImage)) {
            $imageURL2x = self::imageToURL($spec->largeImage);
            $imageURL1x = self::imageToURL($spec->largeImage, 's0.5');
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
                $imageURL2x = self::imageToURL($spec->largeImage, 'cwc3840');
                $imageURL1x = self::imageToURL($spec->largeImage, 'cwc3840s0.5');
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
            $imageURL2x = self::imageToURL($spec->mediumImage);
            $imageURL1x = self::imageToURL($spec->mediumImage, 's0.5');
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
            $imageURL2x = self::imageToURL($spec->smallImage);
            $imageURL1x = self::imageToURL($spec->smallImage, 's0.5');
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
     * //TODO: You should save a model here for the theme probably. Discuss.
     * @return null
     */
    public static function updateStylesForAjax() {
        $response = new CBAjaxResponse();
        $spec = json_decode($_POST['specAsJSON']);

        self::makeImagesForSpec($spec);

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
     * @return null
     */
    public static function updateStylesForAjaxPermissions() {
        return (object)['group' => 'Administrators'];
    }
}
