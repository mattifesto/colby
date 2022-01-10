<?php

final class
CB_Attostamp {

    /* -- CBModel interfaces -- */



    /**
     * @param $attostampSpec
     *
     * @return stdClass
     */
    static function
    CBModel_build(
        $attostampSpec
    ): stdClass {
        $attostampModel = (object)[];

        CB_Attostamp::setUnixTimestamp(
            $attostampModel,
            CB_Attostamp::getUnixTimestamp(
                $attostampSpec
            )
        );

        CB_Attostamp::setAttoseconds(
            $attostampModel,
            CB_Attostamp::getAttoseconds(
                $attostampSpec
            )
        );

        return $attostampModel;
    }
    /* CBModel_build() */



    /* -- accessors -- */



    /**
     * @param object $registeredTimestamp
     *
     * @return int
     */
    static function
    getUnixTimestamp(
        stdClass $registeredTimestamp
    ): int {
        return CBModel::valueAsInt(
            $registeredTimestamp,
            'CB_Attostamp_unixTimestamp_property'
        ) ?? 0;
    }
    /* getUnixTimestamp() */



    /**
     * @param object $attostampModel
     * @param int $unixTimestamp
     *
     * return void
     */
    static function
    setUnixTimestamp(
        stdClass $attostampModel,
        int $unixTimestamp
    ): void {
        $attostampModel->CB_Attostamp_unixTimestamp_property = $unixTimestamp;
    }
    /* setUnixTimestamp() */



    /**
     * @param object $registeredTimestamp
     *
     * @return int
     */
    static function
    getAttoseconds(
        stdClass $registeredTimestamp
    ): int {
        return CBModel::valueAsInt(
            $registeredTimestamp,
            'CB_Attostamp_attoseconds_property'
        ) ?? 0;
    }
    /* getAttoseconds() */



    /**
     * @param object $attostampModel
     * @param int $attoseconds
     *
     * return void
     */
    static function
    setAttoseconds(
        stdClass $attostampModel,
        int $attoseconds
    ): void {
        if (
            $attoseconds < 0 ||
            $attoseconds > 999999999999999999
                        /* ---|||---|||---||| */
        ) {
            throw new InvalidArgumentException(
                'attoseconds'
            );
        }

        $attostampModel->CB_Attostamp_attoseconds_property = $attoseconds;
    }
    /* setAttoseconds() */



    /* -- functions -- */



    /**
     * This function is used for administrative and testing purposes and does
     * not have clear uses outside of that. Unused attostamps will automatically
     * be removed by the system.
     *
     * @param CBID $rootModelCBID
     *
     * @return void
     */
    static function
    deleteAttostampsByRootModelCBID(
        string $rootModelCBID
    ): void {
        $rootModelCBIDAsSQL = CBID::toSQL(
            $rootModelCBID
        );

        $SQL = <<<EOT

            DELETE FROM
            CB_Attostamps_table

            WHERE
            CB_Attostamps_rootModelCBID_column =
            {$rootModelCBIDAsSQL}

        EOT;

        Colby::query(
            $SQL
        );
    }
    /* deleteAttostampsByRootModelCBID() */



    /**
     * @param CBID $rootModelCBID
     *
     * @return [object]
     */
    static function
    fetchRegisteredAttostampsByRootModelCBID(
        string $rootModelCBID
    ) {
        $rootModelCBIDAsSQL = CBID::toSQL(
            $rootModelCBID
        );

        $SQL = <<<EOT

            SELECT

            CB_Attostamps_unixTimestamp_column
            AS
            unixTimestamp,

            CB_Attostamps_attoseconds_column
            AS
            attoseconds

            FROM
            CB_Attostamps_table

            WHERE

            CB_Attostamps_rootModelCBID_column =
            {$rootModelCBIDAsSQL} AND

            CB_Attostamps_reservedAtUnixTimestamp_column
            IS NULL

        EOT;

        $results = CBDB::SQLToObjects(
            $SQL
        );

        $attostampModels = array_map(
            function (
                $result
            ) {
                return CB_Attostamp::from(
                    $result->unixTimestamp,
                    $result->attoseconds
                );
            },
            $results
        );

        return $attostampModels;
    }
    /* fetchRegisteredAttostampsByRootModelCBID() */



    /**
     * @param CBID $rootModelCBID
     *
     * @return [object]
     */
    static function
    fetchReservedAttostampsByRootModelCBID(
        string $rootModelCBID
    ) {
        $rootModelCBIDAsSQL = CBID::toSQL(
            $rootModelCBID
        );

        $SQL = <<<EOT

            SELECT

            CB_Attostamps_unixTimestamp_column
            AS
            unixTimestamp,

            CB_Attostamps_attoseconds_column
            AS
            attoseconds

            FROM
            CB_Attostamps_table

            WHERE

            CB_Attostamps_rootModelCBID_column =
            {$rootModelCBIDAsSQL} AND

            CB_Attostamps_reservedAtUnixTimestamp_column
            IS NOT NULL

        EOT;

        $results = CBDB::SQLToObjects(
            $SQL
        );

        $attostampModels = array_map(
            function (
                $result
            ) {
                return CB_Attostamp::from(
                    $result->unixTimestamp,
                    $result->attoseconds
                );
            },
            $results
        );

        return $attostampModels;
    }
    /* fetchReservedAttostampsByRootModelCBID() */



    /**
     * @param int $unixTimestamp
     * @param int $attoseconds
     *
     * @return object
     */
    static function
    from(
        int $unixTimestamp,
        int $attoseconds = 0
    ): stdClass {
        $attostampModel = CBModel::createSpec(
            'CB_Attostamp'
        );

        CB_Attostamp::setUnixTimestamp(
            $attostampModel,
            $unixTimestamp
        );

        CB_Attostamp::setAttoseconds(
            $attostampModel,
            $attoseconds
        );

        return $attostampModel;
    }
    /* from() */



    /**
     *
     */
    static function
    register(
        stdClass $attostampModel,
        string $rootModelCBID
    ): bool {
        $rootModelCBIDAsSQL = CBID::toSQL(
            $rootModelCBID
        );

        $unixTimestamp = CB_Attostamp::getUnixTimestamp(
            $attostampModel
        );

        $attoseconds = CB_Attostamp::getAttoseconds(
            $attostampModel
        );

        $SQL = <<<EOT

            UPDATE
            CB_Attostamps_table

            SET
            CB_Attostamps_reservedAtUnixTimestamp_column =
            NULL

            WHERE

            CB_Attostamps_rootModelCBID_column =
            {$rootModelCBIDAsSQL} AND

            CB_Attostamps_unixTimestamp_column =
            {$unixTimestamp} AND

            CB_Attostamps_attoseconds_column =
            {$attoseconds}

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
     * This function will reserve a new unique attostamp for a root model CBID
     * if the attostamp is available.
     *
     * @return bool
     *
     *      Returns true if the attostamp was available and reserved as
     *      requested; otherwise false.
     *
     *      Reserving an attostamp is something you do one time, when you have
     *      discovered that you have a need for a new unique registered
     *      attostamp. Therefore, if you try to reserve the same attostamp for
     *      the same root model CBID twice this function will return false the
     *      second time.
     *
     *      This function will return false if the attostamp is no longer
     *      reserved but is now registered.
     *
     *      This function will return false if the attostamp is reserved or
     *      registered for a different root model CBID.
     */
    static function
    reserve(
        stdClass $attostampModel,
        string $rootModelCBID
    ): bool {
        $rootModelCBIDAsSQL = CBID::toSQL(
            $rootModelCBID
        );

        $reservedAtUnixTimestamp = time();

        $unixTimestamp = CB_Attostamp::getUnixTimestamp(
            $attostampModel
        );

        $attoseconds = CB_Attostamp::getAttoseconds(
            $attostampModel
        );

        $SQL = <<<EOT

            INSERT INTO
            CB_Attostamps_table

            (
                CB_Attostamps_rootModelCBID_column,
                CB_Attostamps_reservedAtUnixTimestamp_column,
                CB_Attostamps_unixTimestamp_column,
                CB_Attostamps_attoseconds_column
            )

            VALUES (
                {$rootModelCBIDAsSQL},
                {$reservedAtUnixTimestamp},
                {$unixTimestamp},
                {$attoseconds}
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
     * @param object $attostampModel
     * @param CBID $rootModelCBID
     *
     * @return object
     *
     *      Returns a reserved CB_Attostamp spec.
     */
    static function
    reserveNear(
        stdClass $attostampModel,
        string $rootModelCBID
    ): stdClass {
        while (true) {
            $wasRegistered = CB_Attostamp::reserve(
                $attostampModel,
                $rootModelCBID
            );

            if (
                $wasRegistered
            ) {
                return $attostampModel;
            }

            $attostampModel = CBModel::clone(
                $attostampModel
            );

            $attoseconds = CB_Attostamp::getAttoseconds(
                $attostampModel
            );

            $attoseconds += random_int(
                100,
                5000
            );

            CB_Attostamp::setAttoseconds(
                $attostampModel,
                $attoseconds
            );
        }
    }
    /* reserveNear() */



    /**
     * @param CBID $rootModelCBID
     *
     * @return object
     *
     *      Returns a reserved CB_Attostamp spec.
     */
    static function
    reserveNow(
        $rootModelCBID
    ): stdClass {
        $microtime = microtime();

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

        $attoseconds = (
            $microseconds
            * 1000
            * 1000
            * 1000
            * 1000
        );

        return CB_Attostamp::reserveNear(
            CB_Attostamp::from(
                $unixTimestamp,
                $attoseconds
            ),
            $rootModelCBID
        );
    }
    /* reserveNow() */



    /**
     * @param object $attostampModel
     * @param CBID $rootModelCBID
     *
     * @return void
     */
    static function
    rereserve(
        stdClass $attostampModel,
        string $rootModelCBID
    ): void {
        $unixTimestamp = CB_Attostamp::getUnixTimestamp(
            $attostampModel
        );

        $attoseconds = CB_Attostamp::getAttoseconds(
            $attostampModel
        );

        $rootModelCBIDAsSQL = CBID::toSQL(
            $rootModelCBID
        );

        $reservedAtUnixTimestamp = time();

        $SQL = <<<EOT

            UPDATE
            CB_Attostamps_table

            SET
            CB_Attostamps_reservedAtUnixTimestamp_column =
            {$reservedAtUnixTimestamp}

            WHERE

            CB_Attostamps_rootModelCBID_column =
            {$rootModelCBIDAsSQL} AND

            CB_Attostamps_unixTimestamp_column =
            {$unixTimestamp} AND

            CB_Attostamps_attoseconds_column =
            {$attoseconds}

        EOT;

        Colby::query(
            $SQL
        );
    }
    /* rereserve() */



    /**
     * This function is intended to be called by the model save process and
     * doesn't have much expected use outside of that. It will unregister any
     * registered attostamps for a root model CBID by moving them back to the
     * reserved state. Reserved attostamps will stay around for a while but will
     * eventually be deleted.
     *
     * After calling this function, the model save process registers the
     * attostamps that the model says it needs. This process moves unused
     * attostamps back to the reserved state so that they will eventually be
     * deleted.
     *
     * @param CBID $rootModelCBID
     *
     * @return void
     */
    static function
    rereserveAttostampsByRootModelCBID(
        string $rootModelCBID
    ): void {
        $rootModelCBIDAsSQL = CBID::toSQL(
            $rootModelCBID
        );

        $reservedAtUnixTimestamp = time();

        $SQL = <<<EOT

            UPDATE
            CB_Attostamps_table

            SET

            CB_Attostamps_reservedAtUnixTimestamp_column =
            {$reservedAtUnixTimestamp}

            WHERE

            CB_Attostamps_rootModelCBID_column =
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
                    'CB_Attostamps_table does not yet exist. Update website.'
                );
            } else {
                throw $throwable;
            }
        }
    }
    /* rereserveAttostampsByRootModelCBID() */

}
