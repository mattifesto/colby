<?php

final class CBBackgroundView {

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
            'imageShouldRepeatHorizontally' => !empty($spec->imageShouldRepeatHorizontally),
            'imageShouldRepeatVertically' => !empty($spec->imageShouldRepeatVertically),
            'minimumViewHeightIsImageHeight' => !empty($spec->minimumViewHeightIsImageHeight),
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
     * @return void
     */
    static function CBModel_upgrade(stdClass $spec): void {
        if ($imageSpec = CBModel::valueAsObject($spec, 'image')) {
            $spec->image = CBImage::fixAndUpgrade($imageSpec);
        }

        $subviewSpecs = CBModel::valueToArray($spec, 'children');
        $spec->children = [];

        foreach ($subviewSpecs as $subviewSpec) {
            if ($subviewSpec = CBConvert::valueAsModel($subviewSpec)) {
                $spec->children[] = CBModel::upgrade($subviewSpec);
            }
        }
    }

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
     * @param object $spec
     *
     * @return [object]
     */
    static function CBView_toSubviews(stdClass $spec) {
        return CBModel::valueAsArray($spec, 'children');
    }
}
