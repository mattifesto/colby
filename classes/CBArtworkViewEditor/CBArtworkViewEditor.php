<?php

final class CBArtworkViewEditor {

    /**
     * @return [string]
     */
    static function requiredClassNames() {
        return ['CBUI', 'CBUIImageChooser', 'CBUISelector', 'CBUIStringEditor'];
    }

    /**
     * @return [string]
     */
    static function requiredJavaScriptURls() {
        return [Colby::flexpath(__CLASS__, 'v360.js', cbsysurl())];
    }
}
