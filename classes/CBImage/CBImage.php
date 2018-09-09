<?php

final class CBImage {

    /**
     * @param model $image
     * @param string $filename
     *
     *      If not specified, the filename from the $image model will be used.
     *
     *      Examples: 'original', 'rw320', 'rw1280'
     *
     * @param string $flexdir
     *
     * @return ?string
     *
     *      This function will return null if the required properties are not
     *      available.
     */
    static function asFlexpath(stdClass $image, string $filename = '', string $flexdir = ''): ?string {
        $ID = CBModel::valueAsID($image, 'ID');

        if (empty($ID)) {
            return null;
        }

        $extension = CBModel::valueToString($image, 'extension');

        if (empty($extension)) {
            return null;
        }

        if (empty($filename)) {
            $filename = CBModel::valueToString($image, 'filename');

            if (empty($filename)) {
                return null;
            }
        }

        return CBDataStore::flexpath($ID, "{$filename}.{$extension}", $flexdir);
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v453.js', cbsysurl()),
        ];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return [
            'CBModel',
        ];
    }

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
    static function CBModel_build(stdClass $spec) {
        $specIssues = [];
        $extension = CBModel::value($spec, 'extension');

        if (empty($extension)) {
            $specIssues[] = "The spec `extension` property is empty.";
        }

        $filename = CBModel::value($spec, 'filename');

        if (empty($filename)) {
            // For backward compatability with older spec schema.
            $filename = CBModel::value($spec, 'base');
        }

        if (empty($filename)) {
            $specIssues[] = "The spec `filename` property is empty.";
        }

        $height = CBModel::value($spec, 'height', 0, 'intval');

        if ($height < 1) {
            $specIssues[] = "The spec `height` property is invalid.";
        }

        $ID = CBModel::valueAsID($spec, 'ID');

        if (empty($ID)) {
            $specIssues[] = "The spec `ID` property is invalid.";
        }

        $width = CBModel::value($spec, 'width', 0, 'intval');

        if ($width < 1) {
            $specIssues[] = "The spec `width` property is invalid.";
        }

        if (!empty($specIssues)) {
            $method = __METHOD__ . '()';
            $specAsJSON = CBMessageMarkup::stringToMarkup(CBConvert::valueToPrettyJSON($spec));
            $specIssues = implode("\n\n", $specIssues);
            $message = <<<EOT

                {$method} returned null because of spec issues.

                (Issues: (strong))

                --- ul

                {$specIssues}

                ---

                (Spec: (strong))

                --- pre
{$specAsJSON}
                ---

EOT;

            CBLog::log((object)[
                'className' => __CLASS__,
                'message' => $message,
                'severity' => 4,
            ]);

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
     * @param model $spec
     *
     * @return model
     */
    static function CBModel_upgrade(stdClass $spec): stdClass {
        if (empty($spec->filename) && !empty($spec->base)) {
            $spec->filename = $spec->base;
            unset($spec->base);
        }

        return $spec;
    }

    /**
     * Deleting images is a process that should rarely happen. Images should be
     * kept forever even if they are no longer used. However there are various
     * development and administrative reasons to delete images.
     *
     * @param [hex160] $IDs
     *
     * @return null
     */
    static function CBModels_willDelete(array $IDs) {
        foreach ($IDs as $ID) {
            CBImages::deleteByID($ID);
        }
    }

    /**
     * @param [object] $models
     *
     * @return null
     */
    static function CBModels_willSave(array $models) {
        foreach ($models as $model) {
            CBImages::updateRow($model->ID, time(), $model->extension);
        }
    }

    /**
     * Use the function instead of exif_read_data() because exif_read_data()
     * throws errors for many image files.
     *
     * @param string $filepath
     *
     * @return [string => mixed]|false
     */
    static function exif_read_data($filepath) {
        try {
            return exif_read_data($filepath);
        } catch (Throwable $throwable) {
            return false;
        }
    }

    /**
     * Early CBImage specs did not have a className so this special function is
     * designed to ensure a spec does have a className and then upgrade the
     * spec.
     *
     * @param model $spec
     *
     * @return model
     */
    static function fixAndUpgrade(stdClass $spec): stdClass {
        if (empty($spec->className)) {
            $spec = CBModel::clone($spec);
            $spec->className = 'CBImage';
        }

        return CBModel::upgrade($spec);
    }

    /**
     * Use this function instead of getimagesize() because it properly returns
     * width and height for images that are rotated via the Orientation EXIF
     * property.
     *
     * @return [int => mixed]
     */
    static function getimagesize($filepath) {
        $data = getimagesize($filepath);

        if ($data[2] == IMG_JPEG) {
            $exif = CBImage::exif_read_data($filepath);
            $orientation = empty($exif['Orientation']) ? 1 : $exif['Orientation'];

            if ($orientation == 6 || $orientation == 9) {
                $width = $data[0];      // store width
                $data[0] = $data[1];    // set width to height
                $data[1] = $width;      // set height to width
                $data[3] = "width=\"{$data[0]}\" height=\"{$data[1]}\"";
            }
        }

        return $data;
    }

    /**
     * This function is similar to the CBModel::value... functions.
     *
     * @TODO 2018.03.01
     *
     *      1. Rename to valueAsFlexpath and return ?string instead of false
     *      2. Rename $operation parameter to $filename
     *      3. Use CBImage::asFlexpath()
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
