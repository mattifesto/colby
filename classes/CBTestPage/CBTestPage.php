<?php

/**
 * Most page models are created using the CBViewPage className. However, it's
 * relatively easy to create pages using a custom class name for cases where
 * this is appropriate.
 *
 * These cases are rare, though, and it's more likely to be beneficial to create
 * custom views and layouts instead.
 *
 * This is an example of creating a custom page class, and CBTestPageTests uses
 * this page class. Use it as an example when creating a custom page class.
 */
final class CBTestPage {

    /**
     * @param [stdClass] $tuples
     *
     * @return null
     */
    static function modelsWillSave(array $tuples) {
        $models = array_map(function($tuple) { return $tuple->model; }, $tuples);
        CBPages::save($models);
    }

    /**
     * @param [hex160] $IDs
     *
     * @return null
     */
    static function modelsWillDelete(array $IDs) {
        CBPages::deletePagesByID($IDs);
    }

    /**
     * @param model $model
     *
     * @return string
     */
    static function CBModel_toSearchText(stdClass $model): string {
        return implode(
            ' ',
            [
                CBModel::valueToString($model, 'title'),
                CBModel::valueToString($model, 'description'),
            ]
        );
    }

    /**
     * @param model $model
     *
     * @return null
     */
    static function CBPage_render(stdClass $model) {
        $title = CBConvert::valueToString(CBModel::value($model, 'title'));

        CBHTMLOutput::$classNameForSettings = 'CBPageSettingsForResponsivePages';
        CBHTMLOutput::begin();
        CBHTMLOutput::pageInformation()->title = $title;

        ?>

        <h1 style="padding: 100px; text-align: center;"><?= cbhtml($title) ?></h1>

        <?php

        CBHTMLOutput::render();
    }

    /**
     * @param model $spec
     *
     * @return model
     */
    static function CBModel_toModel(stdClass $spec) {
        return (object)[
            'className' => __CLASS__,
            'title' => CBModel::value($spec, 'title', ''),
            'description' => CBModel::value($spec, 'description', ''),
            'isPublished' => CBModel::value($spec, 'isPublished'),
            'publicationTimeStamp' => CBModel::value($spec, 'publicationTimeStamp'),
            'URI' => CBModel::value($spec, 'URI'),
        ];
    }
}
