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
    public static function flexpath(stdClass $image, $filename = null, $flexdir = null) {
        if (empty($filename)) {
            $filename = empty($image->filename) ? $image->base : $image->filename;
        }

        $basename = "{$filename}.{$image->extension}";

        return CBDataStore::flexpath($image->ID, $basename, $flexdir);
    }

    /**
     * @param stdClass $spec
     *
     * @return stdClass
     */
    public static function specToModel(stdClass $spec) {
        return (object)[
            'className' => __CLASS__,
            'extension' => $spec->extension,
            'filename' => empty($spec->filename) ? $spec->base : $spec->filename,
            'height' => $spec->height,
            'ID' => $spec->ID,
            'width' => $spec->width,
        ];
    }
}
