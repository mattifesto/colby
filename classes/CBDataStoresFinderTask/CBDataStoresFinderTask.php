<?php

/**
 * This class looks in the data store directory for data stores. It updates the
 * table CBDataStores with the IDs of existing data stores.
 *
 * This task splits the work of finding data stores into 256 parts which will
 * execute one after the other. Once the task for the last part is complete, the
 * process will restart in 24 hours.
 */
final class CBDataStoresFinderTask {

    /**
     * Calling this function restarts the data store finder tasks.
     *
     * @return null
     */
    static function CBAjax_restart() {
        $spec = CBModels::fetchSpecByID(CBDataStoresFinderTask::ID());

        if ($spec === false) {
            $spec = (object)[
                'ID' => CBDataStoresFinderTask::ID(),
            ];
        }

        $spec->className = __CLASS__;
        $spec->nextPartIndex = 0;

        CBDB::transaction(function () use ($spec) {
            CBModels::save([$spec]);
        });

        CBTasks2::updateTask(__CLASS__, CBDataStoresFinderTask::ID(), null, null, time());
    }

    /**
     * @return string
     */
    static function CBAjax_restart_group() {
        return 'Developers';
    }

    /**
     * @param object $spec
     *
     * @return object
     */
    static function CBModel_toModel(stdClass $spec) {
        return (object)[
            'className' => __CLASS__,
        ];
    }

    /**
     * @param hex160 $ID
     *
     * @return object
     */
    static function CBTasks2_Execute($ID) {
        if ($ID !== CBDataStoresFinderTask::ID()) {
            throw new InvalidArgumentException('ID');
        }

        $spec = CBModels::fetchSpecByID($ID);

        if ($spec === false) {
            $spec = (object)[
                'ID' => CBDataStoresFinderTask::ID(),
            ];
        }

        $spec->className = __CLASS__;

        $partIndex = CBModel::value($spec, 'nextPartIndex', 0, 'intval');
        $partIndexHex = sprintf('%02x', $partIndex);

        CBDataStoresFinderTask::findDataStoresForPartIndex($partIndex);

        if ($partIndex < 255) {
            $spec->nextPartIndex = $partIndex + 1;
            $scheduled = time(); /* execute again ASAP */
        } else {
            $spec->nextPartIndex = 0;
            $scheduled = time() + (60 * 60 * 24); /* execute again one day later */
        }

        CBDB::transaction(function () use ($spec) {
            CBModels::save([$spec]);
        });

        $message = <<<EOT
Part index: {$partIndex} (0x{$partIndexHex})
EOT;

        return (object)[
            'hint' => "{$partIndex}/255",
            'message' => $message,
            'scheduled' => $scheduled,
        ];
    }

    /**
     * @param int $index
     *
     * @return null
     */
    static function findDataStoresForPartIndex($index) {
        $hexIndex = sprintf('%02x', $index);
        $IDs = [];
        $dataStoreDirectories = glob(cbsitedir() . "/data/{$hexIndex}/*/*");

        foreach ($dataStoreDirectories as $directory) {
            if (!preg_match('/([0-9a-f]{2})\/([0-9a-f]{2})\/([0-9a-f]{36})/', $directory, $matches)) {
                CBLog::addMessage(__CLASS__, 3,
                    "The data store directory `{$directory}` is incorrectly " .
                    "named. Investigate and remove the directory manually");
            } else {
                $IDs[] = $matches[1] . $matches[2] . $matches[3];
            }
        }

        $timestamp = time();

        CBDataStores::update($IDs, $timestamp);

        /**
         * @NOTE Delete the rows of data stores that no longer have a data store
         * directory. I'm not sure this should occur, at least there should be a
         * notification because if a data store directory has been removed and
         * the row in the CBDataStores table hasn't, an error has occurred.
         *
         * CONCAT has three parts:
         *
         *  '\\\\'
         *      This will evaluate to '\\' in the SQL which will then evaluate to
         *      a single backslash which will escape the character that follows it
         *      which will be necessary if that character happens to be '%'.
         *
         *  UNHEX('{$hexIndex}')
         *      Since `hexPartIndex` is two hex characters this will evaluate to
         *      a single "binary character set" character.
         *
         *  '%'
         *      This percent is the wildcard character to be used in the context
         *      of the 'LIKE' keyword.
         */

        $SQL = <<<EOT

            DELETE FROM `CBDataStores`
            WHERE       `ID` LIKE CONCAT('\\\\', UNHEX('{$hexIndex}'), '%') and
                        `timestamp` != {$timestamp}

EOT;

        Colby::query($SQL);
    }

    /**
     * @return hex160
     */
    static function ID() {
        return '3d952455c87a497d0f666851a2ba920340741917';
    }

    /**
     * @return null
     */
    static function install() {
        CBTasks2::updateTask(__CLASS__, CBDataStoresFinderTask::ID(), null, null, time());
    }
}
