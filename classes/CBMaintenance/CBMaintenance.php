<?php

final class CBMaintenance {

    /**
     * @param object $spec
     *
     * @return ?object
     */
    static function CBModel_build(stdClass $spec): ?stdClass {
        return (object)[
            'holderID' => CBModel::valueAsID($spec, 'holderID'),
            'timestamp' => CBModel::valueAsInt($spec, 'timestamp'),
            'userID' => CBModel::valueAsID($spec, 'userID'),
        ];
    }

    /**
     * Check isLocked() when decided whether to do optional database work. For
     * instance, task processing will not occur while isLocked() returns true.
     *
     * @return bool
     */
    static function isLocked(): bool {
        $model = CBModelCache::fetchModelByID(CBMaintenance::ID());
        $timestamp = CBModel::valueAsInt($model, 'timestamp');

        if ($timestamp !== null || time() - $timestamp < 30) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Call lock when you need to reserve the database to perform a major
     * operation. System updates and imports both call lock while the are
     * running.
     *
     * A lock will automatically unlock after 30 seconds so you will generally
     * want to run a timed lock from JavaScript ever 20 seconds or so. Lock is
     * generally initiated and maintained from JavaScript because major
     * operations are assumed to need longer than a single request.
     *
     * This lock is an advisory lock and absolutely not a guarantee that no
     * database activity will occur.
     *
     * @param object $request
     *
     *      {
     *          holderID: ID
     *          title: string
     *      }
     *
     * @return bool
     *
     *      Returns true if the lock was successful; otherwise false.
     */
    static function lock(stdClass $args): bool {
        $holderID = CBModel::valueAsID($args, 'holderID');

        if (empty($holderID)) {
            throw new InvalidArgumentException('No holderID argument was provided.');
        }

        $title = trim(CBModel::valueToString($args, 'title'));

        if (empty($title)) {
            throw new InvalidArgumentException('No title argument was provided.');
        }

        $updater = CBModelUpdater::fetch((object)[
            'className' => 'CBMaintenance',
            'ID' => CBMaintenance::ID(),
        ]);

        $spec = $updater->working;
        $previousHolderID = CBModel::valueAsID($spec, 'holderID');

        /**
         * If the previous holder ID is different than the holder ID argument we
         * may not be able to lock.
         */
        if ($previousHolderID !== null && $holderID !== $previousHolderID) {
            $previousTimestamp = CBModel::valueAsInt($spec, 'timestamp');

            /**
             * If the previous lock was taken less tham 30 seconds ago we are
             * not able to lock.
             */
            if (time() - $previousTimestamp < 30) {
                return false; /* request denied */
            }
        }

        CBModel::merge($spec, (object)[
            'holderID' => $holderID,
            'timestamp' => time(),
            'title' => $title,
            'userID' => ColbyUser::currentUserHash(),
        ]);

        CBModelUpdater::save($updater);

        return true; /* request granted */
    }

    /**
     * If the holder ID matches the previously set holder ID, the lock will be
     * released. If not, the lock has already been release from the specified
     * holder.
     *
     * @param ID $holderID
     *
     * @return void
     */
    static function unlock(string $holderID): void {
        $holderID = CBConvert::valueAsID($holderID);

        if (empty($holderID)) {
            throw new InvalidArgumentException('No holderID argument was provided.');
        }

        $updater = CBModelUpdater::fetch((object)[
            'className' => 'CBMaintenance',
            'ID' => CBMaintenance::ID(),
        ]);

        $spec = $updater->working;
        $previousHolderID = CBModel::valueAsID($spec, 'holderID');

        if ($holderID !== $previousHolderID) {
            return;
        }

        CBModel::merge($spec, (object)[
            'holderID' => null,
            'timestamp' => null,
            'title' => '',
            'userID' => null,
        ]);

        CBModelUpdater::save($updater);
    }

    /**
     * @return ID
     */
    static function ID(): string {
        return 'b3e69475bc6bbc2ec8b8ddccd65b2206329095e7';
    }
}
