<?php

final class CBBackgroundView {

    /**
     * @param model $spec
     *
     * @return ?model
     */
    static function CBModel_build(stdClass $spec): ?stdClass {
        $model = (object)[];

        $model->color = isset($spec->color) ? $spec->color : null;
        $model->colorHTML = ColbyConvert::textToHTML($model->color);
        $model->imageHeight = isset($spec->imageHeight) ? $spec->imageHeight : null;
        $model->imageShouldRepeatHorizontally = isset($spec->imageShouldRepeatHorizontally) ?
                                                    $spec->imageShouldRepeatHorizontally : false;
        $model->imageShouldRepeatVertically = isset($spec->imageShouldRepeatVertically) ?
                                                    $spec->imageShouldRepeatVertically : false;
        $model->imageURL = isset($spec->imageURL) ? $spec->imageURL : null;
        $model->imageURLHTML = ColbyConvert::textToHTML($model->imageURL);
        $model->imageWidth = isset($spec->imageWidth) ? $spec->imageWidth : null;
        $model->minimumViewHeightIsImageHeight = isset($spec->minimumViewHeightIsImageHeight) ?
                                                    $spec->minimumViewHeightIsImageHeight : true;

        /* image (added 2017.09.12) */

        if ($imageSpec = CBModel::valueAsModel($spec, 'image', ['CBImage'])) {
            $model->image = CBModel::build($imageSpec);
        }

        /* children */

        $model->children = [];
        $subviewSpecs = CBModel::valueToArray($spec, 'children');

        foreach($subviewSpecs as $subviewSpec) {
            if ($subviewModel = CBModel::build($subviewSpec)) {
                $model->children[] = $subviewModel;
            }
        }

        return $model;
    }

    /**
     * @param object $model
     *
     * @return string
     */
    static function CBModel_toSearchText(stdClass $model) {
        $subviews = CBModel::valueAsObjects($model, 'children');
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
        $styles = [];
        $styles[] = "display: flex; display: -ms-flexbox; display: -webkit-flex;";
        $styles[] = "justify-content: center; -ms-flex-pack: center; -webkit-justify-content: center;";
        $styles[] = "flex-wrap: wrap; -ms-flex-wrap: wrap; -webkit-flex-wrap: wrap;";

        if ($model->imageURL) {
            $styles[] = "background-image: url({$model->imageURLHTML});";
            $styles[] = "background-position: center top;";

            if ($model->imageShouldRepeatVertically) {
                if ($model->imageShouldRepeatHorizontally) {
                    $repeat = "repeat";
                } else {
                    $repeat = "repeat-y";
                }
            } else if ($model->imageShouldRepeatHorizontally) {
                $repeat = "repeat-x";
            } else {
                $repeat = "no-repeat";
            }

            $styles[] = "background-repeat: {$repeat};";
        }

        if (!empty($model->color)) {
            $styles[] = "background-color: {$model->colorHTML};";
        }

        if ($model->minimumViewHeightIsImageHeight) {
            $styles[] = "min-height: {$model->imageHeight}px;";
        }

        $styles = implode(' ', $styles);

        ?>

        <div class="CBBackgroundView" style="<?= $styles; ?>">
            <?php array_walk($model->children, 'CBView::render'); ?>
        </div>

        <?php
    }

    /**
     * @param object $spec
     *
     * @return [object]
     */
    static function CBView_toSubviews(stdClass $spec) {
        return CBModel::valueAsArray($spec, 'children');
    }
}
