<?php

final class CBImage {

    /**
     * This model is validated more than most models because all of the
     * properties are required for the model to be valid.
     *
     * @param string $spec->extension
     * @param hex160 $spec->ID
     * @param string $spec->filename
     * @param int $spec->height
     * @param int $spec->width
     *
     * @return object|null
     */
    static function CBModel_toModel(stdClass $spec) {
        $extension = CBModel::value($spec, 'extension');

        if (empty($extension)) {
            CBLog::addMessage(__METHOD__, 4, "The spec `extension` property is empty.");
            return null;
        }

        $filename = CBModel::value($spec, 'filename');

        if (empty($filename)) {
            // For backward compatability with older spec schema.
            $filename = CBModel::value($spec, 'base');
        }

        if (empty($filename)) {
            CBLog::addMessage(__METHOD__, 4, "The spec `filename` property is empty.");
            return null;
        }

        $height = CBModel::value($spec, 'height', 0, 'intval');

        if ($height < 1) {
            CBLog::addMessage(__METHOD__, 4, "The spec `height` property is invalid.");
            return null;
        }

        $ID = CBModel::valueAsID($spec, 'ID');

        if (empty($ID)) {
            CBLog::addMessage(__METHOD__, 4, "The spec `ID` property is invalid.");
            return null;
        }

        $width = CBModel::value($spec, 'width', 0, 'intval');

        if ($width < 1) {
            CBLog::addMessage(__METHOD__, 4, "The spec `width` property is invalid.");
            return null;
        }

        return (object)[
            'className' => __CLASS__,
            'extension' => $extension,
            'filename' => $filename,
            'height' => $height,
            'ID' => $ID,
            'width' => $width,
        ];
    }

    /**
     * This function is similar to the CBModel::value... functions.
     *
     * @param object $model
     * @param string $keyPath
     * @param string? $operation
     * @param string? $flexdir
     *
     * @return string|false
     */
    static function valueToFlexpath(stdClass $model, $keyPath, $operation = null, $flexdir = null) {
        $ID = CBModel::value($model, "{$keyPath}.ID");

        if (!CBHex160::is($ID)) {
            return false;
        }

        $extension = CBModel::value($model, "{$keyPath}.extension");

        if (empty($extension)) {
            return false;
        }

        if ($operation) {
            $filename = $operation;
        } else {
            $filename = CBModel::value($model, "{$keyPath}.filename");

            if (empty($filename)) {
                $filename = CBModel::value($model, "{$keyPath}.base"); // deprecated

                if (empty($filename)) {
                    return false;
                }
            }
        }

        return CBDataStore::flexpath($ID, "{$filename}.{$extension}", $flexdir);
    }
}
