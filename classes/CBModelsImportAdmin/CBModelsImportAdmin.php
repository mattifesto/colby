<?php

final class CBModelsImportAdmin {

    /**
     * @return [string]
     */
    static function CBAdmin_menuNamePath(): array {
        return ['models', 'import'];
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
        return [Colby::flexpath(__CLASS__, 'css', cbsysurl())];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [Colby::flexpath(__CLASS__, 'v400.js', cbsysurl())];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return [
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
        return ['CBModelsAdminMenu'];
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
        $response = (object)[
            'processID' => CBHex160::random(),
        ];

        CBProcess::setID($response->processID);

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
                    $rowSpec = CBModelsImportAdmin::valuesAsObject($values, $keys);

                    if ($rowSpec === null) {
                        continue;
                    }

                    $countOfSpecsInDataFile += 1;

                    $className = CBModel::valueToString($rowSpec, 'className');

                    if (empty($className)) {
                        $rowSpecJSONAsMarkup = CBMessageMarkup::stringToMarkup(
                            CBConvert::valueToPrettyJSON($rowSpec)
                        );

                        $message = <<<EOT

                            An imported spec does not have a class name:

                            --- pre\n{$rowSpecJSONAsMarkup}
                            ---

EOT;

                        CBLog::log((object)[
                            'className' => __CLASS__,
                            'message' => $message,
                            'severity' => 3,
                        ]);

                        continue;
                    }

                    if (empty($rowSpec->ID)) {
                        $ID = CBModel::toID($rowSpec);

                        if ($ID === null) {
                            CBModelsImportAdmin::reportNoID($rowSpec);
                            continue;
                        } else {
                            $rowSpec->ID = $ID;
                        }
                    }

                    $rowSpecs[$rowSpec->ID] = $rowSpec;
                }

                fclose($handle);

                $originalSpecs = CBModels::fetchSpecsByID(array_keys($rowSpecs));
                $updatedSpecs = [];

                foreach ($rowSpecs as $ID => $rowSpec) {
                    if (empty($originalSpecs[$ID])) {
                        $updatedSpecs[$ID] = $rowSpec;
                    } else {
                        $originalSpec = $originalSpecs[$ID];
                        $updatedSpec = CBModel::clone($originalSpec);

                        CBModel::merge($updatedSpec, $rowSpec);

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

        return $response;
    }

    /**
     * @return string
     */
    static function CBAjax_uploadDataFile_group(): string {
        return 'Administrators';
    }

    /**
     * @param object $spec
     *
     * @return void
     */
    static function reportNoID(stdClass $spec): void {
        $className = CBModel::valueToString($spec, 'className');
        $specAsJSONAsMarkup = CBMessageMarkup::stringToMessage(
            CBConvert::valueToPrettyJSON($spec)
        );

        $message = <<<EOT

            An imported {$className} spec was unable to generate its own ID.

EOT;

        if (!class_exists($className)) {
            $message .= <<<EOT

                The {$className} class does not exist.

EOT;
        } else if (!is_callable("{$className}::CBModel_toID")) {
            $message .= <<<EOT

                The (CBModel_toID\(\)(code)) interface is not implemented by the
                {$className} class.

EOT;
        }

        $message .= <<<EOT

            --- dl
                --- dt
                Imported spec
                ---

                --- dd
                    --- pre\n{$specAsJSONAsMarkup}
                    ---
                ---
            ---

EOT;

        CBLog::log((object)[
            'message' => $message,
            'severity' => 3,
            'sourceClassName' => __CLASS__,
            'sourceID' => '1506dfad3b967c8fc527f581e0a145d6475e5852',
        ]);
    }
}
