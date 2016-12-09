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

    /**
     * @param string $URI
     *
     * @return stdClass|null
     *
     *  Example: https://yaycomputer.com/data/58/52/adab0f513df82783386e121dac276bb5c9d6/original.jpeg
     *
     *  returns: {
     *      extension: "jpeg",
     *      filename: "original",
     *      ID: "5852adab0f513df82783386e121dac276bb5c9d6",
     *  }
     *
     */
    static function URIToImage($URI) {
        $pattern = '%/data/([0-9a-f]{2})/([0-9a-f]{2})/([0-9a-f]{36})/([^/]+)$%';

        if (preg_match($pattern, $URI, $matches)) {
            $basename = $matches[4];
            $pathinfo = pathinfo($basename);
            return (object)[
                'extension' => $pathinfo['extension'],
                'filename' => $pathinfo['filename'],
                'ID' => "{$matches[1]}{$matches[2]}{$matches[3]}",
            ];
        } else {
            return null;
        }
    }
}
