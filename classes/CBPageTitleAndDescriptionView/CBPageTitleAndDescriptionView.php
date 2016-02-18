<?php

final class CBPageTitleAndDescriptionView {

    /**
     * @param stdClass $model
     *
     * @return null
     */
    public static function renderModelAsHTML(stdClass $model) {
        CBHTMLOutput::addCSSURL(CBTheme::IDToCSSURL($model->themeID));

        $classes = [
            'CBPageTitleAndDescriptionView',
            CBTheme::IDToCSSClass($model->themeID),
        ];

        $context = CBPageContext::current();

        ?><header class="<?= implode(' ', $classes) ?>"><?php
            if (!empty($context->titleAsHTML)) {
                echo "<h1>{$context->titleAsHTML}</h1>";
            }

            if (!empty($context->descriptionAsHTML)) {
                echo "<div class='description'>{$context->descriptionAsHTML}</div>";
            }

            if ($model->showPublicationDate) {
                $publishedAsHTML = ColbyConvert::timestampToHTML($context->publishedTimestamp);
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
            'showPublicationDate' => CBModel::value($spec, 'showPublicationDate', false, 'boolval'),
            'themeID' => CBModel::value($spec, 'themeID'),
        ];
    }
}
