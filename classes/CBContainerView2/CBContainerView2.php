<?php

final class CBContainerView2 {

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
     * @param model $spec
     *
     * @return void
     */
    static function CBModel_upgrade(stdClass $spec): void {
        if ($imageSpec = CBModel::valueAsObject($spec, 'image')) {
            $spec->image = CBImage::fixAndUpgrade($imageSpec);
        }

        $subviewSpecs = CBModel::valueToArray($spec, 'subviews');
        $spec->subviews = [];

        foreach ($subviewSpecs as $subviewSpec) {
            if ($subviewSpec = CBConvert::valueAsModel($subviewSpec)) {
                $spec->subviews[] = CBModel::upgrade($subviewSpec);
            }
        }
    }

    /**
     * @param object $model
     *
     * @return null
     */
    static function CBView_render(stdClass $model) {
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
            <?php array_walk($subviews, 'CBView::render') ?>
        </div>

        <?php
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [Colby::flexnameForCSSForClass(CBSystemURL, __CLASS__)];
    }

    /**
     * @param object $spec
     *
     * @return object
     */
    static function CBModel_build(stdClass $spec) {
        $model = (object)[
            'CSSClassNames' => CBModel::valueAsNames($spec, 'CSSClassNames'),
        ];

        /* image */

        if ($imageSpec = CBModel::valueAsModel($spec, 'image', ['CBImage'])) {
            $model->image = CBModel::build($imageSpec);
        }

        /* subviews */

        $model->subviews = [];
        $subviewSpecs = CBModel::valueToArray($spec, 'subviews');

        foreach($subviewSpecs as $subviewSpec) {
            if ($subviewModel = CBModel::build($subviewSpec)) {
                $model->subviews[] = $subviewModel;
            }
        }

        /* localCSS */

        $localCSSTemplate = CBModel::value($spec, 'localCSSTemplate', '', 'trim');

        if (!empty($localCSSTemplate)) {
            $localCSSClassName = 'ID_' . CBHex160::random();
            $model->CSSClassNames[] = $localCSSClassName;
            $model->localCSS = CBView::localCSSTemplateToLocalCSS($localCSSTemplate, 'view', ".{$localCSSClassName}");
        }

        return $model;
    }
}
