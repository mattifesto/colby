<?php

final class CBBackgroundView {

    /**
     * @return string
     */
    public static function modelToSearchText(stdClass $model = null) {
        if (isset($model->children)) {
            $text = array_map('CBView::modelToSearchText', $model->children);

            return implode(' ', $text);
        }

        return '';
    }

    /**
     * @return void
     */
    public static function renderModelAsHTML(stdClass $model = null) {
        if (!$model) {
            $model = self::specToModel();
        }

        include __DIR__ . '/CBBackgroundViewHTML.php';
    }

    /**
     * @return stdClass
     */
    public static function specToModel(stdClass $spec = null) {
        $model                                  = CBView::modelWithClassName(__CLASS__);
        $model->color                           = isset($spec->color) ? $spec->color : null;
        $model->colorHTML                       = ColbyConvert::textToHTML($model->color);
        $model->imageHeight                     = isset($spec->imageHeight) ? $spec->imageHeight : null;
        $model->imageShouldRepeatHorizontally   = isset($spec->imageShouldRepeatHorizontally) ?
                                                    $spec->imageShouldRepeatHorizontally : false;
        $model->imageShouldRepeatVertically     = isset($spec->imageShouldRepeatVertically) ?
                                                    $spec->imageShouldRepeatVertically : false;
        $model->imageURL                        = isset($spec->imageURL) ? $spec->imageURL : null;
        $model->imageURLHTML                    = ColbyConvert::textToHTML($model->imageURL);
        $model->imageWidth                      = isset($spec->imageWidth) ? $spec->imageWidth : null;
        $model->minimumViewHeightIsImageHeight  = isset($spec->minimumViewHeightIsImageHeight) ?
                                                    $spec->minimumViewHeightIsImageHeight : true;

        $subviewSpecs       = isset($spec->children) ? $spec->children : [];
        $model->children    = array_map('CBView::specToModel', $subviewSpecs);

        return $model;
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
