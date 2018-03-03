<?php

final class CBAdminPageForModelImport {

    /**
     * @return [string]
     */
    static function adminPageMenuNamePath() {
        return ['models', 'import'];
    }

    /**
     * @return stdClass
     */
    static function adminPagePermissions() {
        return (object)['group' => 'Administrators'];
    }

    /**
     * @return null
     */
    static function adminPageRenderContent() {
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
        return [Colby::flexpath(__CLASS__, 'v368.js', cbsysurl())];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return ['CBUI', 'CBUIActionPart', 'CBUIProcessStatus',
                'CBUISectionItem4'];
    }

    /**
     * @param [string] $values
     * @param [string] $keys
     *
     * @return ?model
     */
    static function valuesAsModel(array $values, array $keys): ?stdClass {
        $index = 0;
        $object = null;

        while (isset($values[$index])) {
            $value = $values[$index];

            if (!empty($keys[$index])) {
                $key = $keys[$index];

                /**
                 * It's a rule to not trim values during this stage, leaving
                 * that decision to build, but we do process and validate the
                 * className and ID values.
                 */

                if ($key === 'className') {
                    $value = trim($value);

                    if ($value === '') {
                        return null;
                    }
                } else if ($key === 'ID') {
                    $value = CBConvert::valueAsHex160(trim($value));
                }

                if ($object === null) {
                    $object = new stdClass();
                }

                $object->{$key} = $value;
            }

            $index += 1;
        }

        return $object;
    }

    /**
     * @return null
     */
    static function CBAjax_uploadDataFile() {
        $response = (object)[
            'processID' => CBHex160::random(),
        ];

        CBProcess::setID($response->processID);

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

                $specs = [];
                $countOfSpecsInDataFile = 0;
                $countOfSpecsSaved = 0;

                while (($values = fgetcsv($handle)) !== false) {
                    $spec = CBAdminPageForModelImport::valuesAsModel($values, $keys);

                    if ($spec === null) {
                        continue;
                    }

                    $countOfSpecsInDataFile += 1;

                    if (empty($spec->ID)) {
                        $ID = CBModel::toID($spec);

                        if ($ID === null) {
                            $specAsMessage = CBMessageMarkup::stringToMarkup(CBConvert::valueToPrettyJSON($spec));
                            $message = "An imported {$spec->className} spec was unable to generate its own ID";

                            if (!is_callable("{$spec->className}::CBModel_toID")) {
                                $message .= " and the CBModel_toID interface is not implemented by the {$spec->className} class";
                            }

                            $message = <<<EOT

                                {$message}

                                --- pre\n{$specAsMessage}
                                ---

EOT;

                            CBLog::log((object)[
                                'className' => __CLASS__,
                                'message' => $message,
                                'severity' => 3,
                            ]);

                            continue;
                        } else {
                            $spec->ID = $ID;
                        }
                    }

                    $specs[] = $spec;
                }

                fclose($handle);

                $specsByClass = array_reduce($specs, function(&$specsByClass, $spec) {
                    if (empty($specsByClass[$spec->className])) {
                        $specsByClass[$spec->className] = [];
                    }

                    $specsByClass[$spec->className][] = $spec;

                    return $specsByClass;
                }, []);

                foreach ($specsByClass as $className => $specs) {
                    if (is_callable("$className::CBModel_toModel")) {
                        CBDB::transaction(function () use ($specs) {
                            CBModels::save($specs, /* force: */ true);
                        });

                        $countOfSpecsSaved += count($specs);
                    } else {
                        CBLog::log((object)[
                            'className' => __CLASS__,
                            'message' => "The CBModel_toModel interface must be implemented by the {$className} class so that {$className} specs can be saved",
                            'severity' => 3,
                        ]);
                    }
                }

            }

            CBLog::log((object)[
                'className' => __CLASS__,
                'message' => "{$countOfSpecsSaved} of {$countOfSpecsInDataFile} specs found in the data file were saved",
                'severity' => 5,
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
}
