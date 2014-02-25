<?php

class ColbyImageResizer
{
    /**
     * `$filename` will contain the filename of the image to be resized.
     */
    private $filename = null;

    /**
     * `$inputResource` holds the image data of the input image.
     */
    private $inputResource = null;

    /**
     * `$inputSize` will contain the input image size obtained from a call to
     * `getimagesize` for the input image file.
     */
    private $inputSize = null;

    /**
     * `$inputType` will contain the input image type obtained from
     * a call to `getimagesize` for the input image file.
     */
    private $inputType = null;

    /**
     * `$outputResource` holds the image data of the resized image.
     */
    private $outputResource = null;

    /**
     * `$outputSize` will contain the current projected size of the output
     * image.
     */
    private $outputSize = null;

    /**
     * `$sourceRect` will contain the source rect to copy from the input
     * image into the output image.
     */
    private $sourceRect = null;


    /**
     * By making the constructor private we can force creation using a static
     * method with a clearer intent.
     */
    private function __construct()
    {
    }

    /**
     * @return ColbyImageResizer
     *  A resizer that will be used to create new resized versions of an
     *  existing image file.
     */
    public static function resizerForFilename($filename)
    {
        $resizer = new ColbyImageResizer();

        $resizer->filename = $filename;

        $imageSizeInfo = getimagesize($filename);

        if (!$imageSizeInfo)
        {
            throw new RuntimeException("The file \"{$filename}\" is not a valid image file.");
        }

        $resizer->inputSize = new ColbySize($imageSizeInfo[0], $imageSizeInfo[1]);

        $resizer->inputType = $imageSizeInfo[2];

        $resizer->openOriginalImageFile();

        $resizer->reset();

        return $resizer;
    }

    /**
     * @return string
     *  The canonical filename extension for the image type that has been
     *  uploaded including the dot.
     *
     *  Example: '.jpeg'
     */
    public function canonicalExtension()
    {
        return image_type_to_extension($this->inputType);
    }

    /**
     * @return void
     */
    public function cropFromCenterToWidth($newWidth)
    {
        /**
         * If the new width is less than the current width, change the output
         * size.
         */

        $oldWidth = $this->outputSize->width();

        if ($oldWidth <= $newWidth)
        {
            return;
        }

        $this->outputSize = new ColbySize($newWidth, $this->outputSize->height());

        /**
         * Since this is a crop and not just a resize, the source rectangle for
         * the input image also must change.
         */

        $oldSourceRectWidth = $this->sourceRect->size()->width();
        $newSourceRectWidth = $oldSourceRectWidth * ($newWidth / $oldWidth);

        $oldSourceRectX = $this->sourceRect->origin()->x();
        $newSourceRectX = $oldSourceRectX + (($oldSourceRectWidth - $newSourceRectWidth) / 2);

        $this->sourceRect = new ColbyRect($newSourceRectX, $this->sourceRect->origin()->y(),
                                          $newSourceRectWidth, $this->sourceRect->size()->height());

        $this->outputResource = null;
    }

    /**
     * @return void
     */
    public function cropFromCenterToHeight($newHeight)
    {
        /**
         * If the new height is less than the current height, change the output
         * size.
         */

        $oldHeight = $this->outputSize->height();

        if ($oldHeight <= $newHeight)
        {
            return;
        }

        $this->outputSize = new ColbySize($this->outputSize->width(), $newHeight);

        /**
         * Since this is a crop and not just a resize, the source rectangle for
         * the input image also must change.
         */

        $oldSourceRectHeight = $this->sourceRect->size()->height();
        $newSourceRectHeight = $oldSourceRectHeight * ($newHeight / $oldHeight);

        $oldSourceRectY = $this->sourceRect->origin()->y();
        $newSourceRectY = $oldSourceRectY + (($oldSourceRectHeight - $newSourceRectHeight) / 2);

        $this->sourceRect = new ColbyRect($this->sourceRect->origin()->x(), $newSourceRectY,
                                          $this->sourceRect->size()->width(), $newSourceRectHeight);

        $this->outputResource = null;
    }

    /**
     * Opens the original file and gets the image data.
     *
     * @return void
     */
    private function openOriginalImageFile()
    {
        switch ($this->inputType)
        {
            case IMAGETYPE_GIF:

                $this->inputResource = imagecreatefromgif($this->filename);

                break;

            case IMAGETYPE_JPEG:

                $this->inputResource = imagecreatefromjpeg($this->filename);

                break;

            case IMAGETYPE_PNG:

                $this->inputResource = imagecreatefrompng($this->filename);

                break;

            default:

                throw new RuntimeException("The image type of the file \"{$this->filename}\" is not supported.");

                break;
        }
    }

    /**
     * @return void
     */
    public function reduceHeightTo($newHeight)
    {
        if ($this->outputSize->height() <= $newHeight)
        {
            return;
        }

        $newWidth = $newHeight * ($this->inputSize->width() / $this->inputSize->height());

        $this->outputSize = new ColbySize($newWidth, $newHeight);

        $this->outputResource = null;
    }

    /**
     * @return void
     */
    public function reduceLongEdgeTo($length)
    {
        if ($this->outputSize->width() > $this->outputSize->height())
        {
            $this->reduceWidthTo($length);
        }
        else
        {
            $this->reduceHeightTo($length);
        }
    }
    /**
     * @return void
     */
    public function reduceShortEdgeTo($length)
    {
        if ($this->outputSize->width() < $this->outputSize->height())
        {
            $this->reduceWidthTo($length);
        }
        else
        {
            $this->reduceHeightTo($length);
        }
    }

    /**
     * @return void
     */
    public function reduceWidthTo($newWidth)
    {
        if ($this->outputSize->width() <= $newWidth)
        {
            return;
        }

        $newHeight = $newWidth * ($this->inputSize->height() / $this->inputSize->width());

        $this->outputSize = new ColbySize($newWidth, $newHeight);

        $this->outputResource = null;
    }

    /**
     * @return void
     */
    public function reset()
    {
        $this->sourceRect = new ColbyRect(0, 0, $this->inputSize->width(), $this->inputSize->height());

        $this->outputSize = $this->inputSize;

        $this->outputResource = null;
    }

    /**
     * Saves the most recent size requested to the specified filename.
     *
     * @return void
     */
    public function saveToFilename($filename)
    {
        if (!$this->outputResource)
        {
            /**
             * If the input size is same as the output size it means that no
             * changes have been made and the output can just be a file copy
             * of the input.
             */

            if ($this->inputSize == $this->outputSize)
            {
                copy($this->filename, $filename);

                return;
            }

            $destinationRect = new ColbyRect(0, 0, $this->outputSize->width(), $this->outputSize->height());
            $sourceRect = $this->sourceRect;

            $this->outputResource = imagecreatetruecolor($destinationRect->size()->width(),
                                                         $destinationRect->size()->height());

            imagecopyresampled(
                $this->outputResource,
                $this->inputResource,
                $destinationRect->origin()->x(), $destinationRect->origin()->y(),
                $sourceRect->origin()->x(), $sourceRect->origin()->y(),
                $destinationRect->size()->width(), $destinationRect->size()->height(),
                $sourceRect->size()->width(), $sourceRect->size()->height());
        }

        switch ($this->inputType)
        {
            case IMAGETYPE_GIF:

                imagegif($this->outputResource, $filename);

                break;

            case IMAGETYPE_JPEG:

                imagejpeg($this->outputResource, $filename, /* quality: */ 90);

                break;

            case IMAGETYPE_PNG:

                imagepng($this->outputResource, $filename);

                break;

            default:

                throw new RuntimeException("The image type of the file \"{$this->filename}\" is not supported.");

                break;
        }
    }
}
