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
     * @param model $spec
     *
     * @return ?model
     */
    static function CBModel_build(stdClass $spec): ?stdClass {
        return (object)[
            'description' => CBModel::valueToString($spec, 'description'),
            'isPublished' => (bool)CBModel::value($spec, 'isPublished'),
            'publicationTimeStamp' => CBModel::valueAsInt($spec, 'publicationTimeStamp'),
            'URI' => CBModel::valueToString($spec, 'URI'),
        ];
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
     * @param [model] $models
     *
     * @return void
     */
    static function CBModels_willSave(array $models): void {
        CBPages::save($models);
    }

    /**
     * @param [hex160] $IDs
     *
     * @return void
     */
    static function CBModels_willDelete(array $IDs): void {
        CBPages::deletePagesByID($IDs);
    }

    /**
     * @param model $model
     *
     * @return void
     */
    static function CBPage_render(stdClass $model): void {
        $title = CBConvert::valueToString(CBModel::value($model, 'title'));

        CBHTMLOutput::begin();
        CBHTMLOutput::pageInformation()->title = $title;
        CBHTMLOutput::pageInformation()->classNameForPageSettings = 'CBPageSettingsForResponsivePages';

        ?>

        <h1 style="padding: 100px; text-align: center;"><?= cbhtml($title) ?></h1>

        <?php

        CBHTMLOutput::render();
    }
}
