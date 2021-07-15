<?php

final class
CBBackgroundView {

    /* -- CBHTMLOutput interfaces -- */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_CSSURLs(
    ) {
        return [
            Colby::flexpath(
                __CLASS__,
                'v675.34.css',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_CSSURLs() */



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
     * @return ?model
     */
    static function CBModel_build(stdClass $spec): ?stdClass {
        $model = (object)[
            'color' => CBModel::valueToString($spec, 'color'),
            'imageHeight' => CBModel::valueAsInt($spec, 'imageHeight'),
            'imageWidth' => CBModel::valueAsInt($spec, 'imageWidth'),
            'imageURL' => CBModel::valueToString($spec, 'imageURL'),
            'imageShouldRepeatHorizontally' => CBModel::valueToBool(
                $spec,
                'imageShouldRepeatHorizontally'
            ),
            'imageShouldRepeatVertically' => CBModel::valueToBool(
                $spec,
                'imageShouldRepeatVertically'
            ),
            'minimumViewHeightIsImageHeight' => CBModel::valueToBool(
                $spec,
                'minimumViewHeightIsImageHeight'
            ),
        ];

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
     * @param model $model
     *
     * @return string
     */
    static function CBModel_toSearchText(stdClass $model): string {
        return implode(
            ' ',
            array_map(
                'CBModel::toSearchText',
                CBModel::valueToArray($model, 'children')
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

        $spec->children = array_values(array_filter(array_map(
            'CBModel::upgrade',
            CBModel::valueToArray($spec, 'children')
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
        $styles = [];
        $styles[] = "display: flex;";
        $styles[] = "justify-content: center;";
        $styles[] = "flex-wrap: wrap;";

        if ($imageURL = CBModel::valueToString($model, 'imageURL')) {
            $styles[] = "background-image: url(" . cbhtml($imageURL) . ");";
            $styles[] = "background-position: center top;";

            if (!empty($model->imageShouldRepeatVertically)) {
                if (!empty($model->imageShouldRepeatHorizontally)) {
                    $repeat = "repeat";
                } else {
                    $repeat = "repeat-y";
                }
            } else if (!empty($model->imageShouldRepeatHorizontally)) {
                $repeat = "repeat-x";
            } else {
                $repeat = "no-repeat";
            }

            $styles[] = "background-repeat: {$repeat};";
        }

        if ($color = CBModel::valueToString($model, 'color')) {
            $styles[] = "background-color: " . cbhtml($color). ";";
        }

        if (!empty($model->minimumViewHeightIsImageHeight)) {
            if ($imageHeight = CBModel::valueAsInt($model, 'imageHeight')) {
                $styles[] = "min-height: {$imageHeight}px;";
            }
        }

        $styles = implode(' ', $styles);

        ?>

        <div class="CBBackgroundView" style="<?= $styles ?>">
            <?php

            $subviewModels = CBModel::valueToArray($model, 'children');

            array_walk($subviewModels, 'CBView::render');

            ?>
        </div>

        <?php
    }


    /**
     * @param model $spec
     *
     * @return [model]
     */
    static function CBView_toSubviews(stdClass $spec): array {
        return CBModel::valueToArray($spec, 'children');
    }


    /**
     * @param model $model
     * @param [model] $subviews
     *
     * @return void
     */
    static function CBView_setSubviews(
        stdClass $model,
        array $subviews
    ): void {
        $model->children = $subviews;
    }
}
