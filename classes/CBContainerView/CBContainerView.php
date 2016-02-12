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
     * @param stdClass $image
     * @param string? $args->base
     *
     * @return string
     */
    private static function imageToURL(stdClass $image, $args = []) {
        $base = null;
        extract($args, EXTR_IF_EXISTS);

        if ($base === null) {
            $filename = "{$image->base}.{$image->extension}";
        } else {
            $filename = "{$base}.{$image->extension}";
        }

        return CBDataStore::toURL([
            'ID' => $image->ID,
            'filename' => $filename,
        ]);
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
     * @param stdClass $model
     *
     * @return string
     */
    public static function modelToSearchText(stdClass $model) {
        if (isset($model->subviews)) {
            $text = array_map('CBView::modelToSearchText', $model->subviews);

            return implode(' ', $text);
        }

        return '';
    }

    /**
     * @param stdClass $model
     *
     * @return null
     */
    public static function renderModelAsHTML(stdClass $model) {
        CBHTMLOutput::addCSSURL(CBContainerView::URL('CBContainerView.css'));
        CBHTMLOutput::addCSSURL(CBTheme::IDToCSSURL($model->themeID));

        $classes = ['CBContainerView'];

        if (!empty($model->imageThemeID)) {
            CBHTMLOutput::addCSSURL(CBContainerView::imageThemeIDToStyleSheetURL($model->imageThemeID));
            $classes[] = "T{$model->imageThemeID}";
        }

        if ($model->useImageHeight) {
            $classes[] = "useImageHeight";
        }

        if (($class = CBTheme::IDToCSSClass($model->themeID)) !== null) {
            $classes[] = $class;
        }

        $classes = implode(' ', $classes);

        ?><section class="<?= $classes ?>"><?php
            array_walk($model->subviews, 'CBView::renderModelAsHTML');
        ?></section><?php
    }

    /**
     * @param stdClass $spec
     *
     * @return stdClass
     */
    public static function specToModel(stdClass $spec) {
        $model = (object)['className' => __CLASS__];
        $model->imageThemeID = CBModel::value($spec, 'imageThemeID');
        $model->subviews = array_map('CBView::specToModel', isset($spec->subviews) ? $spec->subviews : []);
        $model->themeID = CBModel::value($spec, 'themeID');
        $model->useImageHeight = CBModel::value($spec, 'useImageHeight', false, 'boolval');

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
            $imageURL1x = self::imageToURL($spec->largeImage, ['base' => 's0.5']);
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
                $imageURL2x = self::imageToURL($spec->largeImage, ['base' => 'cwc3840']);
                $imageURL1x = self::imageToURL($spec->largeImage, ['base' => 'cwc3840s0.5']);
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
            $imageURL1x = self::imageToURL($spec->mediumImage, ['base' => 's0.5']);
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
            $imageURL1x = self::imageToURL($spec->smallImage, ['base' => 's0.5']);
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
        $rules = array_map('CBContainerView::mediaRule', $rules);

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
