<?php

final class
CB_Timestamp {

    /* -- CBHTMLOutput interfaces -- */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs(
    ): array {
        return [
            Colby::flexpath(
                __CLASS__,
                'v675.48.js',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_requiredClassNames(
    ): array {
        return [
            'CBConvert',
            'CBException',
            'CBModel',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */



    /* -- CBInstall interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function
    CBInstall_requiredClassNames(
    ): array {
        return [
            'CB_Table_Timestamps',
        ];
    }
    /* CBInstall_requiredClassNames() */



    /* -- CBModel interfaces -- */



    /**
     * @param $cbtimestampSpec
     *
     * @return stdClass
     */
    static function
    CBModel_build(
        $cbtimestampSpec
    ): stdClass {
        $cbtimestampModel = (object)[];

        CB_Timestamp::setUnixTimestamp(
            $cbtimestampModel,
            CB_Timestamp::getUnixTimestamp(
                $cbtimestampSpec
            )
        );

        CB_Timestamp::setFemtoseconds(
            $cbtimestampModel,
            CB_Timestamp::getFemtoseconds(
                $cbtimestampSpec
            )
        );

        return $cbtimestampModel;
    }
    /* CBModel_build() */



    /* -- accessors -- */



    /**
     * @param object $cbtimestampModel
     *
     * @return int
     */
    static function
    getUnixTimestamp(
        stdClass $cbtimestampModel
    ): int {
        return CBModel::valueAsInt(
            $cbtimestampModel,
            'CB_Timestamp_unixTimestamp_property'
        ) ?? 0;
    }
    /* getUnixTimestamp() */



    /**
     * @param object $cbtimestampModel
     * @param int $unixTimestamp
     *
     * return void
     */
    static function
    setUnixTimestamp(
        stdClass $cbtimestampModel,
        int $unixTimestamp
    ): void {
        $cbtimestampModel->CB_Timestamp_unixTimestamp_property = $unixTimestamp;
    }
    /* setUnixTimestamp() */



    /**
     * @param object $cbtimestampModel
     *
     * @return int
     */
    static function
    getFemtoseconds(
        stdClass $cbtimestampModel
    ): int {
        return CBModel::valueAsInt(
            $cbtimestampModel,
            'CB_Timestamp_femtoseconds_property'
        ) ?? 0;
    }
    /* getFemtoseconds() */



    /**
     * @param object $cbtimestampModel
     * @param int $femtoseconds
     *
     * return void
     */
    static function
    setFemtoseconds(
        stdClass $cbtimestampModel,
        int $femtoseconds
    ): void {
        if (
            $femtoseconds < 0 ||
            $femtoseconds > 999999999999999
                         /* ---|||---|||--- */
        ) {
            throw new CBExceptionWithValue(
                CBConvert::stringToCleanLine(<<<EOT

                    The femtoseconds argument must be between 0 and
                    999,999,999,999,999 inclusive.

                EOT),
                $femtoseconds,
                '71790d0ea17c7e79ef57bead3820c5cbd96f7036'
            );
        }

        $cbtimestampModel->CB_Timestamp_femtoseconds_property = $femtoseconds;
    }
    /* setFemtoseconds() */



    /* -- functions -- */



    /**
     * This function is used for administrative and testing purposes and does
     * not have clear uses outside of that. Unused cbtimestamps will
     * automatically be removed by the system.
     *
     * @param CBID $rootModelCBID
     *
     * @return void
     */
    static function
    deleteByRootModelCBID(
        string $rootModelCBID
    ): void {
        $rootModelCBIDAsSQL = CBID::toSQL(
            $rootModelCBID
        );

        $SQL = <<<EOT

            DELETE FROM
            CB_Timestamps_table

            WHERE
            CB_Timestamps_rootModelCBID_column =
            {$rootModelCBIDAsSQL}

        EOT;

        Colby::query(
            $SQL
        );
    }
    /* deleteByRootModelCBID() */



    /**
     * @param CBID $rootModelCBID
     *
     * @return [object]
     */
    static function
    fetchAllCBTimestampsByRootModelCBID(
        string $rootModelCBID
    ) {
        $rootModelCBIDAsSQL = CBID::toSQL(
            $rootModelCBID
        );

        $SQL = <<<EOT

            SELECT

            CB_Timestamps_unixTimestamp_column
            AS
            unixTimestamp,

            CB_Timestamps_femtoseconds_column
            AS
            femtoseconds

            FROM
            CB_Timestamps_table

            WHERE

            CB_Timestamps_rootModelCBID_column =
            {$rootModelCBIDAsSQL}

        EOT;

        $results = CBDB::SQLToObjects(
            $SQL
        );

        $cbtimestampModels = array_map(
            function (
                $result
            ) {
                return CB_Timestamp::from(
                    $result->unixTimestamp,
                    $result->femtoseconds
                );
            },
            $results
        );

        return $cbtimestampModels;
    }
    /* fetchAllCBTimestampsByRootModelCBID() */



    /**
     * @param CBID $rootModelCBID
     *
     * @return [object]
     */
    static function
    fetchRegisteredCBTimestampsByRootModelCBID(
        string $rootModelCBID
    ) {
        $rootModelCBIDAsSQL = CBID::toSQL(
            $rootModelCBID
        );

        $SQL = <<<EOT

            SELECT

            CB_Timestamps_unixTimestamp_column
            AS
            unixTimestamp,

            CB_Timestamps_femtoseconds_column
            AS
            femtoseconds

            FROM
            CB_Timestamps_table

            WHERE

            CB_Timestamps_rootModelCBID_column =
            {$rootModelCBIDAsSQL} AND

            CB_Timestamps_reservedAtUnixTimestamp_column
            IS NULL

        EOT;

        $results = CBDB::SQLToObjects(
            $SQL
        );

        $cbtimestampModels = array_map(
            function (
                $result
            ) {
                return CB_Timestamp::from(
                    $result->unixTimestamp,
                    $result->femtoseconds
                );
            },
            $results
        );

        return $cbtimestampModels;
    }
    /* fetchRegisteredCBTimestampsByRootModelCBID() */



    /**
     * @param CBID $rootModelCBID
     *
     * @return [object]
     */
    static function
    fetchReservedCBTimestampsByRootModelCBID(
        string $rootModelCBID
    ) {
        $rootModelCBIDAsSQL = CBID::toSQL(
            $rootModelCBID
        );

        $SQL = <<<EOT

            SELECT

            CB_Timestamps_unixTimestamp_column
            AS
            unixTimestamp,

            CB_Timestamps_femtoseconds_column
            AS
            femtoseconds

            FROM
            CB_Timestamps_table

            WHERE

            CB_Timestamps_rootModelCBID_column =
            {$rootModelCBIDAsSQL} AND

            CB_Timestamps_reservedAtUnixTimestamp_column
            IS NOT NULL

        EOT;

        $results = CBDB::SQLToObjects(
            $SQL
        );

        $cbtimestampModels = array_map(
            function (
                $result
            ) {
                return CB_Timestamp::from(
                    $result->unixTimestamp,
                    $result->femtoseconds
                );
            },
            $results
        );

        return $cbtimestampModels;
    }
    /* fetchReservedCBTimestampsByRootModelCBID() */



    /**
     * @param int $unixTimestamp
     * @param int $femtoseconds
     *
     * @return object
     */
    static function
    from(
        int $unixTimestamp,
        int $femtoseconds = 0
    ): stdClass {
        $cbtimestampModel = CBModel::createSpec(
            'CB_Timestamp'
        );

        CB_Timestamp::setUnixTimestamp(
            $cbtimestampModel,
            $unixTimestamp
        );

        CB_Timestamp::setFemtoseconds(
            $cbtimestampModel,
            $femtoseconds
        );

        return $cbtimestampModel;
    }
    /* from() */



    /**
     * @return [<unix timestamp>, <microseconds>]
     *
     *      Returns the current time as an array of two integers.  The second
     *      integer is the number of microseconds. There are one million
     *      microseconds in a second so the second number is in the range
     *      0 - 999,999.
     */
    static function
    getCurrentUnixTimestampAndMicroseconds(
    ): array
    {
        $microtime =
        microtime();

        preg_match(
            '/ ([0-9]+)/',
            $microtime,
            $matches
        );

        $unixTimestamp = $matches[1];

        preg_match(
            '/^0\.([0-9]{6})/',
            $microtime,
            $matches
        );

        $microseconds = intval(
            $matches[1]
        );

        $array =
        [
            $unixTimestamp,
            $microseconds,
        ];

        return $array;
    }
    // getCurrentUnixTimestampAndMicroseconds()



    /**
     * This function is the equivalent of:
     *
     *      $cbtimestampModel1 === $cbtimestampModel2
     *
     * @return bool
     */
    static function
    isEqualTo(
        stdClass $cbtimestampModel1,
        stdClass $cbtimestampModel2
    ): bool
    {
        $unixTimestamp1 =
        CB_Timestamp::getUnixTimestamp(
            $cbtimestampModel1
        );

        $unixTimestamp2 =
        CB_Timestamp::getUnixTimestamp(
            $cbtimestampModel2
        );

        if (
            $unixTimestamp1 !==
            $unixTimestamp2
        ) {
            return false;
        }

        $femtoseconds1 =
        CB_Timestamp::getFemtoseconds(
            $cbtimestampModel1
        );

        $femtoseconds2 =
        CB_Timestamp::getFemtoseconds(
            $cbtimestampModel2
        );

        if (
            $femtoseconds1 !==
            $femtoseconds2
        ) {
            return false;
        }

        return true;
    }
    // isEqualTo()



    /**
     * This function is the equivalent of:
     *
     *      $cbtimestampModel1 < $cbtimestampModel2
     *
     * @return bool
     */
    static function
    isLessThan(
        stdClass $cbtimestampModel1,
        stdClass $cbtimestampModel2
    ): bool
    {
        $unixTimestamp1 =
        CB_Timestamp::getUnixTimestamp(
            $cbtimestampModel1
        );

        $unixTimestamp2 =
        CB_Timestamp::getUnixTimestamp(
            $cbtimestampModel2
        );

        if (
            $unixTimestamp1 <
            $unixTimestamp2
        ) {
            return true;
        }

        if (
            $unixTimestamp1 >
            $unixTimestamp2
        ) {
            return false;
        }

        $femtoseconds1 =
        CB_Timestamp::getFemtoseconds(
            $cbtimestampModel1
        );

        $femtoseconds2 =
        CB_Timestamp::getFemtoseconds(
            $cbtimestampModel2
        );

        if (
            $femtoseconds1 <
            $femtoseconds2
        ) {
            return true;
        }

        return false;
    }
    // isLessThan()



    /**
     * @return CB_Timestamp
     */
    static function
    now(
    ): stdClass
    {
        static $previousCBTimestampModel =
        null;

        $currentUnixTimestampAndMicroseconds =
        CB_Timestamp::getCurrentUnixTimestampAndMicroseconds();

        $currentUnixTimestamp =
        $currentUnixTimestampAndMicroseconds[0];

        $currentMicroseconds =
        $currentUnixTimestampAndMicroseconds[1];

        $currentFemtoseconds =
        $currentMicroseconds
        * 1000
        * 1000
        * 1000;

        $currentCBTimestampModel =
        CB_Timestamp::from(
            $currentUnixTimestamp,
            $currentFemtoseconds,
        );

        if (
            $previousCBTimestampModel !==
            null &&
            (
                CB_Timestamp::isLessThan(
                    $currentCBTimestampModel,
                    $previousCBTimestampModel
                ) ||
                CB_Timestamp::isEqualTo(
                    $currentCBTimestampModel,
                    $previousCBTimestampModel
                )
            )
        ) {
            $currentCBTimestampModel =
            CB_Timestamp::nudgeUp(
                $previousCBTimestampModel
            );
        }

        $previousCBTimestampModel =
        $currentCBTimestampModel;

        return $currentCBTimestampModel;
    }
    // now()



    /**
     *
     */
    static function
    register(
        stdClass $cbtimestampModel,
        string $rootModelCBID
    ): bool {
        $rootModelCBIDAsSQL = CBID::toSQL(
            $rootModelCBID
        );

        $unixTimestamp = CB_Timestamp::getUnixTimestamp(
            $cbtimestampModel
        );

        $femtoseconds = CB_Timestamp::getFemtoseconds(
            $cbtimestampModel
        );

        $SQL = <<<EOT

            UPDATE
            CB_Timestamps_table

            SET
            CB_Timestamps_reservedAtUnixTimestamp_column =
            NULL

            WHERE

            CB_Timestamps_rootModelCBID_column =
            {$rootModelCBIDAsSQL} AND

            CB_Timestamps_unixTimestamp_column =
            {$unixTimestamp} AND

            CB_Timestamps_femtoseconds_column =
            {$femtoseconds}

        EOT;

        Colby::query(
            $SQL
        );

        if (
            Colby::mysqli()->affected_rows === 1
        ) {
            return true;
        } else {
            return false;
        }
    }
    /* register() */



    /**
     * This function will reserve a new unique cbtimestamp for a root model CBID
     * if the cbtimestamp is available.
     *
     * @return bool
     *
     *      Returns true if the cbtimestamp was available and reserved as
     *      requested; otherwise false.
     *
     *      Reserving a cbtimestamp is something you do one time, when you have
     *      discovered that you have a need for a new unique registered
     *      cbtimestamp. Therefore, if you try to reserve the same cbtimestamp
     *      for the same root model CBID twice this function will return false
     *      the second time.
     *
     *      This function will return false if the cbtimestamp is no longer
     *      reserved but is now registered.
     *
     *      This function will return false if the cbtimestamp is reserved or
     *      registered for a different root model CBID.
     */
    static function
    reserve(
        stdClass $cbtimestampModel,
        string $rootModelCBID
    ): bool {
        $rootModelCBIDAsSQL = CBID::toSQL(
            $rootModelCBID
        );

        $reservedAtUnixTimestamp = time();

        $unixTimestamp = CB_Timestamp::getUnixTimestamp(
            $cbtimestampModel
        );

        $femtoseconds = CB_Timestamp::getFemtoseconds(
            $cbtimestampModel
        );

        $SQL = <<<EOT

            INSERT INTO
            CB_Timestamps_table

            (
                CB_Timestamps_rootModelCBID_column,
                CB_Timestamps_reservedAtUnixTimestamp_column,
                CB_Timestamps_unixTimestamp_column,
                CB_Timestamps_femtoseconds_column
            )

            VALUES (
                {$rootModelCBIDAsSQL},
                {$reservedAtUnixTimestamp},
                {$unixTimestamp},
                {$femtoseconds}
            )

        EOT;

        try {
            Colby::query(
                $SQL
            );
        } catch (
            Throwable $throwable
        ) {
            /**
             * If MySQL had an error and the error number is 1062 that means
             * the insert failed because the time is already registered.
             */

            if (
                Colby::mysqli()->errno === 1062
            ) {
                return false;
            }

            throw $throwable;
        }

        return true;
    }
    /* reserve() */



    /**
     * @param object $cbtimestampModel
     * @param CBID $rootModelCBID
     *
     * @return object
     *
     *      Returns a reserved CB_Timestamp spec.
     */
    static function
    reserveNear(
        stdClass $cbtimestampModel,
        string $rootModelCBID
    ): stdClass {
        while (true) {
            $wasRegistered = CB_Timestamp::reserve(
                $cbtimestampModel,
                $rootModelCBID
            );

            if (
                $wasRegistered
            ) {
                return $cbtimestampModel;
            }

            $cbtimestampModel = CBModel::clone(
                $cbtimestampModel
            );

            $femtoseconds = CB_Timestamp::getFemtoseconds(
                $cbtimestampModel
            );

            $femtoseconds += random_int(
                100,
                5000
            );

            CB_Timestamp::setFemtoseconds(
                $cbtimestampModel,
                $femtoseconds
            );
        }
    }
    /* reserveNear() */



    /**
     * @param CBID $rootModelCBID
     *
     * @return object
     *
     *      Returns a reserved CB_Timestamp spec.
     */
    static function
    reserveNow(
        $rootModelCBID
    ): stdClass {
        $currentUnixTimestampAndMicroseconds =
        CB_Timestamp::getCurrentUnixTimestampAndMicroseconds();

        $unixTimestamp =
        $currentUnixTimestampAndMicroseconds[0];

        $microseconds =
        $currentUnixTimestampAndMicroseconds[1];

        $femtoseconds = (
            $microseconds
            * 1000
            * 1000
            * 1000
        );

        return CB_Timestamp::reserveNear(
            CB_Timestamp::from(
                $unixTimestamp,
                $femtoseconds
            ),
            $rootModelCBID
        );
    }
    /* reserveNow() */



    /**
     * @param object $cbtimestampModel
     * @param CBID $rootModelCBID
     *
     * @return void
     */
    static function
    rereserve(
        stdClass $cbtimestampModel,
        string $rootModelCBID
    ): void {
        $unixTimestamp = CB_Timestamp::getUnixTimestamp(
            $cbtimestampModel
        );

        $femtoseconds = CB_Timestamp::getFemtoseconds(
            $cbtimestampModel
        );

        $rootModelCBIDAsSQL = CBID::toSQL(
            $rootModelCBID
        );

        $reservedAtUnixTimestamp = time();

        $SQL = <<<EOT

            UPDATE
            CB_Timestamps_table

            SET
            CB_Timestamps_reservedAtUnixTimestamp_column =
            {$reservedAtUnixTimestamp}

            WHERE

            CB_Timestamps_rootModelCBID_column =
            {$rootModelCBIDAsSQL} AND

            CB_Timestamps_unixTimestamp_column =
            {$unixTimestamp} AND

            CB_Timestamps_femtoseconds_column =
            {$femtoseconds}

        EOT;

        Colby::query(
            $SQL
        );
    }
    /* rereserve() */



    /**
     * This function is intended to be called by the model save process and
     * doesn't have much expected use outside of that. It will unregister any
     * registered cbtimestamps for a root model CBID by moving them back to the
     * reserved state. Reserved cbtimestamps will stay around for a while but
     * will eventually be deleted.
     *
     * After calling this function, the model save process registers the
     * cbtimestamps that the model says it needs. This process moves unused
     * cbtimestamps back to the reserved state so that they will eventually be
     * deleted.
     *
     * @param CBID $rootModelCBID
     *
     * @return void
     */
    static function
    rereserveByRootModelCBID(
        string $rootModelCBID
    ): void {
        $rootModelCBIDAsSQL = CBID::toSQL(
            $rootModelCBID
        );

        $reservedAtUnixTimestamp = time();

        $SQL = <<<EOT

            UPDATE
            CB_Timestamps_table

            SET

            CB_Timestamps_reservedAtUnixTimestamp_column =
            {$reservedAtUnixTimestamp}

            WHERE

            CB_Timestamps_rootModelCBID_column =
            {$rootModelCBIDAsSQL}

        EOT;

        /**
         * @TODO 2022_01_09
         *
         *      Remove try/catch block in Colby version 676
         */

        try {
            Colby::query(
                $SQL
            );
        } catch (
            Throwable $throwable
        ) {
            if (
                Colby::mysqli()->errno === 1146
            ) {
                error_log(
                    'CB_Timestamps_table does not yet exist. Update website.'
                );
            } else {
                throw $throwable;
            }
        }
    }
    /* rereserveByRootModelCBID() */

}
