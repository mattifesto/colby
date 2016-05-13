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
            $themeID = CBStandardPageTitleAndDescriptionTheme::ID;
        };

        CBTheme::useThemeWithID($themeID);

        $class = implode(' ', CBTheme::IDToCSSClasses($themeID));
        $class = "CBPageTitleAndDescriptionView {$class}";

        $context = CBPageContext::current();

        ?><header class="<?= $class ?>"><?php
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

        return (object)[
            'className' => __CLASS__,
            'descriptionColor' => $colorForProperty('descriptionColor'),
            'hideDescription' => CBModel::value($spec, 'hideDescription', false, 'boolval'),
            'publishedColor' => $colorForProperty('publishedColor'),
            'showPublicationDate' => CBModel::value($spec, 'showPublicationDate', false, 'boolval'),
            'themeID' => CBModel::value($spec, 'themeID'),
            'titleColor' => $colorForProperty('titleColor'),
        ];
    }
}
