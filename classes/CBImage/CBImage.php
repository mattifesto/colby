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
    /* asFlexpath() */


    /* -- CBHTMLOutput interfaces -- -- -- -- -- */

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v480.js', cbsysurl()),
        ];
    }


    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return [
            'CBDataStore',
            'CBModel',
        ];
    }


    /* -- CBModel interfaces -- -- -- -- -- */

    /**
     * This model is validated more than most models because all of the
     * properties are required for the model to be valid.
     *
     * @param object $spec
     *
     *      {
     *          extension: string
     *          filename: string
     *          height: int
     *          ID: string
     *          width: int
     *      }
     *
     * @return object
     *
     *      @NOTE 2019_08_14
     *
     *          This function is coded properly to throw exceptions when
     *          necessary. However, this will break many existing scenarios that
     *          rely on the old, incorrect behavior.
     *
     *          For now, we log the exceptions and return null instead of
     *          throwing them.
     */
    static function CBModel_build(stdClass $spec): ?stdClass {
        $extension = CBModel::value($spec, 'extension');

        if (empty($extension)) {
            CBErrorHandler::report(
                CBException::createModelIssueException(
                    'This spec can\'t be built because it has an invalid ' .
                    '"extension" property value.',
                    $spec,
                    'c2bcc7c228c1433577f9b4b3c7ea4e2702c7b1d5'
                )
            );

            return null;
        }

        $filename = CBModel::value($spec, 'filename');

        if (empty($filename)) {
            CBErrorHandler::report(
                CBException::createModelIssueException(
                    'This spec can\'t be built because it has an invalid ' .
                    '"filename" property value.',
                    $spec,
                    '2bc70fc87dd47cb4707395b4af6225b5cf2f0acd'
                )
            );

            return null;
        }

        $height = CBModel::valueAsInt($spec, 'height') ?? 0;

        if ($height < 1) {
            CBErrorHandler::report(
                CBException::createModelIssueException(
                    'This spec can\'t be built because it has an invalid ' .
                    '"height" property value.',
                    $spec,
                    'c6b95f62d68542095335ef2175689dc1cb4f2b90'
                )
            );

            return null;
        }

        $ID = CBModel::valueAsID($spec, 'ID');

        if (empty($ID)) {
            CBErrorHandler::report(
                CBException::createModelIssueException(
                    'This spec can\'t be built because it has an invalid ' .
                    '"ID" property value.',
                    $spec,
                    'ed759d15d41064ae2a3659639298383e6c429b9c'
                )
            );

            return null;
        }

        $width = CBModel::valueAsInt($spec, 'width') ?? 0;

        if ($width < 1) {
            CBErrorHandler::report(
                CBException::createModelIssueException(
                    'This spec can\'t be built because it has an invalid ' .
                    '"width" property value.',
                    $spec,
                    'c2bedac1eb80124931939a28d82b60e570658c5d'
                )
            );

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
    /* CBModel_build() */


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

        if (!CBID::valueIsCBID($ID)) {
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
