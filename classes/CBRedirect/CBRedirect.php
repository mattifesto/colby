<?php

/**
 * Models of this class are added to the ColbyPages table. When rendered, they
 * will redirect to another page. This is a better alternative than adding
 * redirects to the .htaccess file because these redirects can be better managed
 * by administrators of the website.
 */
final class CBRedirect {

    /**
     * @param object $spec
     *
     *      {
     *          URI: string
     *
     *              The URI that should be redirected.
     *
     *              Example: "blog-posts/my-day"
     *
     *          redirectToURI: string
     *
     *              The destination URI.
     *
     *              Example: "blog/my-day"
     *      }
     *
     * @return ?object
     */
    static function CBModel_build(stdClass $spec): ?stdClass {
        $model = (object)[
            'isPublished' => !empty($spec->isPublished),
            'publicationTimeStamp' => CBModel::valueAsInt($spec, 'publicationTimeStamp'),
            'redirectToURI' => CBConvert::stringToURI(CBModel::valueToString($spec, 'redirectToURI')),
            'URI' => CBConvert::stringToURI(CBModel::valueToString($spec, 'URI')),
        ];

        $ID = CBModel::valueAsID($spec, 'ID');

        if (empty($model->URI) && !empty($ID)) {
            $model->URI = $ID;
        }

        if ($model->publicationTimeStamp === null && $model->isPublished) {
            $model->publicationTimeStamp = time();
        }

        return $model;
    }

    /**
     * @param [ID] $IDs
     *
     * @return null
     */
    static function CBModels_willDelete(array $IDs) {
        CBPages::deletePagesByID($IDs);
        CBPages::deletePagesFromTrashByID($IDs);
    }

    /**
     * @param [model] $models
     *
     * @return null
     */
    static function CBModels_willSave(array $models) {
        CBPages::save($models);
    }

    /**
     * @param model $model
     *
     * @return ?model
     */
    static function CBPage_render(stdClass $model): void {
        $URI = CBModel::valueToString($model, 'redirectToURI');

        if (empty($URI)) {
            throw new Exception('The redirectToURI property value is empty.');
        }

        $URI = "/{$URI}/";

        error_log($URI);

        if ($queryString = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY)) {
            $URI = "{$URI}?{$queryString}";
        }

        header("Location: {$URI}", true, 301 /* permanent */);

        exit;
    }
}
