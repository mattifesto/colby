<?php

Colby::useRect();

define('COLBY_IMAGE_QUALITY', 80);

/*

This class provides support for commonly required image manipulations. It's loaded by calling Colby::useImage() and only needs to be loaded if new images need to be canonicalized or resized.


# Import Functions

The 'import' functions are used to canonicalize image filenames and move files in commonly needed ways. The result of an import is always a "master image".  Import functions take an image that's "out of the system" and bring it "into the system", though it's only through file renaming and copying, so it's very lightweight.

        importUploadedImage
        importImage

-   import functions never resize an image
-   import functions always either remove or have the option of removing the source image
-   import functions always create a master image
-   the hash of a master image filename always matches the sha1 hash of the actual image file
-   master image basenames are always <hash>.{jpg|png}
-   since import functions always assign a filename to the image
    they only take a destination directory, not a destination filename


# Create Functions

The 'create' functions are used to create new images by resizing other images.

        createImageByFilling
        createImageByFitting

-   master images are usually passed in as the source image to a create function
-   create doesn't check and doesn't care if the source image is a master image
-   create functions always create the new image in the same directory as the source image
    no destination filename or path is accepted
-   create functions always have a string modifier for the images they create
-   the string modifier 'w200h300' means the image is exactly 200x300 pixels
-   the string modifier 'x200x300' means the max extent of the image fits 200x300 pixels
    usually the image will match this size in at least one dimension
    but it may be smaller in both dimensions where the source image is small
-   create functions never enlarge images
-   create image filenames are always <source-filename>-<modifier>.<source-extension>


# Common Patterns and Scenarios

-   Use 'importUploadedImage' to import an uploaded image, then use a 'create' function to create a new intermediate size, and then use 'importImage' to turn that new image into a master image. This handles a scenario where a large master image is not required so giant uploaded images are discarded.

-   Use the image functions to manipulate images and then use PHP's 'copy' and 'rename' to make custom adjustements to filenames. The ColbyImage functions are not meant to replace or prevent the use of PHP file manipulations.


# 2012.09.06 Note on Filenames

Discussions on filenames are a black hole. There are a million compelling reasons one can come up with for naming image files with certain patterns in certain contexts. They're all red herrings. The goal of this class is to process many unrelated images; with unknown present and future goals; using as little external technology as possible (for instance, databases). This forces the conclusion that it's best not to have "meaningful" image filenames because any meaning would be an external dependency, even if only conceptually. The sha1 hash is perfect because it's easily calculated, is theoretically distinct, and doesn't have any meaning whatsoever related to the content of the image.

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
     * @return string
     *  The filename of the new image.
     */
    public static function createImageByFilling(
        $sourceFilename,
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

        $pathinfo = pathinfo($sourceFilename);

        $sizeId = "w{$requestedSize[0]}h{$requestedSize[1]}";

        $destinationFilename = "{$pathinfo['dirname']}/{$pathinfo['filename']}-{$sizeId}.{$pathinfo['extension']}";

        self::saveImageResource($destinationImage, $destinationFilename);

        return $destinationFilename;
    }

    /**
     * @return string
     *  The filename of the new image.
     */
    public static function createImageByFitting(
        $sourceFilename,
        $requestedSize)
    {
        $pathinfo = pathinfo($sourceFilename);

        $sizeId = "x{$requestedSize[0]}x{$requestedSize[1]}";

        $destinationFilename = "{$pathinfo['dirname']}/{$pathinfo['filename']}-{$sizeId}.{$pathinfo['extension']}";

        $sourceSize = getimagesize($sourceFilename);

        if (   $sourceSize[0] < $requestedSize[0]
            && $sourceSize[1] < $requestedSize[1])
        {
            copy($sourceFilename, $destinationFilename);
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

        return $destinationFilename;
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

        if ($_FILES[$name]['error'] != UPLOAD_ERR_OK)
        {
            switch ($_FILES[$name]['error'])
            {
                case UPLOAD_ERR_INI_SIZE:

                    $maxSize = ini_get('upload_max_filesize');
                    $message = "The file uploaded exceeds the allowed upload size of: {$maxSize}.";

                    break;

                case UPLOAD_ERR_FORM_SIZE:

                    $maxSize = ini_get('post_max_size');
                    $message = "The file uploaded exceeds the allowed post upload size of: {$maxSize}.";

                    break;

                case UPLOAD_ERR_PARTIAL:
                case UPLOAD_ERR_NO_FILE:
                case UPLOAD_ERR_NO_TMP_DIR:
                case UPLOAD_ERR_CANT_WRITE:
                case UPLOAD_ERR_EXTENSION:
                default:

                    $message = "File upload error code: {$_FILES[$name]['error']}";
            }

            throw new RuntimeException($message);
        }

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
        $destinationDirectory,
        $shouldDeleteSourceFile = false)
    {
        $imageHash = hash_file('sha1', $sourceFilename);

        $destinationExtension = self::canonicalizedExtensionFromFilename($sourceFilename);

        $destinationFilename = "{$destinationDirectory}/{$imageHash}.{$destinationExtension}";

        if ($shouldDeleteSourceFile)
        {
            rename($sourceFilename, $destinationFilename);
        }
        else
        {
            copy($sourceFilename, $destinationFilename);
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
     * @return void
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
