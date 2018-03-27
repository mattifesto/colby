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
        return ['CBImages', 'CBLog', 'CBModels', 'CBPages', 'CBTasks2'];
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
        $severity = 6;
        $result = CBPageVerificationTask::run($ID);

        if (!$result->hasColbyPagesRow) {
            $messages[] = 'No ColbyPages row exists for this ID';
            $severity = min(4, $severity);
        }

        if (!$result->hasModel) {
            $messages[] = 'No model exists for this ID';
            $severity = min(4, $severity);
            goto done;
        }

        // first line of message

        $pageTitle = CBModel::value($result, 'model.title', '', 'strval');
        $firstLine = __CLASS__ . " verified the page \"{$pageTitle}\"";
        array_unshift($messages, $firstLine);

        /**
         * Page image issues addressed:
         *
         *      - Model: If both the `image` and the `thumbnailURL` properties
         *        are set on the model, re-save the spec. Only one of these two
         *        properties should be set on a model.
         *
         *        History: At one point CBModel_toModel() tried to convert a
         *        `thumbnailURL` to an `image` and would set both. This process
         *        created an invalid `image` that would confuse renderers.
         *
         *      - Spec: A warning will be emitted if the `image` property on the
         *        spec is not a valid CBImage. There are no known causes of this
         *        but many potential causes.
         *
         *      - Spec: If only the `thumbnailURL` property is set on the spec
         *        and the property either refers to a CBImage or can be imported
         *        to a CBImage, we will import the CBImage, unset `thumbnailURL`
         *        and set `image`.
         */

        if (!empty($result->model->image) && !empty($result->model->thumbnailURL)) {
            CBLog::addMessage(__CLASS__, 6, "The model for CBViewPage {$ID} had both the `image` and `thumbnailURL` properties set, which means it was incorrectly saved. To fix this the spec was re-saved.");
            $resave = true;
        }

        if (empty($result->spec->image)) {

            /**
             * We only process `thumbnailURL` on the spec if `image` is not set.
             * If `image` is set on the spec `thumbnailURL` will be ignored by
             * CBViewPage::CBModel_toModel().
             */

            if ($thumbnailURL = CBModel::value($result, 'spec.thumbnailURL')) {
                if ($thumbnailDataStoreID = CBDataStore::URIToID($thumbnailURL)) {
                    if (CBImages::isInstance($thumbnailDataStoreID)) {
                        $result->spec->image = CBImages::makeModelForID($thumbnailDataStoreID);
                    } else {
                        $result->spec->image = CBImages::importOldStyleImageDataStore($thumbnailDataStoreID);
                    }

                    $result->spec->deprecatedThumbnailURL = $result->spec->thumbnailURL;
                    unset($result->spec->thumbnailURL);

                    $resave = true;

                    CBLog::addMessage(__CLASS__, 6, "The spec for CBViewPage {$ID} upgraded its `thumbnailURL`: {$thumbnailURL} to CBImage {$result->spec->image->ID}");
                }
            }

        } else {

            /**
             * Emit a warning if the `image` on the spec is not a valid CBImage.
             */

            if (!CBImages::isInstance($result->spec->image->ID)) {
                $messages[] = '(3) The `image` property is set on the spec to an image that is not a valid CBImage instance.';
                $severity = min(3, $severity);
            }
        }

        /* check for deprecated views */

        CBPageVerificationTask::$messageContext = [];
        $views = CBView::toSubviews($result->spec);
        array_walk($views, 'CBPageVerificationTask::verifyView');

        if (!empty(CBPageVerificationTask::$messageContext)) {
            $issues = implode("\n\n", CBPageVerificationTask::$messageContext);
            $messages[] = <<<EOT

                View issues:

                --- ul
                {$issues}
                ---

EOT;
        }

        /* test rendering */

        ob_start();

        try {
            CBPage::render($result->model);
        } catch (Throwable $throwable) {
            ob_end_clean();

            throw $throwable;
        }

        ob_end_clean();

        /* check for many old versions */

        $versionCount = CBPageVerificationTask::fetchCountOfOldVersions($ID);

        if ($versionCount > 5) {
            $resave = true;

            CBLog::addMessage(__CLASS__, 6, "The CBViewPage {$ID} was re-saved to remove its {$versionCount} old versions.");
        }

        if ($resave) {
            CBModels::save($result->spec);
        }

        if (empty($messages)) {
            $messages[] = "This page has no current issues";
        }

        done:

        CBLog::log((object)[
            'className' => __CLASS__,
            'ID' => $ID,
            'message' => implode("\n\n", $messages),
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
     *          hasColbyPagesRow: bool
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

        $result->hasModel = !empty($data);

        if (empty($data)) {
            if ($result->hasColbyPagesRow) {
                /**
                 * If there is a row in the pages table that does not have a model
                 * then it most likely represents a very old page. The row and the
                 * data store associated with the ID should be deleted.
                 */

                CBDataStore::deleteByID($ID);
                CBPages::deletePagesByID([$ID]);
            }
        } else {
            $result->spec = $data->spec;
            $result->model = $data->model;
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
