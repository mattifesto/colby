<?php

final class
CBStatusWidgetForLinux {

    /* -- CBStatusAdminPage interfaces -- */



    /**
     * @return [<title>, <key>, <value>]
     */
    static function
    CBStatusAdminPage_data(
    ) {
        $linuxVersion = 'unknown';

        try {
            $result = exec(
                'lsb_release -sd 2>&1',
                $output,
                $resultCode
            );

            if (
                $result !== false &&
                count($output) === 1 &&
                $resultCode === 0
            ) {
                $linuxVersion = $output[0];
            }
        } catch (
            Throwable $throwable
        ) {
            $linuxVersion = 'error';
        }

        return [
            'Linux',
            'Version',
            $linuxVersion
        ];
    }
    /* CBStatusAdminPage_data() */

}
