<?php

final class CBStatusWidgetForMySQL {

    /**
     * @return [<title>, <key>, <value>]
     */
    static function CBStatusAdminPage_data() {
        return ['MySQL', 'Version', CBDB::SQLToValue('SELECT @@version')];
    }
}
