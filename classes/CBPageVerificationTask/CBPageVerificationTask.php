<?php

/**
 * @NOTE 2018.03.22
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
            if (is_callable($function = 'CBPageHelpers::allowedViewClassNames')) {
                $allowedViewClassNames = call_user_func($function);
            } else {
                $allowedViewClassNames = CBPagesPreferences::classNamesForSupportedViews();
            }
        }

        return $allowedViewClassNames;
    }

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
     * @param hex160 $ID
     *
     *      The ID (`ColbyPages`.`archiveID`) of the page to verify.
     *
     * @return object
     */
    static function CBTasks2_run($ID) {
        $messages = [];
        $resave = false;
        $severity = 7;
        $result = CBPageVerificationTask::run($ID);

        if (empty($result->hasColbyPagesRow)) {
            CBLog::log((object)[
                'className' => __CLASS__,
                'message' => 'No ColbyPages row exists for this ID',
                'severity' => 4,
            ]);
        }

        if (empty($result->model)) {
            CBLog::log((object)[
                'className' => __CLASS__,
                'message' => 'No model exists for this ID',
                'severity' => 4,
            ]);
        }

        if (!empty($result->didDeleteRowWithoutModel)) {
            $severity = min(4, $severity);
            $messages[] = 'The page row and data store were deleted';
        }

        if (!empty($result->renderError)) {
            CBErrorHandler::report($result->renderError);
        }

        /* first line of message */

        $pageTitle = CBModel::valueToString($result, 'model.title');
        $firstLine = "A page with the title \"{$pageTitle}\" was verified";
        array_unshift($messages, $firstLine);

        if (CBModel::valueToString($result, 'model.className') == 'CBViewPage') {
            if (empty($result->spec->image)) {

                /**
                 * We only process `thumbnailURL` on the spec if `image` is not
                 * set. If `image` is set on the spec `thumbnailURL` will be
                 * ignored by CBViewPage::CBModel_toModel().
                 */

                if ($thumbnailURL = CBModel::value($result, 'spec.thumbnailURL')) {
                    if ($image = CBImages::URIToCBImage($thumbnailURL)) {
                        $result->spec->image = $image;
                        $result->spec->deprecatedThumbnailURL = $result->spec->thumbnailURL;

                        unset($result->spec->thumbnailURL);

                        $resave = true;
                        $severity = min(6, $severity);
                        $messages[] = <<<EOT

                            A CBViewPage spec upgraded its thumbnailURL

                            {$thumbnailURL}

                            to CBImage

                            {$result->spec->image->ID}

EOT;
                    }
                }

            } else {

                /**
                 * Emit a warning if the `image` on the spec is not a valid CBImage.
                 */

                if (!CBImages::isInstance($result->spec->image->ID)) {
                    $severity = min(3, $severity);
                    $messages[] = <<<EOT

                        The "image" property is set on the spec to an image that
                        is not a valid CBImage instance.

EOT;
                }
            }

            /* check for deprecated views */

            CBPageVerificationTask::$messageContext = [];
            $views = CBView::toSubviews($result->spec);
            array_walk($views, 'CBPageVerificationTask::verifyView');

            if (!empty(CBPageVerificationTask::$messageContext)) {
                $issues = implode("\n\n", CBPageVerificationTask::$messageContext);
                $severity = min(6, $severity);
                $messages[] = <<<EOT

                    View issues:

                    --- ul
                    {$issues}
                    ---

EOT;
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
                 * If there is a row in the pages table that does not have a model
                 * then it most likely represents a very old page. The row and the
                 * data store associated with the ID should be deleted.
                 */

                CBDataStore::deleteByID($ID);
                CBPages::deletePagesByID([$ID]);

                $result->didDeleteRowWithoutModel = true;
            }
        } else {
            $result->spec = $data->spec;
            $result->model = $data->model;

            ob_start();

            try {
                CBPage::render($result->model);
            } catch (Throwable $throwable) {
                $result->renderError = $throwable;
            }

            ob_end_clean();
        }

        return $result;
    }

    /**
     * Start or restart the page verification task for all existing pages.
     */
    static function startForAllPages() {
        $IDs = CBDB::SQLToArray('SELECT LOWER(HEX(archiveID)) FROM ColbyPages');

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
     * @param object $spec
     *
     * @return
     */
    static function verifyView($spec) {
        $className = CBModel::value($spec, 'className');

        if (!in_array($className, CBPageVerificationTask::allowedViewClassNames())) {
            CBPageVerificationTask::$messageContext[] = "The {$className} class is not supported.";
        }

        $subviews = CBView::toSubviews($spec);

        array_walk($subviews, 'CBPageVerificationTask::verifyView');
    }
}
