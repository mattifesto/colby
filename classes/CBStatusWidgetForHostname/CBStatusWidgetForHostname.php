<?php

final class
CBStatusWidgetForHostname {

    /* -- CBStatusAdminPage interfaces -- */



    /**
     * @return [<title>, <key>, <value>]
     */
    static function
    CBStatusAdminPage_data(
    ) {
        $hostname = '* unknown *';

        try {
            $hostname = gethostname();

            if ($hostname === false) {
                $hostname = '* error *';
            }
        } catch (
            Throwable $throwable
        ) {
            $hostname = '* exception *';
        }

        return [
            'Hostname',
            'Hostname',
            $hostname
        ];
    }
    /* CBStatusAdminPage_data() */

}
