<?php

final class CBStatusWidgetForWebsite {

    /**
     * @return [<title>, <key>, <value>]
     */
    static function CBStatusAdminPage_data() {
        return ['Website', 'Version', CBSiteVersionNumber];
    }
}
