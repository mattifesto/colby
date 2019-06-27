<?php

/**
 * @NOTE 2017_07_20
 *
 * This class has updated the way it accomplishes image styling. Older instances
 * will still work but will be updated to the new method if resaved.
 *
 * A few features have been completely removed:
 *
 *      - Themes: these have been deprecated in Colby for a while now.
 *      - backgroundImage: this property was used for the background gradent
 *          propery in the editor was rarely used and should be moved to local
 *          CSS
 *      - backgroundPositionY: this property should be moved to local CSS
 */
final class CBContainerView {

    /**
     * @param model $spec
     *
     * @return model
     */
    static function CBModel_upgrade(stdClass $spec): stdClass {
        foreach(['smallImage', 'mediumImage', 'largeImage'] as $name) {
            if ($imageSpec = CBModel::valueAsObject($spec, $name)) {
                $spec->{$name} = CBImage::fixAndUpgrade($imageSpec);
            }
        }

        $spec->subviews = array_values(array_filter(array_map(
            'CBModel::upgrade',
            CBModel::valueToArray($spec, 'subviews')
        )));

        return $spec;
    }

    /**
     * @deprecated use CBContainerView::modelToImageCSS()
     *
     * @param hex160 $imageThemeID
     *
     * @return string
     */
    static function imageThemeIDToStyleSheetURL($imageThemeID) {
        return CBDataStore::toURL([
            'ID' => $imageThemeID,
            'filename' => 'CBContainerViewImageSetTheme.css',
        ]);
    }

    /**
     * @param object $model
     * @param string $CSSClassName
     *
     * @return string
     */
    static function modelToImageCSS(stdClass $model, $CSSClassName) {
        $blocks = [];
        $maxWidth = null;
        $function = function ($CSSClassName, $image, $maxWidth = null) {
            $imageHeight = $image->height / 2;
            $imageWidth = $image->width / 2;

            $URL = CBDataStore::flexpath(
                $image->ID,
                "original.{$image->extension}",
                cbsiteurl()
            );

            $CSS = <<<EOT

.{$CSSClassName} {
    background-image: url({$URL});
    background-size: {$imageWidth}px {$imageHeight}px;
    min-height: {$imageHeight}px;
}

EOT;

            if (!empty($maxWidth)) {
                $CSS = <<<EOT

@media (max-width: {$maxWidth}px) {
{$CSS}
}

EOT;
            }

            return $CSS;
        };

        if (!empty($model->largeImage)) {
            $blocks[] = call_user_func(
                $function,
                $CSSClassName,
                $model->largeImage
            );
        }

        if (!empty($model->mediumImage)) {
            if (!empty($blocks)) {
                $maxWidth = 1068;
            }

            $blocks[] = call_user_func(
                $function,
                $CSSClassName,
                $model->mediumImage,
                $maxWidth
            );
        }

        if (!empty($model->smallImage)) {
            if (!empty($blocks)) {
                $maxWidth = 735;
            }

            $blocks[] = call_user_func(
                $function,
                $CSSClassName,
                $model->smallImage,
                $maxWidth
            );
        }

        if (!empty($blocks)) {
            array_unshift($blocks, '/* CBContainerView Images */');
        }

        return implode("\n\n", $blocks);
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
     * @param model $model
     *
     * @return void
     */
    static function CBView_render(stdClass $model): void {
        $classes = ['CBContainerView'];
        $tagName = empty($model->tagName) ? 'div' : $model->tagName;

        if ($tagName === 'a' && !empty($model->HREFAsHTML)) {
            $HREF = " href=\"{$model->HREFAsHTML}\"";
        } else {
            $HREF = null;
        }

        if ($imageThemeID = CBModel::valueAsID($model, 'imageThemeID')) {
            CBHTMLOutput::addCSSURL(
                CBContainerView::imageThemeIDToStyleSheetURL(
                    $model->imageThemeID
                )
            );

            $classes[] = "T{$imageThemeID} useImageHeight";
        } else {
            $CSSClassName = 'ID_' . CBHex160::random();

            CBHTMLOutput::addCSS(
                CBContainerView::modelToImageCSS($model, $CSSClassName)
            );

            $classes[] = $CSSClassName;
        }

        /**
         * @NOTE 2017_07_20
         *
         * $model->styleID is deprecated and has the class name for the local
         * styles will be included in CSSClassNames by CBModel_toModel(). This
         * code should stay while the old instances age out. A resave updates to
         * the new method.
         */

        if ($stylesID = CBModel::valueAsID($model, 'stylesID')) {
            $classes[] = "T{$stylesID}";
        }

        /* CSS class names */

        if ($CSSClassNames = CBModel::valueToArray($model, 'CSSClassNames')) {
            array_walk($CSSClassNames, 'CBHTMLOutput::requireClassName');

            $classes = array_unique(array_merge($classes, $CSSClassNames));
        }

        /* render */

        $classes = implode(' ', $classes);
        $styles = [];

        if ($backgroundColor = CBModel::valueToString($model, 'backgroundColor')) {
            $styles[] = "background-color: {$backgroundColor}";
        }

        $styles = implode('; ', $styles);

        if ($stylesCSS = CBModel::valueToString($model, 'stylesCSS')) {
            CBHTMLOutput::addCSS($stylesCSS);
        }

        ?>

        <<?= $tagName . $HREF ?> class="<?= $classes ?>" style="<?= $styles ?>">

            <?php

            $subviewModels = CBModel::valueToArray($model, 'subviews');

            array_walk($subviewModels, 'CBView::render');

            ?>

        </<?= $tagName ?>>

        <?php
    }


    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [
            Colby::flexpath(__CLASS__, 'css', cbsysurl()),
        ];
    }


    /**
     * @param model $spec
     *
     * @return ?model
     */
    static function CBModel_build(stdClass $spec): ?stdClass {
        $model = (object)[
            'largeImage' => CBModel::build(
                CBModel::valueAsModel($spec, 'largeImage', ['CBImage'])
            ),
            'mediumImage' => CBModel::build(
                CBModel::valueAsModel($spec, 'mediumImage', ['CBImage'])
            ),
            'smallImage' => CBModel::build(
                CBModel::valueAsModel($spec, 'smallImage', ['CBImage'])
            ),
        ];

        $model->backgroundColor = CBModel::value(
            $spec,
            'backgroundColor',
            null,
            'CBConvert::stringToCSSColor'
        );

        $model->HREF = CBModel::value($spec, 'HREF');
        $model->HREFAsHTML = cbhtml($model->HREF);
        $model->tagName = CBModel::value($spec, 'tagName');

        switch ($model->tagName) {
            case 'article':
            case 'section':
            case 'a':
                break;
            default:
                unset($model->tagName);
                break;
        }

        /* subviews */

        $model->subviews = [];
        $subviewSpecs = CBModel::valueToArray($spec, 'subviews');

        foreach($subviewSpecs as $subviewSpec) {
            if ($subviewModel = CBModel::build($subviewSpec)) {
                $model->subviews[] = $subviewModel;
            }
        }

        /* CSS class names */

        $CSSClassNames = CBModel::value($spec, 'CSSClassNames', '');

        $CSSClassNames = preg_split(
            '/[\s,]+/',
            $CSSClassNames,
            null,
            PREG_SPLIT_NO_EMPTY
        );

        if ($CSSClassNames === false) {
            throw new RuntimeException("preg_split() returned false");
        }

        $model->CSSClassNames = $CSSClassNames;

        // localCSS (stylesTemplate)
        // This code differs from standard for backward compatability reasons.
        $localCSSTemplate = CBModel::value($spec, 'stylesTemplate', '', 'trim');

        if (!empty($localCSSTemplate)) {
            $localCSSClassName = 'ID_' . CBHex160::random();
            $model->CSSClassNames[] = $localCSSClassName;

            $model->stylesCSS = CBView::localCSSTemplateToLocalCSS(
                $localCSSTemplate,
                'view',
                ".{$localCSSClassName}"
            );
        }

        return $model;
    }
}
