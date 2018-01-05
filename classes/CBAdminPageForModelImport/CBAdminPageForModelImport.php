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
        CBHTMLOutput::setTitleHTML('Model Import');
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
        return [Colby::flexpath(__CLASS__, 'v365.js', cbsysurl())];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return ['CBUI', 'CBUIActionLink', 'CBUIProcessStatus'];
    }

    /**
     * @return null
     */
    static function importJSONForAjax() {
        $response = new CBAjaxResponse();

        switch ($type = $_FILES['file']['type']) {
        case 'application/json':
        case 'text/plain':
            $spec = json_decode(file_get_contents($_FILES['file']['tmp_name']));
            break;

        default:
            $response->message = "This type of the uploaded file is \"{$type}\" which is not an accepted JSON file type.";
            goto done;
        }

        /**
         * TODO: Separate specs into className groups to allow multiple class
         *       names per file.
         */

        CBDB::transaction(function () use ($spec) {
            CBModels::save([$spec], /* force: */ true);
        });

        $response->message = 'Model imported successfully.';
        $response->wasSuccessful = true;

        done:

        $response->send();
    }

    /**
     * @return object
     */
    static function importJSONForAjaxPermissions() {
        return (object)['group' => 'Administrators'];
    }

    /**
     * @param [string] $rowData
     * @param [string] $columnData
     *
     * @return stdClass|null
     */
    static function objectFromCSVRowData($rowData, $columnData) {
        $fieldIndex = 0;
        $object = null;

        while (isset($rowData[$fieldIndex])) {
            $value = trim($rowData[$fieldIndex]);

            if ($value !== '' && isset($columnData[$fieldIndex])) {
                $key = trim($columnData[$fieldIndex]);

                if ($key !== '') {
                    if ($object === null) { $object = new stdClass(); }

                    $object->{$key} = $value;
                }
            }

            $fieldIndex += 1;
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
                $columns = fgetcsv($handle);

                if ($columns === false) {
                    throw new RuntimeException("The data file provided is empty");
                }

                $specs = [];
                $countOfSpecsInDataFile = 0;
                $countOfSpecsSaved = 0;

                while (($data = fgetcsv($handle)) !== false) {
                    $spec = CBAdminPageForModelImport::objectFromCSVRowData($data, $columns);

                    if ($spec !== null) {
                        $countOfSpecsInDataFile += 1;

                        if (empty($spec->className)) {
                            $specAsMessage = CBMessageMarkup::stringToMarkup(CBConvert::valueToPrettyJSON($spec));
                            $message = <<<EOT

                                An imported spec with other fields specified did not specify a className

                                --- pre\n{$specAsMessage}
                                ---

EOT;

                            CBLog::log((object)[
                                'className' => __CLASS__,
                                'message' => $message,
                                'severity' => 3,
                            ]);

                            continue;
                        }

                        if (empty($spec->ID)) {
                            $ID = CBModel::toID($spec);

                            if ($ID === null) {
                                CBLog::log((object)[
                                    'className' => __CLASS__,
                                    'message' => "An imported spec was unable to generate its own ID\n\n" .
                                                 "--- pre\n" .
                                                 CBConvert::valueToPrettyJSON($spec) .
                                                 "\n---",
                                    'severity' => 3,
                                ]);

                                continue;
                            } else {
                                $spec->ID = $ID;
                            }
                        }

                        $countOfSpecsSaved += 1;
                        $specs[] = $spec;
                    }
                }

                CBDB::transaction(function () use ($specs) {
                    CBModels::save($specs, /* force: */ true);
                });

                fclose($handle);
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
     * @return null
     */
    static function CBAjax_uploadDataFile_group() {
        return 'Administrators';
    }
}
