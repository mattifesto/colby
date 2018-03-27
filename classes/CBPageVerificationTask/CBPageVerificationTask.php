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

        /**
         * If the page no longer exists that task should be removed.
         */
        if (!CBPageVerificationTask::fetchPageDoesExist($ID)) {
            $messages[] = '(5) This page no longer exists';
            $severity = min(5, $severity);
            goto done;
        }

        $IDAsSQL = CBHex160::toSQL($ID);
        $className = CBDB::SQLToValue("SELECT `className` FROM `ColbyPages` WHERE `archiveID` = {$IDAsSQL}");

        if (empty($className)) {
            $messages[] = 'This page has no value in the ColbyPages table\'s className column.';
            $severity = min(3, $severity);
            goto done;
        }

        $data = CBModels::fetchSpecAndModelByID($ID);

        /**
         * Update an old file based spec to a CBModels spec
         */

        if ($data === false && $className === 'CBViewPage') {
            $oldspec = CBViewPage::fetchSpecByID($ID);

            if ($oldspec !== false) {
                // sometimes this isn't set on old specs
                $oldspec->className = 'CBViewPage';

                CBModels::save([$oldspec]);
                CBLog::addMessage(__CLASS__, 6, "The CBViewPage {$ID} was re-saved because it did not yet have a model in the CBModels table.");

                $data = CBModels::fetchSpecAndModelByID($ID);
            }
        }

        if ($data === false) {
            $messages[] = '(4) This page has no spec in the CBModels table';
            $severity = min(4, $severity);
            goto done;
        }

        // first line of message

        $pageTitle = CBModel::value($data, 'model.title', '', 'strval');
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

        if (!empty($data->model->image) && !empty($data->model->thumbnailURL)) {
            CBLog::addMessage(__CLASS__, 6, "The model for CBViewPage {$ID} had both the `image` and `thumbnailURL` properties set, which means it was incorrectly saved. To fix this the spec was re-saved.");
            $resave = true;
        }

        if (empty($data->spec->image)) {

            /**
             * We only process `thumbnailURL` on the spec if `image` is not set.
             * If `image` is set on the spec `thumbnailURL` will be ignored by
             * CBViewPage::CBModel_toModel().
             */

            if ($thumbnailURL = CBModel::value($data, 'spec.thumbnailURL')) {
                if ($thumbnailDataStoreID = CBDataStore::URIToID($thumbnailURL)) {
                    if (CBImages::isInstance($thumbnailDataStoreID)) {
                        $data->spec->image = CBImages::makeModelForID($thumbnailDataStoreID);
                    } else {
                        $data->spec->image = CBImages::importOldStyleImageDataStore($thumbnailDataStoreID);
                    }

                    $data->spec->deprecatedThumbnailURL = $data->spec->thumbnailURL;
                    unset($data->spec->thumbnailURL);

                    $resave = true;

                    CBLog::addMessage(__CLASS__, 6, "The spec for CBViewPage {$ID} upgraded its `thumbnailURL`: {$thumbnailURL} to CBImage {$data->spec->image->ID}");
                }
            }

        } else {

            /**
             * Emit a warning if the `image` on the spec is not a valid CBImage.
             */

            if (!CBImages::isInstance($data->spec->image->ID)) {
                $messages[] = '(3) The `image` property is set on the spec to an image that is not a valid CBImage instance.';
                $severity = min(3, $severity);
            }
        }

        /* check for deprecated views */

        CBPageVerificationTask::$messageContext = [];
        $views = CBView::toSubviews($data->spec);
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
            CBPage::render($data->model);
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
            CBModels::save([$data->spec]);
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
     * @param hex160 $ID
     *
     * @return bool
     */
    static function fetchPageDoesExist($ID) {
        $IDAsSQL = CBHex160::toSQL($ID);
        return boolval(CBDB::SQLToValue("SELECT COUNT(*) FROM `ColbyPages` WHERE `archiveID` = {$IDAsSQL}"));
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
