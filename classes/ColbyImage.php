<?php

Colby::useRect();

define('COLBY_IMAGE_QUALITY', 80);

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
    /**
     * Canonicalizes image filename extensions.
     *
     * @return string
     *   The canonicalized extension.
     */
    public static function canonicalizedExtensionFromFilename($filename)
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
            throw new RuntimeException("Unsupported image filename extension: {$filename}");
        }
    }

    /**
     *
     */
    public static function createImageByFilling(
        $sourceFilename,
        $destinationFilename,
        $requestedSize)
    {
        $sourceSize = getimagesize($sourceFilename);

        if (   $sourceSize[0] < $requestedSize[0]
            || $sourceSize[1] < $requestedSize[1])
        {
            // The whole point of filling is to fill the requested size
            // completely. If the source isn't big enough to do that, throw
            // an exception. The caller should never do that on purpose.

            throw new RuntimeException("The size '{$sourceSize[0]}x{$sourceSize[1]}' of '{$sourceFilename}' is too small to fill the requested size.");
        }

        $sourceRect = ColbyRect::sourceRectToFillRequestedSize($sourceSize, $requestedSize);

        $sourceImage = self::openImageResource($sourceFilename);

        $destinationImage = imagecreatetruecolor($requestedSize[0], $requestedSize[1]);

        imagecopyresampled(
            $destinationImage,
            $sourceImage,
            0 /* destination x */, 0 /* destination y */,
            $sourceRect->x, $sourceRect->y,
            $requestedSize[0] /* destination width */, $requestedSize[1] /* destination height */,
            $sourceRect->width, $sourceRect->height);

        self::saveImageResource($destinationImage, $destinationFilename);
    }

    /**
     *
     */
    public static function createImageByFitting(
        $sourceFilename,
        $destinationFilename,
        $requestedSize)
    {
        $sourceSize = getimagesize($sourceFilename);

        if (   $sourceSize[0] < $requestedSize[0]
            && $sourceSize[1] < $requestedSize[1])
        {
            // copy
        }

        $destinationRect = ColbyRect::destinationRectToFitRequestedSize($sourceSize, $requestedSize);

        $sourceImage = self::openImageResource($sourceFilename);

        $destinationImage = imagecreatetruecolor($destinationRect->width, $destinationRect->height);

        imagecopyresampled(
            $destinationImage,
            $sourceImage,
            $destinationRect->x, $destinationRect->y,
            0 /* source x */, 0 /* source y */,
            $destinationRect->width, $destinationRect->height,
            $sourceSize[0] /* source width */, $sourceSize[1] /* source height */);

        self::saveImageResource($destinationImage, $destinationFilename);
    }

    /**
     * Creates a new independent image that is free to be moved or deleted.
     *
     * This function creates an independent image of the specified size.
     * This file is guaranteed not to be needed by anyone other than the caller.
     *
     * Note: Deleting the source file only actually happens in this function.
     *
     * @return string
     *   The filename of the independent file.
     *   This file needs to be either used or deleted by the caller.
     */
    public static function createIndependentImage(
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

    /**
     * Imports an uploaded image.
     *
     * This method does not do any resizing. If the image needs to be further
     * processed to create a new master image at a smaller size, use
     * ColbyImage::importImage after calling this function.
     *
     * If the caller determines the image is unacceptable, the caller will
     * need to delete it.
     *
     * @return string
     *   The filename of the imported image.
     *   This will be: <sha1 hash of file>.<canonicalized extension>
     */
    public static function importUploadedImage(
        $name,
        $destinationDirectory)
    {
        // NOTE: This code does not handle multiple file uploads for one name
        //       and will most likely fail explicitly or functionally if that
        //       is attempted. It's neither a security nor a functionality
        //       concern. It's not something the user can choose to do on their
        //       own so it shouldn't be a problem in a production environment.

        $extension = self::canonicalizedExtensionFromFilename($_FILES[$name]['name']);

        $imageHash = hash_file('sha1', $_FILES[$name]['tmp_name']);

        $filename = "{$destinationDirectory}/{$imageHash}.{$extension}";

        move_uploaded_file($_FILES[$name]['tmp_name'], $filename);

        return $filename;
    }

    /**
     * @return string
     *   The filename of the imported image.
     */
    public static function importImage(
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

        if ($shouldDeleteSourceFile)
        {
            rename($temporaryFilename, $destinationFilename);
        }
        else
        {
            copy($temporaryFilename, $destinationFilename);
        }

        return $destinationFilename;
    }

    /**
     *
     * @return resource
     */
    public static function openImageResource($filename)
    {
        $extension = self::canonicalizedExtensionFromFilename($filename);

        switch ($extension)
        {
            case 'jpg':

                $resource = imagecreatefromjpeg($filename);

                break;

            case 'png':

                $resource = imagecreatefrompng($filename);

                break;

            default:

                throw new RuntimeException("Unsupported image filename extension: {$filename}");

                break;
        }

        return $resource;
    }

    /**
     *
     */
    public static function saveImageResource($imageResource, $filename)
    {
        $extension = self::canonicalizedExtensionFromFilename($filename);

        switch ($extension)
        {
            case 'jpg':

                imagejpeg($imageResource, $filename, COLBY_IMAGE_QUALITY);

                break;

            case 'png':

                imagepng($imageResource, $filename);

                break;

            default:

                throw new RuntimeException("Unsupported image filename extension: {$filename}");

                break;
        }
    }
}
