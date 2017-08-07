<?php

final class CBImage {

    /**
     * @param string $spec->extension
     * @param hex160 $spec->ID
     * @param string? $spec->filename
     * @param int? $spec->height
     * @param int? $spec->width
     *
     * @return stdClass
     */
    static function specToModel(stdClass $spec) {
        $filename = CBModel::value($spec, 'filename', '');

        if (empty($filename)) {
            // For backward compatability with older spec schema.
            $filename = CBModel::value($spec, 'base', '');
        }

        return (object)[
            'className' => __CLASS__,
            'extension' => $spec->extension,
            'filename' => $filename,
            'height' => CBModel::value($spec, 'height', null, 'intval'),
            'ID' => $spec->ID,
            'width' => CBModel::value($spec, 'width', null, 'intval'),
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
