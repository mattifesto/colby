<?php

class ColbyImageUploader
{
    /**
     * `$info` will contain the result of a call to `getimagesize` for the
     * uploaded image file.
     */
    private $info = null;

    /**
     * `$name` will be set to the string of the index for the `$_FILES` for
     * the uploaded image.
     */
    private $name = null;

    /**
     * By making the constructor private we can force creation using a static
     * method with a clearer intent.
     */
    private function __construct()
    {
    }

    /**
     * @return ColbyImageUploader
     *  An uploader that will be used to verify and move an uploaded image.
     */
    public static function uploaderForName($name)
    {
        $uploader = new ColbyImageUploader();

        $uploader->name = $name;

        $uploader->verifyUploadedFile();

        $uploader->info = getimagesize($_FILES[$name]['tmp_name']);

        if (!$uploader->info)
        {
            throw new RuntimeException("The file uploaded for the field \"{$name}\" is not a valid image file.");
        }

        return $uploader;
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
        return image_type_to_extension($this->info[2]);
    }

    /**
     * Moves the file from the system uploads directory to the filename
     * provided.
     *
     * @return void
     */
    public function moveToFilename($filename)
    {
        move_uploaded_file($_FILES[$this->name]['tmp_name'], $filename);
    }

    /**
     * @return int
     */
    public function sizeX()
    {
        return $this->info[0];
    }

    /**
     * @return int
     */
    public function sizeY()
    {
        return $this->info[1];
    }

    /**
     * Detects any errors that occurred when the file was uploaded to the
     * server.
     */
    private function verifyUploadedFile()
    {
        if ($_FILES[$this->name]['error'] != UPLOAD_ERR_OK)
        {
            switch ($_FILES[$this->name]['error'])
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
    }
}
