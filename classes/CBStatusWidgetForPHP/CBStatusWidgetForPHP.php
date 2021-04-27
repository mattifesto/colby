<?php

final class CBStatusWidgetForPHP {

    /* -- CBStatusAdminPage interfaces -- */



    /**
     * @return [<title>, <key>, <value>]
     */
    static function
    CBStatusAdminPage_data(
    ) {
        return [
            'PHP',
            'Version',
            phpversion()
        ];
    }
    /* CBStatusAdminPage_data() */

}
