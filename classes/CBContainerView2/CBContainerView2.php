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

        if (!empty($model->localCSS)) {
            $styleSheet = <<<EOT

/* CBContainerView2 */

{$model->localCSS}

EOT;

            CBHTMLOutput::addCSS($styleSheet);
        }

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
        $model = (object)[
            'className' => __CLASS__,
            'CSSClassNames' => CBModel::valueAsNames($spec, 'CSSClassNames'),
            'image' => CBModel::valueAsSpecToModel($spec, 'image', 'CBImage'),
            'subviews' => CBModel::namedSpecArrayToModelArray($spec, 'subviews'),
        ];

        // localCSS
        $localCSSTemplate = CBModel::value($spec, 'localCSSTemplate', '', 'trim');

        if (!empty($localCSSTemplate)) {
            $localCSSClassName = 'ID_' . CBHex160::random();
            $model->CSSClassNames[] = $localCSSClassName;
            $model->localCSS = CBView::localCSSTemplateToLocalCSS($localCSSTemplate, 'view', ".{$localCSSClassName}");
        }

        return $model;
    }
}
