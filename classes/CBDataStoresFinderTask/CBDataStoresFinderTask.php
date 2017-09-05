<?php

/**
 * This class looks in the data store directory for data stores. It updates the
 * table CBDataStores with the IDs of existing data stores.
 */
final class CBDataStoresFinderTask {

    /**
     * Calling this function start fast find mode where the task will start at
     * index zero an restart itself as fast as possible until it has found all
     * data stores. Then it will reset itself to normal mode.
     *
     * In normal mode all data stores are checked in about a day.
     *
     * @return null
     */
    static function CBAjax_startFastFind() {
        $spec = CBModels::fetchSpecByID(CBDataStoresFinderTask::ID());

        if ($spec === false) {
            $spec = (object)[
                'ID' => CBDataStoresFinderTask::ID(),
            ];
        }

        $spec->className = __CLASS__;
        $spec->fastFindIsActive = true;
        $spec->nextPartIndex = 0;

        CBDB::transaction(function () use ($spec) {
            CBModels::save([$spec]);
        });

        CBTasks2::updateTask(__CLASS__, CBDataStoresFinderTask::ID(), null, null, time());
    }

    /**
     * @return string
     */
    static function CBAjax_startFastFind_group() {
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
        $partIndexHex = dechex($partIndex);

        CBDataStoresFinderTask::findDataStoresForPartIndex($partIndex);

        if ($partIndex < 255) {
            $spec->nextPartIndex = $partIndex + 1;
        } else {
            $spec->nextPartIndex = 0;
            unset($spec->fastFindIsActive);
        }

        CBDB::transaction(function () use ($spec) {
            CBModels::save([$spec]);
        });

        return (object)[
            'message' => <<<EOT
CBDataStoresFinderTask, part index: {$partIndex} (0x{$partIndexHex})
EOT
            ,
            'scheduled' => !empty($spec->fastFindIsActive) ? time() : time() + (60 * 5), /* execute again in 5 minutes */
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

        if (!empty($IDs)) {
            $values = array_map(function ($ID) use ($timestamp) {
                $IDAsSQL = CBHex160::toSQL($ID);
                return "({$IDAsSQL}, {$timestamp})";
            }, $IDs);
            $values = implode(',', $values);

            $SQL = <<<EOT

                INSERT INTO `CBDataStores`
                    (`ID`, `timestamp`)
                VALUES
                    {$values}
                ON DUPLICATE KEY UPDATE
                    `timestamp` = {$timestamp}

EOT;

            Colby::query($SQL);
        }

        /**
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
