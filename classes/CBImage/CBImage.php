<?php

final class CBImage {

    /**
     * @deprecated 2016.07.28
     *  Images are now generically identified with the image ID and extension.
     *  Use these values along with a filename and the CBDataStore::flexpath()
     *  function instead of this function.
     *
     * @param hex160 $image->ID
     * @param string $image->filename (used if $filename parameter is empty)
     * @param string $image->extension
     * @param string? $filename
     * @param string? $flexdir
     *
     * @return string
     */
    static function flexpath(stdClass $image, $filename = null, $flexdir = null) {
        if (empty($filename)) {
            $filename = empty($image->filename) ? $image->base : $image->filename;
        }

        $basename = "{$filename}.{$image->extension}";

        return CBDataStore::flexpath($image->ID, $basename, $flexdir);
    }

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
}
