<?php

final class CBRemoteAdministration {

    /**
     * @return null
     */
    static function fetchPublicInformationForAjax() {
        $response = new CBAjaxResponse();

        $response->siteName = CBSitePreferences::siteName();
        $response->isLoggedIn = (ColbyUser::currentUserHash() !== null);
        $response->isAdministrator = ColbyUser::currentUserIsMemberOfGroup('Administrators');
        $response->isDeveloper = ColbyUser::currentUserIsMemberOfGroup('Developers');

        $response->wasSuccessful = true;
        $response->send();
    }

    /**
     * @return object
     */
    static function fetchPublicInformationForAjaxPermissions() {
        return (object)['group' => 'Public'];
    }

    /**
     * @return null
     */
    static function fetchStatisticsForAjax() {
        $response = new CBAjaxResponse();

        $SQL = <<<EOT

            SELECT  COUNT(*)
            FROM    `ColbyPages`

EOT;

        $response->pageCount = (int)CBDB::SQLToValue($SQL);
        $response->wasSuccessful = true;
        $response->send();
    }

    /**
     * @return object
     */
    static function fetchStatisticsForAjaxPermissions() {
        return (object)['group' => 'Administrators'];
    }

    /**
     * @return null
     */
    static function logoutForAjax() {
        $response = new CBAjaxResponse();

        ColbyUser::logoutCurrentUser();

        $response->wasSuccessful = true;
        $response->send();
    }

    /**
     * @return object
     */
    static function logoutForAjaxPermissions() {
        return (object)['group' => 'Public'];
    }

    /**
     * This function should by called by a cron-like process every five minutes
     * to keep scheduled tasks operating. Administrative processes should be
     * associated with scheduled tasks.
     *
     * @return object
     *
     *      bool    didExecute
     *                  true if ping was executed; otherwise false
     *
     *      int     previouslyPinged
     *                  0 if this is the first ping for this site
     *
     */
    static function ping() {
        $now = time();
        $spec = CBModels::fetchSpecByID(CBRemoteAdministration::pingModelID());
        $returnValue = (object)[
            'didExecute' => false,
        ];

        if ($spec === false) {
            $spec = (object)[
                'className' => __CLASS__,
                'ID' => CBRemoteAdministration::pingModelID(),
            ];
        }

        $returnValue->previouslyPinged = CBModel::value($spec, 'pinged', 0);

        if (($now - $returnValue->previouslyPinged) < (60 * 4 /* 4 minutes */)) {
            return $spec;
        }

        try {
            $spec->pinged = $now;

            Colby::query('START TRANSACTION');
            CBModels::save([$spec]);
            Colby::query('COMMIT');
        } catch (Exception $exception) {
            Colby::query('ROLLBACK');

            if ($exception instanceof CBModelVersionMismatchException) {
                // Another request beat us to the punch, no big deal.
                return;
            }

            throw $exception;
        }

        CBImageVerificationTask::startForNewImages();
        CBPageVerificationTask::startForNewPages();

        CBTasks2::wakeScheduledTasks();

        $returnValue->didExecute = true;

        return $returnValue;
    }

    /**
     * @return ajax
     */
    static function pingForAjax() {
        $response = new CBAjaxResponse();

        CBRemoteAdministration::ping();

        $response->wasSuccessful = true;
        $response->send();
    }

    static function pingForAjaxPermissions() {
        return (object)['group' => 'Public'];
    }

    /**
     * @return hex160
     */
    static function pingModelID() {
        return '9893b4a401686ac0e85707b6c19a01405481cc38';
    }

    /**
     * @param object $spec
     *
     * @return object
     */
    static function specToModel(stdClass $spec) {
        return (object)[
            'className' => __CLASS__,
            'pinged' => CBModel::value($spec, 'pinged', null, 'intval'),
        ];
    }
}
