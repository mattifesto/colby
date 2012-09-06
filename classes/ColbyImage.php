<?php

/*

This class provides support for importing images as simply as possible. The class adds maximum image support while adding very little dogma. The class only needs to be loaded if an image needs to be imported or a version needs to be created. The images created by the class don't need the class to exist in the future. The class is meant to be as small as possible while providing maximum simplicity for user uploaded and maintained images.

    --------

The image file known as the master image is either imported from an original file that's been uploaded or already exists on disk.

    --------

Master images are created by the functions:

        importFromUpload
        importFromFile

    --------

At the time of import a size can be specified for the master image that is different from the original. The application may often choose to use this size because true original images are often larger than will ever be necessary on a website. The master image will be a copy of the original imported image file if a size is not specified.

    --------

Master image filenames are the sha1 hash of the master image file:

        d8610f80bc8210bd5259bb7af1dc756ee364596c.jpg

This hash is virtually guaranteed to be distinct. The meta data used to describe user uploaded files is often frequently changed or not relevant, so meta data will be placed in an alt attribute on the image element instead of the filename.

    --------

Versions of the master image are created by the function:

        versionFromMaster

Versions are created with a required size parameter which is appended to the filename:

        d8610f80bc8210bd5259bb7af1dc756ee364596c-x200.jpg

Size parameters are specified in the following manner:

        current supported size specifications:

        x200        the maximum extent of the image is 200 pixels

        future supported size specifications:

        w200        the width of the image is 200 pixels
        h200        the height of the image is 200 pixels
        w100h150    the image is exactly 100x150 pixels
                    the image is centered, filled, and clipped if necessary

*/

class ColbyImage
{
    public static function importFromUpload(
        $name,
        $destinationSize,
        $imageDirectory)
    {
    }

    public static function importFromFile(
        $path,
        $destinationSize,
        $imageDirectory,
        $shouldDeleteFile)
    {
    }

    public static function versionFromMaster(
        $imageHash,
        $destinationSize,
        $imageDirectory)
    {
    }
}
