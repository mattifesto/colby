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

        ?>

        <header class="<?= implode(' ', $classes) ?>">
            <h1><?= CBHTMLOutput::titleAsHTML() ?></h1>
            <div class="description"><?= CBHTMLOutput::descriptionAsHTML() ?></div>
        </header>

        <?php
    }

    /**
     * @param stdClass $spec
     *
     * @return stdClass
     */
    public static function specToModel(stdClass $spec) {
        return (object)[
            'className' => __CLASS__,
            'themeID' => CBModel::value($spec, 'themeID'),
        ];
    }
}
