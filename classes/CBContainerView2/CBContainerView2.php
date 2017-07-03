<?php

final class CBContainerView2 {

    /**
     * @param object $model
     *
     * @return null
     */
    static function renderModelAsHTML(stdClass $model) {
        $CSSClassNames = CBModel::valueAsArray($model, 'CSSClassNames');

        array_walk($CSSClassNames, 'CBHTMLOutput::requireClassName');

        $CSSClassNames = cbhtml(implode(' ', $CSSClassNames));

        if (empty($model->image)) {
            $backgroundImageDeclaration = '';
        } else {
            $image = $model->image;
            $imageURLAsHTML = cbhtml(CBDataStore::flexpath($image->ID, "original.{$image->extension}", CBSiteURL));
            $backgroundImageDeclaration = "background-image: url('{$imageURLAsHTML}')";
        }

        $subviews = CBModel::valueAsArray($model, 'subviews');

        ?>

        <div class="CBContainerView2 <?= $CSSClassNames ?>" style="<?= $backgroundImageDeclaration ?>">
            <?php array_walk($subviews, 'CBView::renderModelAsHTML') ?>
        </div>

        <?php
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
    static function specToModel(stdClass $spec) {
        return (object)[
            'className' => __CLASS__,
            'CSSClassNames' => CBModel::value($spec, 'CSSClassNames', [], function ($value) {
                if (!is_string($value)) {
                    return [];
                }

                $names = preg_split('/[\s,]+/', $value, null, PREG_SPLIT_NO_EMPTY);

                if ($names === false) {
                    throw new RuntimeException("preg_split() returned false");
                }

                return $names;
            }),
            'image' => CBModel::value($spec, 'image', null, 'CBImage::specToModel'),
            'subviews' => CBModel::namedSpecArrayToModelArray($spec, 'subviews'),
        ];
    }
}
