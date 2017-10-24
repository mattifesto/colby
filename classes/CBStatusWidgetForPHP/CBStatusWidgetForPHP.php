<?php

final class CBStatusWidgetForPHP {

    /**
     * @return [<title>, <key>, <value>]
     */
    static function CBStatusAdminPage_data() {
        return ['PHP', 'Version', phpversion()];
    }
}
