<?php

final class CBStatusWidgetForGD {

    /* -- CBStatusAdminPage interfaces -- */



    /**
     * @return [<title>, <key>, <value>]
     */
    static function
    CBStatusAdminPage_data(
    ) {
        try {
            if (extension_loaded('gd')) {
                $info = gd_info();

                $gd_version = $info['GD Version'];
            } else {
                $gd_version = 'not loaded';
            }
        } catch (Throwable $throwable) {
            $gd_version = 'error';
        }

        return [
            'GD',
            'Version',
            $gd_version
        ];
    }
    /* CBStatusAdminPage_data() */

}
