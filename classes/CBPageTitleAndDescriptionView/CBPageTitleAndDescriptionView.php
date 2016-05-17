<?php

final class CBPageTitleAndDescriptionView {

    /**
     * @param bool? $model->hideDescription
     * @param bool? $model->showPublicationDate
     * @param hex160? $model->themeID
     *
     *
     * @return null
     */
    public static function renderModelAsHTML(stdClass $model) {
        if (empty($themeID = CBModel::value($model, 'themeID'))) {
            $themeID = CBWellKnownThemeForPageTitleAndDescription::ID;
        };

        CBTheme::useThemeWithID($themeID);

        $class = implode(' ', CBTheme::IDToCSSClasses($themeID));
        $class = "CBPageTitleAndDescriptionView {$class}";

        if (!empty($model->stylesID)) {
            $stylesClass = CBTheme::IDToCSSClass($model->stylesID);
            $class = "{$class} {$stylesClass}";
        }

        if (empty($model->stylesID)) {
            $styleElement = null;
        } else {
            $styleElement = "<style scoped>{$model->stylesCSS}</style>";
        }

        $context = CBPageContext::current();

        ?><header class="<?= $class ?>"><?php
            echo $styleElement;

            if (!empty($context->titleAsHTML)) {
                if (empty($model->titleColor)) {
                    $style = '';
                } else {
                    $style = " style='color: {$model->titleColor}'";
                }
                echo "<h1{$style}>{$context->titleAsHTML}</h1>";
            }

            if (!empty($context->descriptionAsHTML) && empty($model->hideDescription)) {
                if (empty($model->descriptionColor)) {
                    $style = '';
                } else {
                    $style = " style='color: {$model->descriptionColor}'";
                }
                echo "<div class='description'{$style}>{$context->descriptionAsHTML}</div>";
            }

            if (!empty($model->showPublicationDate)) {
                if (empty($model->publishedColor)) {
                    $style = '';
                } else {
                    $style = " style='color: {$model->publishedColor}'";
                }
                $publishedAsHTML = ColbyConvert::timestampToHTML($context->publishedTimestamp, 'Unpublished');
                echo "<div class='published'{$style}>{$publishedAsHTML}</div>";
            }
        ?></header><?php
    }

    /**
     * @param stdClass $spec
     *
     * @return stdClass
     */
    public static function specToModel(stdClass $spec) {
        $colorForProperty = function ($propertyName) use ($spec) {
            return CBModel::value($spec, $propertyName, null, 'CBConvert::stringToCSSColor');
        };

        $model = (object)[
            'className' => __CLASS__,
            'descriptionColor' => $colorForProperty('descriptionColor'),
            'hideDescription' => CBModel::value($spec, 'hideDescription', false, 'boolval'),
            'publishedColor' => $colorForProperty('publishedColor'),
            'showPublicationDate' => CBModel::value($spec, 'showPublicationDate', false, 'boolval'),
            'themeID' => CBModel::value($spec, 'themeID'),
            'titleColor' => $colorForProperty('titleColor'),
        ];

        /* view styles */

        $stylesTemplate = empty($spec->stylesTemplate) ? '' : trim($spec->stylesTemplate);

        if (!empty($stylesTemplate)) {
            $model->stylesID = CBHex160::random();
            $model->stylesCSS = CBTheme::stylesTemplateToStylesCSS($stylesTemplate, $model->stylesID);
        }

        return $model;
    }
}
