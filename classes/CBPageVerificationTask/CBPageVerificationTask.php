<?php

/**
 * @NOTE 2018_03_22
 *
 *      This class verifies rows in the ColbyPages table. It should not verify
 *      properties of a specific page model. That should be done in
 *      CBModel_upgrade() in the model class.
 *
 *      As this class is updated, good code will be moved to the front and code
 *      that belongs in other classes will be moved out. Comments should
 *      document each action.
 */
final class CBPageVerificationTask {

    static $messageContext = [];

    /**
     * @return [string]
     */
    static function allowedViewClassNames() {
        static $allowedViewClassNames;

        if ($allowedViewClassNames === null) {
            $function = 'CBPageHelpers::allowedViewClassNames';

            if (is_callable($function)) {
                $allowedViewClassNames =
                call_user_func($function);
            } else {
                $allowedViewClassNames =
                CBPagesPreferences::classNamesForSupportedViews();
            }
        }

        return $allowedViewClassNames;
    }
    /* allowedViewClassNames() */


    /**
     * @return void
     */
    static function CBInstall_install(): void {
        CBPageVerificationTask::startForAllPages();
    }


    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return [
            'CBImages',
            'CBLog',
            'CBModels',
            'CBPages',
            'CBTasks2',
        ];
    }


    /**
     * @param string $ID
     *
     *      The ID of the page to verify.
     *
     * @return object
     */
    static function CBTasks2_run(string $ID): stdClass {
        $messages = [];
        $resave = false;
        $severity = 7;
        $result = CBPageVerificationTask::run($ID);

        if (empty($result->hasColbyPagesRow)) {
            CBLog::log(
                (object)[
                    'sourceClassName' => __CLASS__,
                    'message' => 'No ColbyPages row exists for this ID',
                    'severity' => 4,
                ]
            );
        }

        if (empty($result->model)) {
            CBLog::log((
                object)[
                    'sourceClassName' => __CLASS__,
                    'message' => 'No model exists for this ID',
                    'severity' => 4,
                ]
            );
        }

        if (!empty($result->didDeleteRowWithoutModel)) {
            $message = <<<EOT

                The page row and data store were deleted by this task because no
                model exists for this ID

EOT;

            CBLog::log(
                (object)[
                    'sourceClassName' => __CLASS__,
                    'message' => $message,
                    'severity' => 4,
                ]
            );
        }

        if (!empty($result->renderError)) {
            CBErrorHandler::report($result->renderError);
        }

        /* first line of message */

        $pageTitle = CBModel::valueToString($result, 'model.title');
        $firstLine = "A page with the title \"{$pageTitle}\" was verified";

        array_unshift($messages, $firstLine);

        $modelClassName = CBModel::valueToString($result, 'model.className');

        if ($modelClassName == 'CBViewPage') {
            $imageValue = CBModel::value($result, 'spec.image');

            if (empty($imageValue)) {

                /**
                 * We only process `thumbnailURL` on the spec if `image` is not
                 * set. If `image` is set on the spec `thumbnailURL` will be
                 * ignored by CBViewPage::CBModel_build().
                 */

                $thumbnailURL = CBModel::value($result, 'spec.thumbnailURL');

                if ($thumbnailURL) {
                    $image = CBImages::URIToCBImage($thumbnailURL);

                    if ($image) {
                        $result->spec->image = $image;

                        $result->spec->deprecatedThumbnailURL =
                        $result->spec->thumbnailURL;

                        unset($result->spec->thumbnailURL);

                        $resave = true;
                        $message = <<<EOT

                            A CBViewPage spec upgraded its thumbnailURL

                            {$thumbnailURL}

                            to CBImage

                            {$result->spec->image->ID}

EOT;

                        CBLog::log(
                            (object)[
                                'className' => __CLASS__,
                                'sourceID' => (
                                    '0099cecb597038d4bf5f182965271e25cc60c070'
                                ),
                                'message' => $message,
                                'severity' => 6,
                            ]
                        );
                    }
                }
            } else {
                $imageID = CBModel::value($imageValue, 'ID');

                if (!CBImages::isInstance($imageID)) {
                    $imageValueAsMessage = CBMessageMarkup::stringToMarkup(
                        CBConvert::valueToPrettyJSON($imageValue)
                    );

                    $imageIDAsMessage = CBMessageMarkup::stringToMarkup(
                        CBConvert::valueToPrettyJSON($imageID)
                    );

                    $message = <<<EOT

                        The "image" property value of a page spec with the
                        title, "{$pageTitle}", is invalid.

                        The "image" property value is:

                        --- pre\n{$imageValueAsMessage}
                        ---

                        The "ID" property value of this value is:

                        --- pre\n{$imageIDAsMessage}
                        ---

                        When the ID property value was used as the parameter to
                        the (CBImage::isInstance\(\) (code)) function, the
                        function returned false.

EOT;

                    CBLog::log(
                        (object)[
                            'message' => $message,
                            'severity' => 4,
                            'sourceClassName' => __CLASS__,
                            'sourceID' => (
                                '4b30866e7e5e5edf42b7a5cab882b072ec144b75'
                            ),
                        ]
                    );
                }
            }

            /* check for deprecated views */

            $deprecatedViewClassNames =
            CBPageVerificationTask::findDeprecatedSubviewClassNames(
                $result->spec
            );

            if (!empty($deprecatedViewClassNames)) {
                $count = count($deprecatedViewClassNames);
                $countPlural = $count > 1 ? 's' : '';

                $uniqueClassNames = array_values(
                    array_unique($deprecatedViewClassNames)
                );

                $uniqueCount = count($uniqueClassNames);
                $uniqueCountPlural = $uniqueCount > 1 ? 'es' : '';
                $uniqueClassNames = implode("\n\n", $uniqueClassNames);
                $message = <<<EOT

                    The page "{$pageTitle}" has {$count} deprecated
                    view{$countPlural} using {$uniqueCount} deprecated view
                    class{$uniqueCountPlural}.

                    --- ul
                    {$uniqueClassNames}
                    ---

EOT;

                CBLog::log(
                    (object)[
                        'message' => $message,
                        'severity' => 4,
                        'sourceClassName' => __CLASS__,
                        'sourceID' => (
                            '06232e21d9ced6f7b8f91fb0f7ae381944e5f4f2'
                        ),
                    ]
                );
            }

            /* check for unsupported views */

            $unsupportedViewClassNames =
            CBPageVerificationTask::findUnsupportedSubviewClassNames(
                $result->spec
            );

            if (!empty($unsupportedViewClassNames)) {
                $count = count($unsupportedViewClassNames);
                $countPlural = $count > 1 ? 's' : '';

                $uniqueClassNames = array_values(
                    array_unique($unsupportedViewClassNames)
                );

                $uniqueCount = count($uniqueClassNames);
                $uniqueCountPlural = $uniqueCount > 1 ? 'es' : '';
                $uniqueClassNames = implode("\n\n", $uniqueClassNames);
                $message = <<<EOT

                    The page "{$pageTitle}" has {$count} unsupported
                    view{$countPlural} using {$uniqueCount} unsupported view
                    class{$uniqueCountPlural}.

                    --- ul
                    {$uniqueClassNames}
                    ---

EOT;

                CBLog::log(
                    (object)[
                        'message' => $message,
                        'severity' => 3,
                        'sourceClassName' => __CLASS__,
                        'sourceID' => (
                            'd8faccb5fe6161d7a61a12ddcdbc5b16f42c6e4d'
                        ),
                    ]
                );
            }
        }

        if ($resave) {
            CBModels::save($result->spec);
        }

        CBLog::log((object)[
            'className' => __CLASS__,
            'message' => implode("\n\n--- hr\n---\n\n", $messages),
            'severity' => $severity,
        ]);
    }

    /**
     * This function counts versions older than 30 days because CBModels:save()
     * will remove those versions.
     *
     * @param hex160 $ID
     *
     * @return int
     */
    static function fetchCountOfOldVersions($ID) {
        $IDAsSQL = CBHex160::toSQL($ID);
        $timestamp = time() - (60 * 60 * 24 * 30); // 30 days ago
        $SQL = <<<EOT

            SELECT  COUNT(*)
            FROM    `CBModelVersions`
            WHERE   `ID` = {$IDAsSQL} and
                    `timestamp` < {$timestamp}

EOT;

        return CBDB::SQLToValue($SQL);
    }

    /**
     * This function performs all the actions of the task but doesn't create any
     * log entries and returns its results in a simple object. This function
     * provides clear results which allows the task to be tested.
     *
     * @return object
     *
     *      {
     *          didDeleteRowWithoutModel: bool?
     *          hasColbyPagesRow: bool
     *          model: model?
     *          renderError: Throwable?
     *          spec: model?
     *      }
     */
    static function run(string $ID) {
        $result = (object)[];
        $IDAsSQL = CBHex160::toSQL($ID);

        /* check for ColbyPages row */

        $SQL = "SELECT COUNT(*) FROM ColbyPages WHERE archiveID = {$IDAsSQL}";
        $result->hasColbyPagesRow = !empty(CBDB::SQLToValue($SQL));

        /* check for CBModel */

        $data = CBModels::fetchSpecAndModelByID($ID);

        if (empty($data)) {
            if ($result->hasColbyPagesRow) {

                /**
                 * If there is a row in the pages table that does not have a
                 * model then it most likely represents a very old page. The row
                 * and the data store associated with the ID should be deleted.
                 */

                CBDataStore::deleteByID($ID);
                CBPages::deletePagesByID([$ID]);

                $result->didDeleteRowWithoutModel = true;
            }
        } else {
            $result->spec = $data->spec;
            $result->model = $data->model;

            /**
             * @BUG 2018_10_18
             *
             *      Excluded CBRedirect pages here because they emit headers
             *      that redirect the browser to a different page and don't
             *      allow the task to finish.
             *
             *      In the future every model should be checked by a standard
             *      model specific process (if one exists). This should be the
             *      process for CBViewPage models.
             */
            if ($data->spec->className !== 'CBRedirect') {
                ob_start();

                try {
                    CBPage::render($result->model);
                } catch (Throwable $throwable) {
                    $result->renderError = $throwable;
                }

                ob_end_clean();
            }
        }

        return $result;
    }


    /**
     * Start or restart the page verification task for all existing pages.
     */
    static function startForAllPages() {
        $IDs = CBDB::SQLToArray(
            'SELECT LOWER(HEX(archiveID)) FROM ColbyPages'
        );

        CBTasks2::restart(__CLASS__, $IDs, /* priority: */ 101);
    }


    /**
     * @return null
     */
    static function CBAjax_startForAllPages() {
        CBPageVerificationTask::startForAllPages();
    }


    /**
     * @return string
     */
    static function CBAjax_startForAllPages_group() {
        return 'Administrators';
    }


    /**
     * This function walks the subview tree of the model provided and returns a
     * non-unique array of deprecated subview class names.
     *
     * @param model $model
     *
     * @return [string]
     */
    static function findDeprecatedSubviewClassNames(stdClass $model): array {
        $data = (object)[
            'classNames' => [],
        ];

        CBView::walkSubviews(
            $model,
            function ($subview) use ($data) {
                $viewClassName = CBModel::valueToString($subview, 'className');

                if (
                    in_array(
                        $viewClassName,
                        CBViewCatalog::fetchDeprecatedViewClassNames()
                    )
                ) {
                    array_push($data->classNames, $viewClassName);
                }
            }
        );

        return $data->classNames;
    }


    /**
     * This function walks the subview tree of the model provided and returns a
     * non-unique array of deprecated subview class names.
     *
     * @param model $model
     *
     * @return [string]
     */
    static function findUnsupportedSubviewClassNames(stdClass $model): array {
        $data = (object)[
            'classNames' => [],
        ];

        CBView::walkSubviews(
            $model,
            function ($subview) use ($data) {
                $viewClassName = CBModel::valueToString($subview, 'className');

                if (
                    in_array(
                        $viewClassName,
                        CBViewCatalog::fetchUnsupportedViewClassNames()
                    )
                ) {
                    array_push($data->classNames, $viewClassName);
                }
            }
        );

        return $data->classNames;
    }
}
