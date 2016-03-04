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
        $classes = ['CBPageTitleAndDescriptionView'];

        if (!empty($model->themeID)) {
            CBHTMLOutput::addCSSURL(CBTheme::IDToCSSURL($model->themeID));
            $classes[] = CBTheme::IDToCSSClass($model->themeID);
        }

        $context = CBPageContext::current();

        ?><header class="<?= implode(' ', $classes) ?>"><?php
            if (!empty($context->titleAsHTML)) {
                echo "<h1>{$context->titleAsHTML}</h1>";
            }

            if (!empty($context->descriptionAsHTML) && empty($model->hideDescription)) {
                echo "<div class='description'>{$context->descriptionAsHTML}</div>";
            }

            if (!empty($model->showPublicationDate)) {
                $publishedAsHTML = ColbyConvert::timestampToHTML($context->publishedTimestamp, 'Unpublished');
                echo "<div class='published'>{$publishedAsHTML}</div>";
            }
        ?></header><?php
    }

    /**
     * @param stdClass $spec
     *
     * @return stdClass
     */
    public static function specToModel(stdClass $spec) {
        return (object)[
            'className' => __CLASS__,
            'hideDescription' => CBModel::value($spec, 'hideDescription', false, 'boolval'),
            'showPublicationDate' => CBModel::value($spec, 'showPublicationDate', false, 'boolval'),
            'themeID' => CBModel::value($spec, 'themeID'),
        ];
    }
}
