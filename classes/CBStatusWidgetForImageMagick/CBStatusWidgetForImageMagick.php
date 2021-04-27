<?php

final class CBStatusWidgetForImageMagick {

    /* -- CBStatusAdminPage interfaces -- */



    /**
     * @return [<title>, <key>, <value>]
     */
    static function
    CBStatusAdminPage_data(
    ) {
        try {
            if (extension_loaded('imagick')) {
                $info = Imagick::getVersion();

                $imagick_version = $info['versionString'];
            } else {
                $imagick_version = 'not loaded';
            }
        } catch (Throwable $throwable) {
            $imagick_version = 'error';
        }

        return [
            'ImageMagick',
            'Version',
            $imagick_version
        ];
    }
    /* CBStatusAdminPage_data() */

}
