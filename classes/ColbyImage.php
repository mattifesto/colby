<?php

/*

This class provides support for importing images as simply as possible. The class adds maximum image support while adding very little dogma. The class only needs to be loaded if an image needs to be imported or a version needs to be created. The images created by the class don't need the class to exist in the future. The class is meant to be as small as possible while providing maximum simplicity for user uploaded and maintained images.

    --------

The image file known as the master image is either imported from an original file that's been uploaded or already exists on disk.

    --------

Master images are created by the functions:

        importUploadedImage
        importImage

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

# 2012.09.06 Note on Filenames

Discussions on filenames are a black hole. There are a million compelling reasons one can come up with for naming image files with certain patterns in certain contexts. They're all red herrings. The goal of this class is to process many unrelated images; with unknown present and future goals; using as little external technology as possible (for instance, databases). This forces the conclusion that it's best not to have "meaningful" image filenames because any meaning would be an external dependency, even if only conceptually. The sha1 hash is perfect because it's easily calculated and doesn't have any meaning whatsoever related to the content of the image.

If you find yourself in a discussion about how to name image files, end it as fast as you can. All your worries will be gone. Image metadata is important, it just shouldn't be part of the image filename.

*/

class ColbyImage
{
    //
    // This function canonicalizes image file extensions
    // and also determines supported image types.
    //
    // returns: the canonicalized extension.
    //
    public static function /* string */ canonicalizedExtensionFromFilename($filename)
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if ($extension === 'jpg' || $extension = 'jpeg')
        {
            return 'jpg';
        }
        else if ($extension === 'png')
        {
            return 'png';
        }
        else
        {
            throw new RuntimeException("Unsupported image format: {$filename}");
        }
    }

    //
    // This function creates an independent image of the specified size.
    // This file is guaranteed not to be needed by anyone other than the caller.
    //
    // Note: Deleting the source file only actually happens in this function.
    //
    // returns: the filename of the independent file
    //          this file will need to be either used or deleted by the caller
    //
    public static function /* string */ createIndependentImage(
        $sourceFilename,
        $destinationSize,
        $destinationDirectory,
        $shouldDeleteSourceFile)
    {
        $temporaryFilename = $destinationDirectory .
            '/tmp-' .
            hash('sha1', $sourceFilename . rand());

        if (false /* needs resize */)
        {
        }
        else if ($shouldDeleteSourceFile)
        {
            rename($sourceFilename, $temporaryFilename);
        }
        else
        {
            copy($sourceFilename, $temporaryFilename);
        }

        return $temporaryFilename;
    }

    //
    // the caller should pass in the master image as the $sourceFilename
    // this is not strictly checked, but it would be odd to do otherwise
    //
    // the new image will be created in the same directory as the $sourceFilename
    // and will have the same basename with the size specification appended
    //
    //     abcdef.jpg
    //     abcdef-x200.jpg
    //
    // returns: the basename of the new image
    //
    public static function /* string */ createResizedImage(
        $sourceFilename,
        $destinationSize)
    {
    }

    //
    // returns: the basename of the imported image
    //
    public static function /* string */ importUploadedImage(
        $name,
        $destinationSize,
        $destinationDirectory)
    {
    }

    //
    // returns: the basename of the imported image
    //
    public static function /* string */ importImage(
        $sourceFilename,
        $destinationSize,
        $destinationDirectory,
        $shouldDeleteSourceFile = false)
    {
        $temporaryFilename = self::createIndependentImage(
            $sourceFilename,
            $destinationSize,
            $destinationDirectory,
            $shouldDeleteSourceFile);

        $imageHash = hash_file('sha1', $temporaryFilename);

        $destinationExtension = self::canonicalizedExtensionFromFilename($sourceFilename);

        $destinationFilename = "{$destinationDirectory}/{$imageHash}.{$destinationExtension}";

        if (!rename($temporaryFilename, $destinationFilename))
        {
            throw new RuntimeException("Unable to move {$temporaryFilename} to {$destinationFilename}");
        }

        return basename($destinationFilename);
    }
}
