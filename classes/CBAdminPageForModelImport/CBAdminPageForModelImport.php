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
        return [Colby::flexpath(__CLASS__, 'v363.js', cbsysurl())];
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
    static function uploadDataFileForAjax() {
        $response = new CBAjaxResponse();
        $processID = CBHex160::random();

        CBProcess::setID($processID);

        if (strtolower(pathinfo($_FILES['dataFile']['name'], PATHINFO_EXTENSION)) !== 'csv') {
            throw new Exception('The data file should be a "csv" file.');
        }

        if ($handle = fopen($_FILES['dataFile']['tmp_name'], 'r')) {
            $columns = fgetcsv($handle);

            if ($columns === false) {
                throw new RuntimeException("The data file provided is empty");
            }

            $specs = [];

            while (($data = fgetcsv($handle)) !== false) {
                $spec = CBAdminPageForModelImport::objectFromCSVRowData($data, $columns);

                if ($spec !== null) {
                    if (empty($spec->className)) {
                        CBLog::log((object)[
                            'className' => __CLASS__,
                            'message' => 'A spec row with other data specified did not specify a className',
                            'severity' => 3,
                        ]);

                        continue;
                    }

                    if (empty($spec->ID)) {
                        $spec->ID = CBModel::toID($spec);

                        if ($spec->ID === null) {
                            CBLog::log((object)[
                                'className' => __CLASS__,
                                'message' => "A imported spec was unable to generate its own ID\n\n" .
                                             "--- pre\n" .
                                             CBConvert::valueToPrettyJSON($spec) .
                                             "\n---",
                                'severity' => 3,
                            ]);

                            continue;
                        }
                    }

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
            'message' => 'Data file upload completed',
            'severity' => 5,
        ]);

        $response->message = "Data file uploaded successfully";
        $response->wasSuccessful = true;
        $response->processID = $processID;

        CBProcess::clearID();

        $response->send();
    }

    /**
     * @return null
     */
    static function uploadDataFileForAjaxPermissions() {
        return (object)['group' => 'Administrators'];
    }
}
