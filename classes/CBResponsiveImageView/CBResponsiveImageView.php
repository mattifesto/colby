<?php

final class CBResponsiveImageView {

    /**
     * @param hex160 $imageThemeID
     *
     * @return string
     */
    public static function imageThemeIDToStyleSheetFilepath($imageThemeID) {
        return CBDataStore::filepath([
            'ID' => $imageThemeID,
            'filename' => 'CBResponsiveImageView.css',
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
            'filename' => 'CBResponsiveImageView.css',
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

    /**
     * @param stdClass $model
     *
     * @return null
     */
    public static function renderModelAsHTML(stdClass $model) {
        if (empty($model->imageThemeID)) {
            $class = "";
        } else {
            CBHTMLOutput::addCSSURL(CBResponsiveImageView::imageThemeIDToStyleSheetURL($model->imageThemeID));
            $class = "T{$model->imageThemeID}";
        }

        ?><figure class="CBResponsiveImageView <?= $class ?> image">CBResponsiveImageView</figure><?php
    }

    /**
     * @param stdClass $spec
     *
     * @return stdClass
     */
    public static function specToModel(stdClass $spec) {
        $model = (object)['className' => __CLASS__];
        $model->imageThemeID = CBModel::value($spec, 'imageThemeID');

        return $model;
    }

    /**
     * @return string?
     */
    public static function specToImageThemeCSS(stdClass $spec, array $args) {
        $imageThemeID = null;
        extract($args, EXTR_IF_EXISTS);

        $class = "T{$imageThemeID}";
        $URLForSmallImage2x = self::imageToURL($spec->smallImage);
        $URLForMediumImage2x = self::imageToURL($spec->mediumImage);
        $URLFor1920Image2x = self::imageToURL($spec->largeImage, ['base' => 'cwc3840']);
        $URLForLargeImage2x = self::imageToURL($spec->largeImage);
        $URLForSmallImage = self::imageToURL($spec->smallImage, ['base' => 's0.5']);
        $URLForMediumImage = self::imageToURL($spec->mediumImage, ['base' => 's0.5']);
        $URLFor1920Image = self::imageToURL($spec->largeImage, ['base' => 'cwc3840s0.5']);
        $URLForLargeImage = self::imageToURL($spec->largeImage, ['base' => 's0.5']);

        $widthForSmall = intval($spec->smallImage->width / 2);
        $widthForMedium = intval($spec->mediumImage->width / 2);
        $widthForLarge = intval($spec->largeImage->width / 2);

        $heightForSmall = intval($spec->smallImage->height / 2);
        $heightForMedium = intval($spec->mediumImage->height / 2);
        $heightForLarge = intval($spec->largeImage->height / 2);

        if ($spec->largeImage->width > 3380) {
            $media1920 = <<<EOT

@media (max-width: 1920px) {
    .{$class} {
        background-image: url({$URLFor1920Image});
        background-size: 1920px {$heightForLarge}px;
    }

    .{$class}.image {
        min-height: {$heightForLarge}px;
    }
}

@media (max-width: 1920px) and (-webkit-min-device-pixel-ratio: 1.5), (max-width: 1068px) and (min-resolution: 144dpi)
{
    .{$class} {
        background-image: url({$URLFor1920Image2x});
    }
}

EOT;
        } else {
            $media1920 = null;
        }

        return <<<EOT

.{$class} {
    background-image: url({$URLForLargeImage});
    background-size: {$widthForLarge}px {$heightForLarge}px;
}

.{$class}.image {
    background-position: center top; /* move these properties to shared stylesheet */
    background-repeat: no-repeat;
    min-height: {$heightForLarge}px;
}

@media (-webkit-min-device-pixel-ratio: 1.5), (min-resolution: 144dpi) {
    .{$class} {
        background-image: url({$URLForLargeImage2x});
    }
}

{$media1920}

@media (max-width: 1068px) {
    .{$class} {
        background-image: url({$URLForMediumImage});
        background-size: {$widthForMedium}px {$heightForMedium}px;
    }

    .{$class}.image {
        min-height: {$heightForMedium}px;
    }
}

@media (max-width: 1068px) and (-webkit-min-device-pixel-ratio: 1.5), (max-width: 1068px) and (min-resolution: 144dpi)
{
    .{$class} {
        background-image: url({$URLForMediumImage2x});
    }
}

@media (max-width: 735px) {
    .{$class} {
        background-image: url({$URLForSmallImage});
        background-size: {$widthForSmall}px {$heightForSmall}px;
    }

    .{$class}.image {
        min-height: {$heightForSmall}px;
    }
}

@media (max-width: 735px) and (-webkit-min-device-pixel-ratio: 1.5), (max-width: 735px) and (min-resolution: 144dpi)
{
    .{$class} {
        background-image: url({$URLForSmallImage2x});
    }
}

EOT;
    }

    /**
     * return hex160?
     */
    public static function specToImageThemeID(stdClass $spec) {
        if (!empty($spec->largeImage->ID) && !empty($spec->mediumImage->ID) && !empty($spec->smallImage->ID)) {
            return sha1("{$spec->largeImage->ID}{$spec->mediumImage->ID}{$spec->smallImage->ID}");
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

        if (!empty($spec->largeImage->ID) && !empty($spec->mediumImage->ID) && !empty($spec->smallImage->ID)) {
            $imageThemeID = sha1("{$spec->largeImage->ID}{$spec->mediumImage->ID}{$spec->smallImage->ID}");
            $filepath = CBResponsiveImageView::imageThemeIDToStyleSheetFilepath($imageThemeID);

            CBDataStore::makeDirectoryForID($imageThemeID);

            file_put_contents($filepath, CBResponsiveImageView::specToImageThemeCSS($spec, [
                'imageThemeID' => $imageThemeID,
            ]));

            $response->imageThemeID = $imageThemeID;
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
