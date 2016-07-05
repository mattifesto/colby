<?php

final class CBAdminPageForModelImport {

    /**
     * @return [string]
     */
    public static function adminPageMenuNamePath() {
        return ['models', 'import'];
    }

    /**
     * @return stdClass
     */
    public static function adminPagePermissions() {
        return (object)['group' => 'Administrators'];
    }

    /**
     * @param [string] $rowData
     * @param [string] $columnData
     *
     * @return stdClass|null
     */
    public static function objectFromCSVRowData($rowData, $columnData) {
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
     * @return [string]
     */
    public static function requiredClassNames() {
        return ['CBUI', 'CBUIActionLink', 'CBUITaskStatus'];
    }

    /**
     * @return [string]
     */
    public static function requiredJavaScriptURLs() {
        return [Colby::flexnameForJavaScriptForClass(CBSystemURL, __CLASS__)];
    }

    /**
     * @return null
     */
    public static function uploadDataFileForAjax() {
        $response = new CBAjaxResponse();

        if (strtolower(pathinfo($_FILES['dataFile']['name'], PATHINFO_EXTENSION)) !== 'csv') {
            throw new Exception('The data file should be a "csv" file.');
        }

        if ($handle = fopen($_FILES['dataFile']['tmp_name'], 'r')) {
            $columns = fgetcsv($handle);

            if ($columns === false) {
                throw new RuntimeException("The data file provided is empty");
            }

            $argsArray = [];

            while (($data = fgetcsv($handle)) !== false) {
                $spec = CBAdminPageForModelImport::objectFromCSVRowData($data, $columns);

                if ($spec !== null) {
                    if (empty($spec->className)) {
                        CBLog::addMessage('CBAdminPageForModelImport', 3, 'A spec row with other data specified did not specify a className.');
                        continue;
                    }

                    $argsArray[] = (object)[
                        'spec' => $spec,
                    ];
                }
            }

            CBTasks::add('CBModel', 'importSpec', $argsArray, -1);

            fclose($handle);
        }

        CBLog::addMessage('CBAdminPageForModelImport', 5, 'A data file was imported.');

        $response->message = "Data file uploaded successfully";
        $response->wasSuccessful = true;
        $response->send();
    }

    /**
     * @return null
     */
    public static function uploadDataFileForAjaxPermissions() {
        return (object)['group' => 'Administrators'];
    }
}
