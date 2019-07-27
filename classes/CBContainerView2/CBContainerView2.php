<?php

final class CBContainerView2 {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */

    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [
            Colby::flexpath(__CLASS__, 'css', cbsysurl()),
        ];
    }


    /* -- CBInstall interfaces -- -- -- -- -- */

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        CBViewCatalog::installView(
            __CLASS__
        );
    }
    /* CBInstall_install() */


    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return [
            'CBViewCatalog',
        ];
    }
    /* CBInstall_requiredClassNames() */


    /* -- CBModel interfaces -- -- -- -- -- */

    /**
     * @param model $spec
     *
     * @return ?object
     */
    static function CBModel_build(stdClass $spec): ?stdClass {
        $model = (object)[
            'CSSClassNames' => CBModel::valueToNames($spec, 'CSSClassNames'),
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
            $model->localCSS = CBView::localCSSTemplateToLocalCSS(
                $localCSSTemplate,
                'view',
                ".{$localCSSClassName}"
            );
        }

        return $model;
    }

    /**
     * @param model $model
     *
     * @return string
     */
    static function CBModel_toSearchText(stdClass $model): string {
        return implode(
            ' ',
            array_map(
                'CBModel::toSearchText',
                CBModel::valueToArray($model, 'subviews')
            )
        );
    }


    /**
     * @param model $spec
     *
     * @return model
     */
    static function CBModel_upgrade(stdClass $spec): stdClass {
        if ($imageSpec = CBModel::valueAsObject($spec, 'image')) {
            $spec->image = CBImage::fixAndUpgrade($imageSpec);
        }

        $spec->subviews = array_values(array_filter(array_map(
            'CBModel::upgrade',
            CBModel::valueToArray($spec, 'subviews')
        )));

        return $spec;
    }


    /* -- CBView interfaces -- -- -- -- -- */

    /**
     * @param model $model
     *
     * @return void
     */
    static function CBView_render(stdClass $model): void {
        $CSSClassNames = CBModel::valueToArray($model, 'CSSClassNames');

        array_walk($CSSClassNames, 'CBHTMLOutput::requireClassName');

        $CSSClassNames = cbhtml(implode(' ', $CSSClassNames));

        if (empty($model->image)) {
            $backgroundImageDeclaration = '';
        } else {
            $image = $model->image;

            $imageURLAsHTML = cbhtml(
                CBDataStore::flexpath(
                    $image->ID,
                    "original.{$image->extension}",
                    CBSiteURL
                )
            );

            $backgroundImageDeclaration =
            "background-image: url('{$imageURLAsHTML}')";
        }

        $subviews = CBModel::valueToArray($model, 'subviews');

        if (!empty($model->localCSS)) {
            $styleSheet = <<<EOT

/* CBContainerView2 */

{$model->localCSS}

EOT;

            CBHTMLOutput::addCSS($styleSheet);
        }

        ?>

        <div class="CBContainerView2 <?=
            $CSSClassNames
        ?>" style="<?=
            $backgroundImageDeclaration
        ?>">
            <?php array_walk($subviews, 'CBView::render') ?>
        </div>

        <?php
    }
    /* CBView_render() */
}
