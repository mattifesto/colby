<?php

final class CBModelsImportAdmin {

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
            Colby::flexpath(__CLASS__, 'v462.js', cbsysurl()),
        ];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return [
            'CBMaintenance',
            'CBModelImporter',
            'CBUI',
            'CBUIBooleanSwitchPart',
            'CBUIProcessStatus',
            'CBUISectionItem4',
            'CBUIStringsPart',
        ];
    }

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $spec = CBModels::fetchSpecByID(CBModelsAdminMenu::ID());

        $spec->items[] = (object)[
            'className' => 'CBMenuItem',
            'name' => 'import',
            'text' => 'Import',
            'URL' => '/admin/?c=CBModelsImportAdmin',
        ];

        CBDB::transaction(function () use ($spec) {
            CBModels::save($spec);
        });
    }

    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return [
            'CBModelsAdminMenu',
        ];
    }

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

    /**
     * @param object $args
     *
     *      {
     *          saveUnchangedModels: bool?
     *      }
     *
     * @return object
     */
    static function CBAjax_uploadDataFile(stdClass $args): stdClass {
        CBProcess::setID(CBModelImporter::processID());

        /**
         * @NOTE 2019_08_16
         *
         *      The "save unchanged models" option can go away because we've
         *      moved to the "process version" paradigm where a process version
         *      number is set on the spec by CBModel_upgrade() and incremented
         *      when the process changes.
         *
         *      Imported specs are upgraded before they are saved. If the
         *      developer changes the process version, imported specs will save,
         *      if not, they don't need to be saved.
         */

        $saveUnchangedModels = (bool)CBModel::value($args, 'saveUnchangedModels');

        /**
         * Because any error that occurs will be associated with the process ID,
         * we use a try catch block to ensure that in all but the most extreme
         * of situations this Ajax request will succeed and return the process
         * ID which the caller can use to get further reporting information.
         */

        try {
            CBLog::log((object)[
                'className' => __CLASS__,
                'message' => 'A data file upload process has begun',
                'severity' => 6,
            ]);

            if (strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION)) !== 'csv') {
                throw new Exception('The data file should be a "csv" file.');
            }

            if ($handle = fopen($_FILES['file']['tmp_name'], 'r')) {
                $keys = fgetcsv($handle);

                if ($keys === false) {
                    throw new RuntimeException("The data file provided is empty");
                }

                $keys = array_map(function ($key) {
                    return trim($key);
                }, $keys);

                $rowSpecs = [];
                $countOfSpecsInDataFile = 0;
                $countOfSpecsSaved = 0;

                while (($values = fgetcsv($handle)) !== false) {
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

                    /**
                     * @NOTE 2019_08_16
                     *
                     *      All imported specs are upgraded before they are
                     *      saved for two reasons:
                     *
                     *          1) If there are necessary changes that haven't
                     *          been reflected in the comma separated values.
                     *
                     *          2) If there is a process version number that
                     *          needs to be set on imported specs.
                     */

                    $rowSpecs[$ID] = CBModel::upgrade($rowSpec);
                }

                fclose($handle);

                $originalSpecs = CBModels::fetchSpecsByID(array_keys($rowSpecs));
                $updatedSpecs = [];

                foreach ($rowSpecs as $ID => $rowSpec) {
                    if (empty($originalSpecs[$ID])) {
                        $updatedSpecs[$ID] = $rowSpec;
                    } else {
                        $originalSpec = $originalSpecs[$ID];
                        $updatedSpec = $rowSpec;
                        $updatedSpec->version = $originalSpec->version;

                        if ($saveUnchangedModels || $updatedSpec != $originalSpec) {
                            $updatedSpecs[$ID] = $updatedSpec;
                        }
                    }
                }

                $specsByClass = array_reduce($updatedSpecs, function(&$specsByClass, $spec) {
                    if (empty($specsByClass[$spec->className])) {
                        $specsByClass[$spec->className] = [];
                    }

                    $specsByClass[$spec->className][] = $spec;

                    return $specsByClass;
                }, []);

                foreach ($specsByClass as $className => $specs) {
                    CBDB::transaction(function () use ($specs) {
                        CBModels::save($specs);
                    });

                    $countOfSpecsSaved += count($specs);
                }

            }

            CBLog::log((object)[
                'className' => __CLASS__,
                'message' => "{$countOfSpecsInDataFile} of the data file rows contained model information.",
                'severity' => 6,
            ]);

            CBLog::log((object)[
                'className' => __CLASS__,
                'message' => "{$countOfSpecsSaved} of the data file rows provided changes to models that were saved.",
                'severity' => 6,
            ]);
        } catch (Throwable $throwable) {
            CBErrorHandler::report($throwable);
        }

        CBProcess::clearID();

        return (object)[];
    }

    /**
     * @return string
     */
    static function CBAjax_uploadDataFile_group(): string {
        return 'Administrators';
    }
}
