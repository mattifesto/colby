<?php

final class CBModelsImportAdmin {

    /* -- CBAdmin interfaces -- -- -- -- -- */



    /**
     * @return string
     */
    static function CBAdmin_getUserGroupClassName(): string {
        return 'CBAdministratorsUserGroup';
    }



    /**
     * @return [string]
     */
    static function CBAdmin_menuNamePath(): array {
        return [
            'models',
            'import',
        ];
    }



    /**
     * @return void
     */
    static function CBAdmin_render(): void {
        CBHTMLOutput::pageInformation()->title = 'Model Administration: Import';
    }



    /* -- CBAjax interfaces -- -- -- -- -- */



    /**
     * @param object $args
     *
     * @return object
     */
    static function CBAjax_uploadDataFile(stdClass $args): stdClass {
        CBProcess::setID(
            CBModelImporter::processID()
        );


        /**
         * Because any error that occurs will be associated with the process ID,
         * we use a try catch block to ensure that in all but the most extreme
         * of situations this Ajax request will succeed and return the process
         * ID which the caller can use to get further reporting information.
         */

        try {
            CBLog::log(
                (object)[
                    'message' => 'A data file upload process has begun',
                    'severity' => 6,
                    'sourceClassName' => __CLASS__,
                    'sourceID' => 'a16f002a207ba41c5e24b11f250265156331723e',
                ]
            );

            if (
                strtolower(
                    pathinfo(
                        $_FILES['file']['name'],
                        PATHINFO_EXTENSION
                    )
                ) !== 'csv'
            ) {
                throw new CBException(
                    'The data file should be a "csv" file.',
                    '',
                    '653e99c77513130d2f32f2a0b20c0304c9522242'
                );
            }


            /* if file can be opened */
            if (
                $handle = fopen($_FILES['file']['tmp_name'], 'r')
            ) {
                $keys = fgetcsv($handle);

                if ($keys === false) {
                    throw new CBException(
                        'The data file provided is empty',
                        '',
                        '0dcb1db0bf92cbf32f78f72812b665ba94fadbec'
                    );
                }

                $keys = array_map(function ($key) {
                    return trim($key);
                }, $keys);

                $rowSpecs = [];
                $countOfSpecsInDataFile = 0;
                $countOfSpecsSaved = 0;


                /* while file lines */
                while (
                    ($values = fgetcsv($handle)) !== false
                ) {
                    /**
                     * @NOTE 2019_08_16
                     *
                     *      This function name should change to valuesAsModel()
                     *      and throw an exception for any object that has a
                     *      non-empty field but is not a model.
                     */

                    $rowSpec = CBModelsImportAdmin::valuesAsObject(
                        $values,
                        $keys
                    );

                    if ($rowSpec === null) {
                        continue;
                    }

                    $countOfSpecsInDataFile += 1;

                    $ID = CBModel::valueAsID($rowSpec, 'ID');

                    if ($ID === null) {
                        $ID = CBModel::toID($rowSpec);
                        $rowSpec->ID = $ID;
                    }

                    $rowSpecs[$ID] = $rowSpec;
                }
                /* while file lines */


                fclose($handle);

                $alteredSpecs = CBModelsImportAdmin::rowSpecsToAlteredSpecs(
                    $rowSpecs
                );

                $specsByClass = array_reduce(
                    $alteredSpecs,
                    function (&$specsByClass, $spec) {
                        if (empty($specsByClass[$spec->className])) {
                            $specsByClass[$spec->className] = [];
                        }

                        $specsByClass[$spec->className][] = $spec;

                        return $specsByClass;
                    },
                    []
                );

                foreach ($specsByClass as $className => $specs) {
                    CBDB::transaction(
                        function () use ($specs) {
                            CBModels::save($specs);
                        }
                    );

                    $countOfSpecsSaved += count($specs);
                }
            }
            /* if file can be opened */


            CBLog::log(
                (object)[
                    'message' => (
                        "{$countOfSpecsInDataFile} of the data file rows " .
                        "contained model information."
                    ),
                    'severity' => 6,
                    'sourceClassName' => __CLASS__,
                    'sourceID' => '3eab7002c9011291887fba0ce4a9c7f6cd3d13e9',
                ]
            );

            CBLog::log(
                (object)[
                    'message' => (
                        "{$countOfSpecsSaved} of the data file rows " .
                        "provided changes to models that were saved."
                    ),
                    'severity' => 6,
                    'sourceClassName' => __CLASS__,
                    'sourceID' => 'e18b60bf0d7ae7377fdbce2a313175f4b66b3a0b',
                ]
            );
        } catch (Throwable $throwable) {
            CBErrorHandler::report($throwable);
        }

        CBProcess::clearID();

        return (object)[];
    }
    /* CBAjax_uploadDataFile() */



    /**
     * @return string
     */
    static function CBAjax_uploadDataFile_getUserGroupClassName(): string {
        return 'CBAdministratorsUserGroup';
    }



    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [
            Colby::flexpath(__CLASS__, 'css', cbsysurl()),
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v540.js', cbsysurl()),
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return [
            'CBErrorHandler',
            'CBMaintenance',
            'CBModelImporter',
            'CBUI',
            'CBUIProcessStatus',
            'CBUISectionItem4',
            'CBUIStringsPart',
        ];
    }



    /* -- CBInstall interfaces -- -- -- -- -- */



    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $spec = CBModels::fetchSpecByID(
            CBModelsAdminMenu::ID()
        );

        $spec->items[] = (object)[
            'className' => 'CBMenuItem',
            'name' => 'import',
            'text' => 'Import',
            'URL' => '/admin/?c=CBModelsImportAdmin',
        ];

        CBDB::transaction(
            function () use ($spec) {
                CBModels::save($spec);
            }
        );
    }



    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return [
            'CBModelsAdminMenu',
        ];
    }



    /* -- functions -- -- -- -- -- */



    /**
     * @param [object] $rowSpecs
     *
     * @return [object]
     *
     *      Returns an array row specs that are different from the currently
     *      saved specs for each ID.
     */
    static function rowSpecsToAlteredSpecs(
        array $rowSpecs
    ): array {
        $originalSpecs = CBModels::fetchSpecsByID(
            array_keys($rowSpecs)
        );

        $alteredSpecs = [];

        foreach ($rowSpecs as $CBID => $rowSpec) {

            /**
             * @NOTE 2019_08_16
             *
             *      All imported specs are upgraded before they are saved for
             *      two reasons:
             *
             *          1) If there are necessary changes that haven't been
             *          reflected in the comma separated values.
             *
             *          2) If there is a process version number that needs to be
             *          set on imported specs.
             */
            $rowSpec = CBModel::upgrade($rowSpec);

            if (empty($originalSpecs[$CBID])) {
                $alteredSpecs[$CBID] = $rowSpec;
            } else {
                $originalSpec = $originalSpecs[$CBID];
                $rowSpec->version = $originalSpec->version;

                if ($rowSpec != $originalSpec) {
                    $alteredSpecs[$CBID] = $rowSpec;
                }
            }
        }
        /* foreach */

        return $alteredSpecs;
    }
    /* rowSpecsToChangedSpecs() */



    /**
     * @param [string] $values
     * @param [string] $keys
     *
     * @return ?object
     *
     *      An object will be returned if it has at least one property value
     *      that isn't entirely white space.
     */
    static function valuesAsObject(array $values, array $keys): ?stdClass {
        $index = 0;
        $object = (object)[];
        $isEmpty = true;

        while (isset($values[$index])) {
            $value = $values[$index];

            if (!empty($keys[$index])) {
                $key = $keys[$index];

                /**
                 * $isEmpty will be set to false once there is at least one
                 * non-empty value.
                 */

                if ($isEmpty && preg_match('/\S/', $value)) {
                    $isEmpty = false;
                }

                /**
                 * Values are always left as found except classname and ID
                 * values which can be processed and validated.
                 */

                if ($key === 'className') {
                    $value = trim($value);
                } else if ($key === 'ID') {
                    $value = CBConvert::valueAsHex160(trim($value));
                }

                $object->{$key} = $value;
            }

            $index += 1;
        }

        if ($isEmpty) {
            return null;
        } else {
            return $object;
        }
    }
    /* valuesAsObject() */

}
